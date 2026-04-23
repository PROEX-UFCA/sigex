<?php

namespace App\Providers;

use App\Repositories\Actions\ActionsRepository;
use App\Repositories\Actions\EloquentActionsRepository;
use App\Repositories\Forms\EloquentFormsRepository;
use App\Repositories\Forms\FormsRepository;
use App\Repositories\Parametros\EloquentParametrosRepository;
use App\Repositories\Parametros\ParametrosRepository;
use App\Repositories\Settings\Roles\EloquentRolesRepository;
use App\Repositories\Settings\Roles\RolesRepository;
use App\Repositories\Settings\User\EloquentUsersRepository;
use App\Repositories\Settings\User\UsersRepository;
use App\Repositories\Tokens\UserTokens\EloquentUsersTokensRepository;
use App\Repositories\Tokens\UserTokens\UsersTokensRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UsersRepository::class, EloquentUsersRepository::class);
        $this->app->bind(UsersTokensRepository::class, EloquentUsersTokensRepository::class);
        $this->app->bind(RolesRepository::class, EloquentRolesRepository::class);
        $this->app->bind(ActionsRepository::class, EloquentActionsRepository::class);
        $this->app->bind(ParametrosRepository::class, EloquentParametrosRepository::class);
        $this->app->bind(FormsRepository::class, EloquentFormsRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
    }
}
