<?php

namespace App\Http\Requests\Web\Form;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormRequest extends FormRequest
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
            'numero_secoes' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'titulo.required' => 'O campo título é obrigatório.',
            'titulo.string' => 'O título deve ser um texto.',

            'descricao.required' => 'O campo descrição é obrigatório.',
            'descricao.string' => 'O descrição deve ser um texto.',

            'numero_secoes.required' => 'O campo quantidade de seções é obrigatório.',
            'numero_secoes.integer' => 'O campo quantidade de seções deve ser um número inteiro.',
            'numero_secoes.min' => 'O campo quantidade de seções deve ser no mínimo 1.',
        ];
    }
}
