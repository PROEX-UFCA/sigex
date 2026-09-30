<?php

namespace App\Http\Requests\Web\Action;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleRequest extends FormRequest
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
            'titulo'           => ['required', 'string', 'max:255'],
            'local_formato'    => ['required', 'string', 'max:255'],
            'data_hora_inicio' => ['required', 'date'],
            'data_hora_fim'    => ['required', 'date', 'after:data_hora_inicio'], 
            'descricao'        => ['required', 'string'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'titulo.required' => 'O título do evento é obrigatório.',
            'titulo.string'   => 'O título do evento deve ser um texto válido.',
            'titulo.max'      => 'O título do evento não pode ultrapassar 255 caracteres.',

            'local_formato.required' => 'O local ou formato do evento é obrigatório.',
            'local_formato.string'   => 'O local ou formato do evento deve ser um texto válido.',
            'local_formato.max'      => 'O local ou formato do evento não pode ultrapassar 255 caracteres.',

            'data_hora_inicio.required' => 'A data e hora de início são obrigatórias.',
            'data_hora_inicio.date'     => 'Informe uma data e hora de início válida.',

            'data_hora_fim.required' => 'A data e hora de término são obrigatórias.',
            'data_hora_fim.date'     => 'Informe uma data e hora de término válida.',
            'data_hora_fim.after'    => 'A data e hora de término não podem ser anteriores ou iguais ao início do evento.',

            'descricao.required' => 'A descrição do evento é obrigatória.',
            'descricao.string'   => 'A descrição do evento deve ser um texto válido.',
        ];
    }
}
