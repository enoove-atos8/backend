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

    // Entries/Exits columns
    private const DATE_TRANSACTION_COMPENSATION_COLUMN = 'date_transaction_compensation';

    private const AMOUNT_COLUMN = 'amount';

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
            ->where(self::MEMBER_TYPE_COLUMN, self::MEMBER_TYPE_MEMBER)
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

    public function getEntriesByMonth(int $months): Collection
    {
        // Buscar apenas dízimos (entry_type = 'tithe') agrupados por mês
        return DB::table(self::ENTRIES_TABLE)
            ->selectRaw('DATE_FORMAT('.self::DATE_TRANSACTION_COMPENSATION_COLUMN.", '%Y-%m') as month, SUM(".self::AMOUNT_COLUMN.') as total')
            ->where(self::ENTRY_TYPE_COLUMN, self::ENTRY_TYPE_TITHE)
            ->where(self::DELETED_COLUMN, 0)
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit($months)
            ->get();
    }

    public function getExitsByMonth(int $months): Collection
    {
        // Buscar apenas saídas reais (payment, ministerial_transfer, contributions)
        return DB::table(self::EXITS_TABLE)
            ->selectRaw('DATE_FORMAT('.self::DATE_TRANSACTION_COMPENSATION_COLUMN.", '%Y-%m') as month, SUM(".self::AMOUNT_COLUMN.') as total')
            ->whereIn(self::EXIT_TYPE_COLUMN, self::REAL_EXIT_TYPES)
            ->where(self::DELETED_COLUMN, 0)
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit($months)
            ->get();
    }
}
