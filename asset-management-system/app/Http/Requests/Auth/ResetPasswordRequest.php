<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class ResetPasswordRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(['email' => strtolower(trim((string) $this->input('email', '')))]);
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:255', 'ends_with:@gmail.com'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $email = (string) $this->input('email');
                if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $validator->errors()->add('email', 'Enter a valid Gmail address.');
                }
            },
        ];
    }
}
