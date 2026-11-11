<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ForgotPasswordRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(['email' => strtolower(trim((string) $this->input('email', '')))]);
    }

    public function rules(): array
    {
        return ['email' => ['required', 'email', 'max:255', 'ends_with:@gmail.com']];
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
