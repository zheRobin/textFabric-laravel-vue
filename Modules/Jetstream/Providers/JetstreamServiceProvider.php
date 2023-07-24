<?php

namespace Modules\Jetstream\Providers;

use App\Models\Team;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Laravel\Jetstream\Jetstream;
use Modules\Jetstream\Actions\AddTeamMember;
use Modules\Jetstream\Actions\CreateTeam;
use Modules\Jetstream\Actions\DeleteTeam;
use Modules\Jetstream\Actions\DeleteUser;
use Modules\Jetstream\Actions\ToggleDisabledTeam;
use Modules\Jetstream\Actions\InviteTeamMember;
use Modules\Jetstream\Actions\RemoveTeamMember;
use Modules\Jetstream\Actions\UpdateLogo;
use Modules\Jetstream\Actions\UpdateTeamName;
use Modules\Jetstream\Contracts\TogglesDisabledTeam;
use Modules\Jetstream\Contracts\UpdatesLogo;
use Modules\Jetstream\Policies\TeamPolicy;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public array $bindings = [
        TogglesDisabledTeam::class => ToggleDisabledTeam::class,
        UpdatesLogo::class => UpdateLogo::class,
    ];

    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Team::class => TeamPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRoutes();
        $this->registerMigrations();
        $this->configurePermissions();
        $this->registerPolicies();

        Jetstream::ignoreRoutes();
        Jetstream::createTeamsUsing(CreateTeam::class);
        Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        Jetstream::inviteTeamMembersUsing(InviteTeamMember::class);
        Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
        Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);

        $this->app->register(AuthServiceProvider::class);
    }

    /**
     * Configure the roles and permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::role('admin', 'Administrator', [
            'Enable'
//            'create',
//            'read',
//            'update',
//            'delete',
        ])->description('Administrator users can perform any action.');

        Jetstream::role('editor', 'Editor', [
            'Enable',
//            'create',
//            'update',
        ])->description('Editor users have the ability to read, create, and update.');

        Jetstream::role('viewer', 'Viewer', [])->description('Viewer users');
    }

    /**
     * Configure routes.
     */
    protected function configureRoutes(): void
    {
        Route::middleware('web')
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            });
    }

    /**
     * Configure publishing.
     */
    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
