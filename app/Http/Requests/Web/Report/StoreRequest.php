<?php

namespace App\Http\Requests\Web\Report;

use Illuminate\Foundation\Http\FormRequest;

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
            'formulario'      => ['required', 'uuid'],
            'titulo'      => ['required', 'string', 'max:255'],
            'data_inicio' => ['required', 'date'],
            'prazo'       => ['required', 'date', 'after:data_inicio'], 
            'parametros'   => ['required', 'array'],
            'parametros.*' => ['array'], 
            'ano_acao'    => ['required', 'integer', 'digits:4'],
            'ano_inicio'  => ['required', 'integer', 'digits:4'],
            'ano_fim'     => ['required', 'integer', 'digits:4', 'gte:ano_inicio'],
        ];
    }

    /**
     * Mensagens de validação personalizadas para as regras.
     */
    public function messages(): array
    {
        return [
            'formulario.required' => 'O campo "Formulário" é de preenchimento obrigatório.',
            'formulario.uuid' => 'O campo "Formulário" deve ser válido.',
            
            'titulo.required' => 'O campo "Título do relatório" é de preenchimento obrigatório.',
            'titulo.string'   => 'O título informado não é válido.',
            'titulo.max'      => 'O título não pode ultrapassar o limite de 255 caracteres.',

            'data_inicio.required' => 'A "Data de início" é obrigatória.',
            'data_inicio.date'     => 'O formato da "Data de início" é inválido.',

            'prazo.required' => 'O "Prazo" é obrigatório.',
            'prazo.date'     => 'O formato do "Prazo" é inválido.',
            'prazo.after'    => 'O prazo definido deve ser uma data posterior à data de início.',

            'parametros.required'   => 'Os parâmetros são obrigatórios.',
            'parametros.array'   => 'O formato dos parâmetros selecionados é inválido.',
            'parametros.*.array' => 'Houve um erro na seleção dos parâmetros.',

            'ano_acao.required' => 'Selecione o "Ano da ação".',
            'ano_acao.integer'  => 'O "Ano da ação" deve ser numérico.',
            'ano_acao.digits'   => 'O "Ano da ação" deve conter 4 dígitos (Ex: 2024).',

            'ano_inicio.required' => 'Selecione o "Ano do início da ação".',
            'ano_inicio.integer'  => 'O "Ano do início da ação" deve ser numérico.',
            'ano_inicio.digits'   => 'O "Ano do início da ação" deve conter 4 dígitos.',

            'ano_fim.required' => 'Selecione o "Ano do fim da ação".',
            'ano_fim.integer'  => 'O "Ano do fim da ação" deve ser numérico.',
            'ano_fim.digits'   => 'O "Ano do fim da ação" deve conter 4 dígitos.',
            'ano_fim.gte'      => 'O "Ano do fim da ação" não pode ser anterior ao ano de início.',
        ];
    }
}
