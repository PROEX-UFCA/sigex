<?php

namespace App\Http\Requests\Web\Auth;

use Illuminate\Foundation\Http\FormRequest;

class FirstRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "email" => [
                "required",
                "email",
                // "regex:/^[a-zA-Z0-9._%+-]+@aluno.ufca\.edu\.br$/",
                // "unique:users,email",
            ],
            // 'name' => 'required|string|',
            'aceite' => 'accepted'
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            // "email.regex" => "O e-mail deve pertencer ao domínio @aluno.ufca.edu.br.",
            // "email.unique" => "Informe um e-mail válido.",

            // 'name.required' => 'O campo nome é obrigatório.',
            // 'name.string' => 'A senha nome deve ser uma sequência de caracteres.',

            'aceite.accepted' => 'Você precisa aceitar os termos de uso e a política de privacidade para continuar.',
        ];
    }
}
