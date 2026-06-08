<?php

namespace App\Http\Requests\Web\Report;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'titulo'      => ['required', 'string', 'max:255'],
            'data_inicio' => ['required', 'date'],
            'prazo'       => ['required', 'date', 'after:data_inicio'],
            'status'       => ['required', 'in:0,1,2'],
        ];
    }

    /**
     * Mensagens de validação personalizadas para as regras.
     */
    public function messages(): array
    {
        return [
            'titulo.required' => 'O campo "Título do relatório" é de preenchimento obrigatório.',
            'titulo.string'   => 'O título informado não é válido.',
            'titulo.max'      => 'O título não pode ultrapassar o limite de 255 caracteres.',

            'data_inicio.required' => 'A "Data de início" é obrigatória.',
            'data_inicio.date'     => 'O formato da "Data de início" é inválido.',

            'prazo.required' => 'O "Prazo" é obrigatório.',
            'prazo.date'     => 'O formato do "Prazo" é inválido.',
            'prazo.after'    => 'O prazo definido deve ser uma data posterior à data de início.',

            'status.required' => 'O status é obrigatório.',
            'status.integer' => 'O status deve ser válido.',
            'status.in' => 'O status deve ser válido',            
        ];
    }
}
