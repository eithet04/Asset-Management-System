<?php

namespace App\Http\Requests\Auth;

use App\Support\MyanmarPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class RegisterRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim((string) $this->input('email', ''))),
            'phone' => MyanmarPhoneNumber::normalize((string) $this->input('phone', ''))
                ?? trim((string) $this->input('phone', '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'ends_with:@gmail.com', 'unique:users,email'],
            'phone' => ['required', 'regex:/^\+959[2-9]\d{6,8}$/', 'unique:users,phone'],
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
