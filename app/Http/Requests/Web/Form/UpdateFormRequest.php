<?php

namespace App\Http\Requests\Web\Form;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFormRequest extends FormRequest
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
            'status' => 'required|integer|in:0,1',
        ];
    }

    public function messages()
    {
        return [
            'titulo.required' => 'O campo título é obrigatório.',
            'titulo.string' => 'O título deve ser um texto.',

            'descricao.required' => 'O campo descrição é obrigatório.',
            'descricao.string' => 'O descrição deve ser um texto.',

            'status.required' => 'O status é obrigatório.',
            'status.integer' => 'O status deve ser válido.',
            'status.in' => 'O status deve ser válido',
        ];
    }
}
