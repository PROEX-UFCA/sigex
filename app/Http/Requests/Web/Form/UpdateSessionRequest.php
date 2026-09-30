<?php

namespace App\Http\Requests\Web\Form;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSessionRequest extends FormRequest
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
    public function rules()
    {

        return [
            'titulo' => 'required|string',
            'descricao' => 'required|string',
            'ordem' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'titulo.required' => 'O campo título é obrigatório.',
            'titulo.string' => 'O título deve ser um texto.',

            'descricao.required' => 'O campo descrição é obrigatório.',
            'descricao.string' => 'O descrição deve ser um texto.',

            'ordem.required' => 'O campo ordem da seção é obrigatório.',
            'ordem.integer' => 'O campo ordem da seção deve ser um número inteiro.',
            'ordem.min' => 'O campo ordem da seção deve ser no mínimo 1.',
        ];
    }
}
