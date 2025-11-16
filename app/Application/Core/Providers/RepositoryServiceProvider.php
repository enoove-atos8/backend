<?php

namespace Application\Core\Providers;

use App\Domain\Accounts\Users\Interfaces\UserDetailRepositoryInterface;
use App\Domain\Accounts\Users\Interfaces\UserRepositoryInterface;
use App\Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountsBalancesRepositoryInterface;
use App\Domain\Financial\Entries\Consolidation\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Domain\Financial\Entries\Cults\Interfaces\CultRepositoryInterface;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardInstallmentsRepositoryInterface;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardInvoiceRepositoryInterface;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardPurchaseRepositoryInterface;
use App\Domain\Financial\Reports\Balances\Interfaces\MonthlyBalancesReportsRepositoryInterface;
use App\Domain\Financial\Reports\Entries\Interfaces\MonthlyReportsRepositoryInterface;
use App\Domain\Financial\Reports\Exits\Interfaces\MonthlyExitsReportsRepositoryInterface;
use App\Domain\Financial\Reviewers\Interfaces\FinancialReviewerRepositoryInterface;
use App\Domain\Financial\Settings\Interfaces\FinancialSettingsRepositoryInterface;
use App\Domain\SyncStorage\Interfaces\SyncStorageRepositoryInterface;
use App\Infrastructure\Repositories\Accounts\User\UserDetailRepository;
use App\Infrastructure\Repositories\Accounts\User\UserRepository;
use App\Infrastructure\Repositories\Financial\AccountsAndCards\Accounts\AccountsBalancesRepository;
use App\Infrastructure\Repositories\Financial\AccountsAndCards\Card\CardInstallmentsRepository;
use App\Infrastructure\Repositories\Financial\Entries\Consolidation\ConsolidationRepository;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use App\Infrastructure\Repositories\Financial\Entries\Indicators\AmountDevolutionEntries\AmountDevolutionEntriesRepository;
use App\Infrastructure\Repositories\Financial\Entries\Indicators\TithesMonthlyTarget\TithesMonthlyTargetEntriesRepository;
use App\Infrastructure\Repositories\Financial\Reports\Balances\MonthlyBalancesReportsRepository;
use App\Infrastructure\Repositories\Financial\Reports\Entries\MonthlyReportsRepository;
use App\Infrastructure\Repositories\Financial\Reports\Exits\MonthlyExitsReportsRepository;
use App\Infrastructure\Repositories\Financial\Reviewer\FinancialReviewerRepository;
use App\Infrastructure\Repositories\Secretary\Membership\MemberRepository;
use Domain\CentralDomain\Churches\Church\Interfaces\ChurchRepositoryInterface;
use Domain\CentralDomain\Plans\Interfaces\PlanRepositoryInterface;
use Domain\Ecclesiastical\Divisions\Interfaces\DivisionRepositoryInterface;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountFileRepositoryInterface;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountMovementsRepositoryInterface;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountRepositoryInterface;
use Domain\Financial\AccountsAndCards\Cards\Interfaces\CardRepositoryInterface;
use Domain\Financial\Entries\Indicators\AmountDevolutions\Interfaces\AmountDevolutionRepositoryInterface;
use Domain\Financial\Entries\Indicators\AmountToCompensate\Interfaces\AmountToCompensateRepositoryInterface;
use Domain\Financial\Entries\Indicators\TithesMonthlyTarget\Interfaces\TithesMonthlyTargetEntriesRepositoryInterface;
use Domain\Financial\Entries\Indicators\TotalGeneral\Interfaces\TotalGeneralRepositoryInterface;
use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Domain\Financial\Exits\Payments\Categories\Interfaces\PaymentCategoryRepositoryInterface;
use Domain\Financial\Exits\Payments\Items\Interfaces\PaymentItemRepositoryInterface;
use Domain\Financial\Movements\Interfaces\MovementRepositoryInterface;
use Domain\Financial\ReceiptProcessing\Interfaces\ReceiptProcessingRepositoryInterface;
use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Interfaces\BaseRepositoryInterface;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\CentralDomain\PlanRepository;
use Infrastructure\Repositories\Church\ChurchRepository;
use Infrastructure\Repositories\Ecclesiastical\Divisions\DivisionRepository;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Infrastructure\Repositories\Financial\AccountsAndCards\Accounts\AccountFilesRepository;
use Infrastructure\Repositories\Financial\AccountsAndCards\Accounts\AccountMovementsRepository;
use Infrastructure\Repositories\Financial\AccountsAndCards\Accounts\AccountRepository;
use Infrastructure\Repositories\Financial\AccountsAndCards\Card\CardInvoiceRepository;
use Infrastructure\Repositories\Financial\AccountsAndCards\Card\CardPurchaseRepository;
use Infrastructure\Repositories\Financial\AccountsAndCards\Card\CardRepository;
use Infrastructure\Repositories\Financial\Entries\Cults\CultRepository;
use Infrastructure\Repositories\Financial\Entries\Indicators\AmountToCompensate\AmountToCompensateRepository;
use Infrastructure\Repositories\Financial\Entries\Indicators\TotalGeneral\TotalGeneralRepository;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
use Infrastructure\Repositories\Financial\Exits\Payments\PaymentCategoryRepository;
use Infrastructure\Repositories\Financial\Exits\Payments\PaymentItemRepository;
use Infrastructure\Repositories\Financial\Movements\MovementRepository;
use Infrastructure\Repositories\Financial\ReceiptProcessing\ReceiptProcessingRepository;
use Infrastructure\Repositories\Financial\Settings\FinancialSettingsRepository;
use Infrastructure\Repositories\Mobile\SyncStorage\SyncStorageRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(BaseRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserDetailRepositoryInterface::class, UserDetailRepository::class);
        $this->app->bind(ChurchRepositoryInterface::class, ChurchRepository::class);
        $this->app->bind(EntryRepositoryInterface::class, EntryRepository::class);
        $this->app->bind(MemberRepositoryInterface::class, MemberRepository::class);
        $this->app->bind(ConsolidatedEntriesRepositoryInterface::class, ConsolidationRepository::class);
        $this->app->bind(FinancialReviewerRepositoryInterface::class, FinancialReviewerRepository::class);
        $this->app->bind(TithesMonthlyTargetEntriesRepositoryInterface::class, TithesMonthlyTargetEntriesRepository::class);
        $this->app->bind(FinancialSettingsRepositoryInterface::class, FinancialSettingsRepository::class);
        $this->app->bind(AmountToCompensateRepositoryInterface::class, AmountToCompensateRepository::class);
        $this->app->bind(AmountDevolutionRepositoryInterface::class, AmountDevolutionEntriesRepository::class);
        $this->app->bind(TotalGeneralRepositoryInterface::class, TotalGeneralRepository::class);
        $this->app->bind(DivisionRepositoryInterface::class, DivisionRepository::class);
        $this->app->bind(GroupRepositoryInterface::class, GroupsRepository::class);
        $this->app->bind(GroupRepositoryInterface::class, GroupsRepository::class);
        $this->app->bind(CultRepositoryInterface::class, CultRepository::class);
        $this->app->bind(MonthlyReportsRepositoryInterface::class, MonthlyReportsRepository::class);
        $this->app->bind(PlanRepositoryInterface::class, PlanRepository::class);
        $this->app->bind(PlanRepositoryInterface::class, PlanRepository::class);
        $this->app->bind(SyncStorageRepositoryInterface::class, SyncStorageRepository::class);
        $this->app->bind(ExitRepositoryInterface::class, ExitRepository::class);
        $this->app->bind(ReceiptProcessingRepositoryInterface::class, ReceiptProcessingRepository::class);
        $this->app->bind(PaymentCategoryRepositoryInterface::class, PaymentCategoryRepository::class);
        $this->app->bind(PaymentItemRepositoryInterface::class, PaymentItemRepository::class);
        $this->app->bind(MovementRepositoryInterface::class, MovementRepository::class);
        $this->app->bind(CardRepositoryInterface::class, CardRepository::class);
        $this->app->bind(CardInvoiceRepositoryInterface::class, CardInvoiceRepository::class);
        $this->app->bind(CardPurchaseRepositoryInterface::class, CardPurchaseRepository::class);
        $this->app->bind(CardInstallmentsRepositoryInterface::class, CardInstallmentsRepository::class);
        $this->app->bind(AccountRepositoryInterface::class, AccountRepository::class);
        $this->app->bind(AccountFileRepositoryInterface::class, AccountFilesRepository::class);
        $this->app->bind(AccountMovementsRepositoryInterface::class, AccountMovementsRepository::class);
        $this->app->bind(MonthlyExitsReportsRepositoryInterface::class, MonthlyExitsReportsRepository::class);
        $this->app->bind(MonthlyBalancesReportsRepositoryInterface::class, MonthlyBalancesReportsRepository::class);
        $this->app->bind(AccountsBalancesRepositoryInterface::class, AccountsBalancesRepository::class);

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
