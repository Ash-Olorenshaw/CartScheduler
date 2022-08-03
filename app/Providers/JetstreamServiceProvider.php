<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->configurePermissions();

        //Jetstream::createTeamsUsing(CreateTeam::class);
        //Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
        //Jetstream::addTeamMembersUsing(AddTeamMember::class);
        //Jetstream::inviteTeamMembersUsing(InviteTeamMember::class);
        //Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
        //Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);
    }

    /**
     * Configure the roles and permissions that are available within the application.
     *
     * @return void
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::role('admin', 'Administrator', [
            'admin:dashboard',
        ])->description('Administrator users can perform any action.');

        Jetstream::role('user', 'User', [
        ])->description('Users have basic app privileges.');
    }
}
