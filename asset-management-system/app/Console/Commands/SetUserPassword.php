<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('users:set-password {email}')]
#[Description('Set a user password using hidden console prompts')]
class SetUserPassword extends Command
{
    public function handle(): int
    {
        $user = User::query()->where('email', $this->argument('email'))->first();

        if ($user === null) {
            $this->error('No user account was found with that email address.');

            return self::FAILURE;
        }

        $password = $this->secret('New password (minimum 12 characters)');

        if ($password === null || strlen($password) < 12) {
            $this->error('Password must be at least 12 characters.');

            return self::INVALID;
        }

        $confirmation = $this->secret('Confirm new password');

        if ($password !== $confirmation) {
            $this->error('Passwords do not match. The password was not changed.');

            return self::INVALID;
        }

        $user->forceFill(['password' => $password])->save();
        $this->info("Password updated for {$user->email}.");

        return self::SUCCESS;
    }
}
