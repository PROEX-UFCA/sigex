<?php

namespace App\Http\Requests\Web\Action;

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
    public function rules()
    {

        return [
            'titulo' => 'required|string',
            'id_atividade' => 'required|string',
            'id_projeto' => 'required|string',
            'ano' => 'required|integer',
            'tipo' => 'required|string|exists:parametro,value',
            'modalidade' => 'required|string|exists:parametro,value',
            'area_tematica' => 'required|string|exists:parametro,value',
            'centro_departamento' => 'required|string|exists:parametro,value',
            'id_coordenador' => 'required|uuid|exists:users,uuid',
            'data_inicio' => 'required|date|before_or_equal:data_fim',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'status' => 'required|integer|in:0,1,2',
        ];
    }

    public function messages()
    {
        return [
            'titulo.required' => 'O campo título é obrigatório.',
            'titulo.string' => 'O título deve ser um texto.',

            'id_atividade.required' => 'O campo id da atividade é obrigatório.',
            'id_atividade.string' => 'O id da atividade deve ser um texto.',

            'id_projeto.required' => 'O campo id do projeto é obrigatório.',
            'id_projeto.string' => 'O id do projeto deve ser um texto.',

            'ano.required' => 'O ano é obrigatório.',
            'ano.integer' => 'O ano deve ser um número inteiro.',

            'tipo.required' => 'O campo tipo é obrigatório.',
            'tipo.string' => 'O tipo deve ser um texto.',
            'tipo.exists' => 'O tipo deve existir na nossa base de dados.',

            'modalidade.required' => 'O campo modalidade é obrigatório.',
            'modalidade.string' => 'A modalidade deve ser um texto.',
            'modalidade.exists' => 'A modalidade deve existir na nossa base de dados.',

            'area_tematica.required' => 'O campo área temática é obrigatório.',
            'area_tematica.string' => 'A área temática deve ser um texto.',
            'area_tematica.exists' => 'A área temática deve existir na nossa base de dados.',

            'ceentro_departamento.required' => 'O curso é obrigatório.',
            'ceentro_departamento.uuid' => 'O curso deve ser um UUID válido.',
            'ceentro_departamento.exists' => 'O curso selecionado não existe.',

            'id_coordenador.required' => 'O professor é obrigatório.',
            'id_coordenador.uuid' => 'O professor deve ser um UUID válido.',
            'id_coordenador.exists' => 'O professor selecionado não existe.',

            'data_inicio.required' => 'A data de início é obrigatória.',
            'data_inicio.date' => 'A data de início deve ser uma data válida.',
            'data_inicio.before_or_equal' => 'A data de início não pode ser posterior à data de término.',

            'data_fim.required' => 'A data de término é obrigatória.',
            'data_fim.date' => 'A data de término deve ser uma data válida.',
            'data_fim.after_or_equal' => 'A data de término não pode ser anterior à data de início.',

            'status.required' => 'O status é obrigatório.',
            'status.integer' => 'O status deve ser um número inteiro.',
            'status.in' => 'O status deve ser 0 (Rascunho), 1 (Ativo) ou 2 (Concluído).',
        ];
    }
}
