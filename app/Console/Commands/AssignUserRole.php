<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('users:assign-role {email} {role : admin, manager, or employee}')]
#[Description('Assign a role to an existing user account')]
class AssignUserRole extends Command
{
    public function handle(): int
    {
        $role = (string) $this->argument('role');

        if (! in_array($role, User::ROLES, true)) {
            $this->error('Role must be admin, manager, or employee.');

            return self::INVALID;
        }

        $user = User::query()->where('email', $this->argument('email'))->first();

        if ($user === null) {
            $this->error('No user account was found with that email address.');

            return self::FAILURE;
        }

        $user->forceFill(['role' => $role])->save();
        $this->info("Assigned the {$role} role to {$user->email}.");

        return self::SUCCESS;
    }
}
