<?php

namespace Application\Core\Providers;

use App\Domain\Accounts\Users\Interfaces\UserDetailRepositoryInterface;
use App\Domain\Accounts\Users\Interfaces\UserRepositoryInterface;
use App\Domain\Financial\Entries\Consolidated\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Domain\Financial\Entries\General\Interfaces\EntryRepositoryInterface;
use App\Domain\Financial\Reviewers\Interfaces\FinancialReviewerRepositoryInterface;
use App\Domain\Financial\Settings\Interfaces\FinancialSettingsRepositoryInterface;
use App\Infrastructure\Repositories\Accounts\User\UserDetailRepository;
use App\Infrastructure\Repositories\Accounts\User\UserRepository;
use App\Infrastructure\Repositories\Financial\Entries\Consolidated\ConsolidationEntriesRepository;
use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use App\Infrastructure\Repositories\Financial\Entries\Indicators\AmountDevolutionEntries\AmountDevolutionEntriesRepository;
use App\Infrastructure\Repositories\Financial\Entries\Indicators\TithesMonthlyTarget\TithesMonthlyTargetEntriesRepository;
use App\Infrastructure\Repositories\Financial\Reviewer\FinancialReviewerRepository;
use Domain\Churches\Interfaces\ChurchRepositoryInterface;
use Domain\Financial\Entries\Indicators\AmountDevolutions\Interfaces\AmountDevolutionRepositoryInterface;
use Domain\Financial\Entries\Indicators\AmountToCompensate\Interfaces\AmountToCompensateRepositoryInterface;
use Domain\Financial\Entries\Indicators\TithesMonthlyTarget\Interfaces\TithesMonthlyTargetEntriesRepositoryInterface;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Interfaces\BaseRepositoryInterface;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Church\ChurchRepository;
use Infrastructure\Repositories\Financial\Entries\Indicators\AmountToCompensate\AmountToCompensateRepository;
use Infrastructure\Repositories\Financial\Settings\FinancialSettingsRepository;
use Infrastructure\Repositories\Member\MemberRepository;

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
        $this->app->bind(ConsolidatedEntriesRepositoryInterface::class, ConsolidationEntriesRepository::class);
        $this->app->bind(FinancialReviewerRepositoryInterface::class, FinancialReviewerRepository::class);
        $this->app->bind(TithesMonthlyTargetEntriesRepositoryInterface::class, TithesMonthlyTargetEntriesRepository::class);
        $this->app->bind(FinancialSettingsRepositoryInterface::class, FinancialSettingsRepository::class);
        $this->app->bind(AmountToCompensateRepositoryInterface::class, AmountToCompensateRepository::class);
        $this->app->bind(AmountDevolutionRepositoryInterface::class, AmountDevolutionEntriesRepository::class);
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
