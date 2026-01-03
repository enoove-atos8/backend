<?php

namespace App\Infrastructure\Repositories\Dashboard;

use App\Domain\Dashboard\Interfaces\DashboardRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardRepository implements DashboardRepositoryInterface
{
    // Tables
    private const MEMBERS_TABLE = 'members';

    private const ENTRIES_TABLE = 'entries';

    private const EXITS_TABLE = 'exits';

    private const CONSOLIDATION_TABLE = 'consolidation_entries';

    private const CARDS_INVOICES_TABLE = 'cards_invoices';

    private const CARDS_TABLE = 'cards';

    private const ACTIVE_COLUMN = 'active';

    // Members columns
    private const ACTIVATED_COLUMN = 'activated';

    private const DELETED_COLUMN = 'deleted';

    private const MEMBER_TYPE_COLUMN = 'member_type';

    private const MEMBER_TYPE_MEMBER = 'member';

    private const MEMBER_TYPE_CONGREGATE = 'congregate';

    private const ACTIVE_MEMBER_TYPES = [self::MEMBER_TYPE_MEMBER, self::MEMBER_TYPE_CONGREGATE];

    // Entries/Exits columns
    private const DATE_TRANSACTION_COMPENSATION_COLUMN = 'date_transaction_compensation';

    private const AMOUNT_COLUMN = 'amount';

    private const MEMBER_ID_COLUMN = 'member_id';

    // Consolidation columns
    private const DATE_COLUMN = 'date';

    private const TOTAL_AMOUNT_COLUMN = 'total_amount';

    private const CONSOLIDATED_COLUMN = 'consolidated';

    // Entry types
    private const ENTRY_TYPE_COLUMN = 'entry_type';

    private const ENTRY_TYPE_TITHE = 'tithe';

    // Exit types
    private const EXIT_TYPE_COLUMN = 'exit_type';

    private const REAL_EXIT_TYPES = ['payments', 'ministerial_transfer', 'contributions'];

    // Invoice columns
    private const STATUS_COLUMN = 'status';

    private const INVOICE_STATUS_OPEN = 'open';

    private const INVOICE_STATUS_PAID = 'paid';

    public function getActiveMembersCount(): int
    {
        return DB::table(self::MEMBERS_TABLE)
            ->whereIn(self::MEMBER_TYPE_COLUMN, self::ACTIVE_MEMBER_TYPES)
            ->where(self::ACTIVATED_COLUMN, 1)
            ->count();
    }

    public function getTotalTithes(string $month): float
    {
        $result = DB::table(self::ENTRIES_TABLE)
            ->where(self::ENTRY_TYPE_COLUMN, self::ENTRY_TYPE_TITHE)
            ->where(self::DATE_TRANSACTION_COMPENSATION_COLUMN, 'LIKE', $month.'%')
            ->where(self::DELETED_COLUMN, 0)
            ->sum(self::AMOUNT_COLUMN);

        return (float) ($result ?? 0);
    }

    public function getTotalOpenInvoices(): float
    {
        $result = DB::table(self::CARDS_INVOICES_TABLE.' as ci')
            ->join(self::CARDS_TABLE.' as c', 'c.id', '=', 'ci.card_id')
            ->where('c.'.self::ACTIVE_COLUMN, 1)
            ->where('c.'.self::DELETED_COLUMN, 0)
            ->where('ci.'.self::STATUS_COLUMN, '!=', self::INVOICE_STATUS_PAID)
            ->where('ci.'.self::DELETED_COLUMN, 0)
            ->sum('ci.'.self::AMOUNT_COLUMN);

        return (float) ($result ?? 0);
    }

    public function getTotalRealExits(string $month): float
    {
        $result = DB::table(self::EXITS_TABLE)
            ->whereIn(self::EXIT_TYPE_COLUMN, self::REAL_EXIT_TYPES)
            ->where(self::DATE_TRANSACTION_COMPENSATION_COLUMN, 'LIKE', $month.'%')
            ->where(self::DELETED_COLUMN, 0)
            ->sum(self::AMOUNT_COLUMN);

        return (float) ($result ?? 0);
    }

    public function getConsolidatedMonths(int $months): Collection
    {
        return DB::table(self::CONSOLIDATION_TABLE)
            ->select(self::DATE_COLUMN.' as month')
            ->where(self::CONSOLIDATED_COLUMN, 1)
            ->orderBy(self::DATE_COLUMN, 'desc')
            ->limit($months)
            ->pluck('month');
    }

    public function getEntriesByMonth(string $startMonth, string $endMonth): Collection
    {
        // Buscar apenas dízimos (entry_type = 'tithe') agrupados por mês no período especificado
        return DB::table(self::ENTRIES_TABLE)
            ->selectRaw('DATE_FORMAT('.self::DATE_TRANSACTION_COMPENSATION_COLUMN.", '%Y-%m') as month, SUM(".self::AMOUNT_COLUMN.') as total')
            ->where(self::ENTRY_TYPE_COLUMN, self::ENTRY_TYPE_TITHE)
            ->where(self::DELETED_COLUMN, 0)
            ->where(self::DATE_TRANSACTION_COMPENSATION_COLUMN, '>=', $startMonth.'-01')
            ->where(self::DATE_TRANSACTION_COMPENSATION_COLUMN, '<=', $endMonth.'-31')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
    }

    public function getExitsByMonth(string $startMonth, string $endMonth): Collection
    {
        // Buscar apenas saídas reais (payment, ministerial_transfer, contributions) no período especificado
        return DB::table(self::EXITS_TABLE)
            ->selectRaw('DATE_FORMAT('.self::DATE_TRANSACTION_COMPENSATION_COLUMN.", '%Y-%m') as month, SUM(".self::AMOUNT_COLUMN.') as total')
            ->whereIn(self::EXIT_TYPE_COLUMN, self::REAL_EXIT_TYPES)
            ->where(self::DELETED_COLUMN, 0)
            ->where(self::DATE_TRANSACTION_COMPENSATION_COLUMN, '>=', $startMonth.'-01')
            ->where(self::DATE_TRANSACTION_COMPENSATION_COLUMN, '<=', $endMonth.'-31')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
    }

    public function getTotalActiveMembersByMonth(string $month): int
    {
        return DB::table(self::MEMBERS_TABLE)
            ->whereIn(self::MEMBER_TYPE_COLUMN, self::ACTIVE_MEMBER_TYPES)
            ->where(self::ACTIVATED_COLUMN, 1)
            ->count();
    }

    public function getActiveTithersByMonth(string $month): int
    {
        return DB::table(self::ENTRIES_TABLE.' as e')
            ->join(self::MEMBERS_TABLE.' as m', 'e.'.self::MEMBER_ID_COLUMN, '=', 'm.id')
            ->where('e.'.self::ENTRY_TYPE_COLUMN, self::ENTRY_TYPE_TITHE)
            ->where('e.'.self::DELETED_COLUMN, 0)
            ->where('e.'.self::DATE_TRANSACTION_COMPENSATION_COLUMN, 'LIKE', $month.'%')
            ->whereIn('m.'.self::MEMBER_TYPE_COLUMN, self::ACTIVE_MEMBER_TYPES)
            ->where('m.'.self::ACTIVATED_COLUMN, 1)
            ->distinct()
            ->count('e.'.self::MEMBER_ID_COLUMN);
    }

    public function getTotalContributionsByMonth(string $month): int
    {
        return DB::table(self::ENTRIES_TABLE.' as e')
            ->join(self::MEMBERS_TABLE.' as m', 'e.'.self::MEMBER_ID_COLUMN, '=', 'm.id')
            ->where('e.'.self::ENTRY_TYPE_COLUMN, self::ENTRY_TYPE_TITHE)
            ->where('e.'.self::DELETED_COLUMN, 0)
            ->where('e.'.self::DATE_TRANSACTION_COMPENSATION_COLUMN, 'LIKE', $month.'%')
            ->whereIn('m.'.self::MEMBER_TYPE_COLUMN, self::ACTIVE_MEMBER_TYPES)
            ->where('m.'.self::ACTIVATED_COLUMN, 1)
            ->count();
    }
}
