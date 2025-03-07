<?php

namespace Application\Core\Providers;

use App\Domain\Accounts\Users\Interfaces\UserDetailRepositoryInterface;
use App\Domain\Accounts\Users\Interfaces\UserRepositoryInterface;
use App\Domain\Financial\Entries\Consolidation\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Domain\Financial\Entries\Cults\Interfaces\CultRepositoryInterface;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Domain\Financial\Entries\Reports\Interfaces\ReportRequestsRepositoryInterface;
use App\Domain\Financial\Reviewers\Interfaces\FinancialReviewerRepositoryInterface;
use App\Domain\Financial\Settings\Interfaces\FinancialSettingsRepositoryInterface;
use App\Domain\SyncStorage\Interfaces\SyncStorageRepositoryInterface;
use App\Infrastructure\Repositories\Accounts\User\UserDetailRepository;
use App\Infrastructure\Repositories\Accounts\User\UserRepository;
use App\Infrastructure\Repositories\Financial\Entries\Automation\AutomationRepository;
use App\Infrastructure\Repositories\Financial\Entries\Consolidation\ConsolidationRepository;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use App\Infrastructure\Repositories\Financial\Entries\Indicators\AmountDevolutionEntries\AmountDevolutionEntriesRepository;
use App\Infrastructure\Repositories\Financial\Entries\Indicators\TithesMonthlyTarget\TithesMonthlyTargetEntriesRepository;
use App\Infrastructure\Repositories\Financial\Reviewer\FinancialReviewerRepository;
use Domain\CentralDomain\Churches\Church\Interfaces\ChurchRepositoryInterface;
use Domain\CentralDomain\Plans\Interfaces\PlanRepositoryInterface;
use Domain\Ecclesiastical\Divisions\Interfaces\DivisionRepositoryInterface;
use Domain\Ecclesiastical\Folders\Interfaces\SyncFoldersRepositoryInterface;
use Domain\Ecclesiastical\Groups\Interfaces\GroupRepositoryInterface;
use Domain\Financial\Entries\Indicators\AmountDevolutions\Interfaces\AmountDevolutionRepositoryInterface;
use Domain\Financial\Entries\Indicators\AmountToCompensate\Interfaces\AmountToCompensateRepositoryInterface;
use Domain\Financial\Entries\Indicators\TithesMonthlyTarget\Interfaces\TithesMonthlyTargetEntriesRepositoryInterface;
use Domain\Financial\Entries\Indicators\TotalGeneral\Interfaces\TotalGeneralRepositoryInterface;
use Domain\Financial\Receipts\Entries\ReadingError\Interfaces\ReadingErrorReceiptRepositoryInterface;
use Domain\Financial\SyncStorage\Interfaces\SyncStorageFolderRepositoryInterface;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Interfaces\BaseRepositoryInterface;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\CentralDomain\PlanRepository;
use Infrastructure\Repositories\Church\ChurchRepository;
use Infrastructure\Repositories\Ecclesiastical\Divisions\DivisionRepository;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Infrastructure\Repositories\Ecclesiastical\SyncFolders\SyncFoldersRepository;
use Infrastructure\Repositories\Financial\Entries\Cults\CultRepository;
use Infrastructure\Repositories\Financial\Entries\Indicators\AmountToCompensate\AmountToCompensateRepository;
use Infrastructure\Repositories\Financial\Entries\Indicators\TotalGeneral\TotalGeneralRepository;
use Infrastructure\Repositories\Financial\Entries\Reports\ReportRequestsRepository;
use Infrastructure\Repositories\Financial\Settings\FinancialSettingsRepository;
use Infrastructure\Repositories\Financial\SyncStorageFolders\SyncStorageFoldersRepository;
use Infrastructure\Repositories\Member\MemberRepository;
use Infrastructure\Repositories\Mobile\SyncStorage\SyncStorageRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
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
        $this->app->bind(SyncFoldersRepositoryInterface::class, SyncFoldersRepository::class);
        $this->app->bind(ReadingErrorReceiptRepositoryInterface::class, AutomationRepository::class);
        $this->app->bind(GroupRepositoryInterface::class, GroupsRepository::class);
        $this->app->bind(CultRepositoryInterface::class, CultRepository::class);
        $this->app->bind(ReportRequestsRepositoryInterface::class, ReportRequestsRepository::class);
        $this->app->bind(PlanRepositoryInterface::class, PlanRepository::class);
        $this->app->bind(PlanRepositoryInterface::class, PlanRepository::class);
        $this->app->bind(SyncStorageRepositoryInterface::class, SyncStorageRepository::class);

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
