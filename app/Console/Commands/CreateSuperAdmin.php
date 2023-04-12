<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Fortify\Actions\CreateSuperAdminUser;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-admin {first_name?} {last_name?} {email?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register an admin.';

    /**
     * Execute the console command.
     */
    public function handle(CreateSuperAdminUser $createAdmin): void
    {
        $user = $createAdmin->create($this->data());

        $this->line("Admin [{$user->email}] has been registered.");
    }

    /**
     * @return array
     */
    private function data(): array
    {
        return [
            'first_name' => $this->argument('first_name') ?? $this->ask('What is the admin\'s first name?'),
            'last_name' => $this->argument('last_name') ?? $this->ask('What is the admin\'s last name?'),
            'email' => $this->argument('email') ?? $this->ask('What is the admin\'s email?'),
            'password' => $this->argument('password') ?? $this->secret('What is the admin\'s password?'),
        ];
    }
}
