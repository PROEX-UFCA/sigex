<?php

namespace App\Http\Requests\Web\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Captcha;

class StoreRequest extends FormRequest
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
            'email' => 'required|email',
            'password' => 'required|string',
            'g-recaptcha-response' => filled(config('services.captcha.secret_key'))
                ? ['required', new Captcha()]
                : ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.string' => 'A senha deve ser uma sequência de caracteres.',
            'g-recaptcha-response.required' => 'Não foi possível validar que você é humano. Recarregue a página e tente novamente.',
        ];
    }
}
