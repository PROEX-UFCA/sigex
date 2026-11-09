<?php

namespace App\Http\Requests\Web\Settings\Profile;

use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'actual_password'  => ['required', 'string', 'max:255'],
            'password'         => ['required', 'string', 'min:8', 'max:255', 'same:password_confirm'],
            'password_confirm' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'actual_password.required'  => 'Informe sua senha atual.',
            'actual_password.string'    => 'A senha atual deve ser um texto.',
            'actual_password.max'       => 'A senha atual não pode ter mais de 255 caracteres.',

            'password.required'         => 'Informe a nova senha.',
            'password.min'              => 'A nova senha deve ter pelo menos 8 caracteres.',
            'password.same'             => 'A confirmação da nova senha não corresponde.',
            'password.max'              => 'A nova senha não pode ter mais de 255 caracteres.',

            'password_confirm.required' => 'Confirme a nova senha.',
            'password_confirm.max'      => 'A confirmação não pode ter mais de 255 caracteres.',
        ];
    }
}