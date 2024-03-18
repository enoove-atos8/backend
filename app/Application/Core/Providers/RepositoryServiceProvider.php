<?php

namespace Application\Core\Providers;

use App\Domain\Financial\Entries\Consolidated\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Domain\Financial\Entries\General\Interfaces\EntryRepositoryInterface;
use App\Domain\Financial\Reviewers\Interfaces\FinancialReviewerRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Consolidated\ConsolidationEntriesRepository;
use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use App\Infrastructure\Repositories\Financial\Reviewer\FinancialReviewerRepository;
use Domain\Churches\Interfaces\ChurchRepositoryInterface;
use Domain\Financial\Entries\Indicators\MonthlyTarget\Interfaces\MonthlyTargetEntriesRepositoryInterface;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Domain\Users\Interfaces\UserDetailRepositoryInterface;
use Domain\Users\Interfaces\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Interfaces\BaseRepositoryInterface;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Church\ChurchRepository;
use Infrastructure\Repositories\Financial\Entries\MonthlyTarget\MonthlyTargetEntriesRepository;
use Infrastructure\Repositories\Member\MemberRepository;
use Infrastructure\Repositories\User\UserDetailRepository;
use Infrastructure\Repositories\User\UserRepository;

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
        $this->app->bind(MonthlyTargetEntriesRepositoryInterface::class, MonthlyTargetEntriesRepository::class);
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
