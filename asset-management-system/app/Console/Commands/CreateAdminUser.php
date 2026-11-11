<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('users:create-admin {email : Email address for the administrator account}')]
#[Description('Create an administrator account using hidden password prompts')]
class CreateAdminUser extends Command
{
    public function handle(): int
    {
        $email = strtolower(trim((string) $this->argument('email')));

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->error('Enter a valid email address.');

            return self::INVALID;
        }

        if (User::query()->where('email', $email)->exists()) {
            $this->error('An account already exists with that email. Use users:assign-role to change its role.');

            return self::FAILURE;
        }

        $name = trim((string) $this->ask('Administrator name'));

        if ($name === '') {
            $this->error('Name is required.');

            return self::INVALID;
        }

        $password = $this->secret('Password (minimum 12 characters)');

        if ($password === null || strlen($password) < 12) {
            $this->error('Password must be at least 12 characters.');

            return self::INVALID;
        }

        if ($password !== $this->secret('Confirm password')) {
            $this->error('Passwords do not match. The account was not created.');

            return self::INVALID;
        }

        $user = DB::transaction(function () use ($email, $name, $password): User {
            $role = Role::query()->firstOrCreate(
                ['slug' => User::ROLE_ADMIN],
                ['name' => 'Administrator'],
            );

            return User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'role_id' => $role->id,
                'is_active' => true,
            ]);
        });

        $this->info("Administrator account created for {$user->email}.");

        return self::SUCCESS;
    }
}
