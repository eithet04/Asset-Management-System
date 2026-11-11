<?php

namespace App\Http\Requests\Auth;

use App\Support\MyanmarPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class LoginRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $identifier = trim((string) $this->input('identifier', ''));
        if (str_contains($identifier, '@')) {
            $identifier = strtolower($identifier);
        } else {
            $identifier = MyanmarPhoneNumber::normalize($identifier) ?? $identifier;
        }

        $this->merge(['identifier' => $identifier]);
    }

    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $identifier = (string) $this->input('identifier');
                if ($identifier === '') {
                    return;
                }

                if (str_contains($identifier, '@')) {
                    if (! filter_var($identifier, FILTER_VALIDATE_EMAIL) || ! str_ends_with($identifier, '@gmail.com')) {
                        $validator->errors()->add('identifier', 'Enter a valid Gmail address or Myanmar mobile number.');
                    }

                    return;
                }

                if (MyanmarPhoneNumber::normalize($identifier) !== $identifier) {
                    $validator->errors()->add('identifier', 'Enter a valid Myanmar mobile number.');
                }
            },
        ];
    }
}
