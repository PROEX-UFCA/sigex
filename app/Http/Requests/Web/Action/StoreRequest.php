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
            'id_projeto' => 'required|string',
            'ano' => 'required|integer',
            'tipo' => 'required|string|exists:parametro,value',
            'modalidade_edital' => 'required|string|exists:parametro,value',
            'area_tematica' => 'required|string|exists:parametro,value',
            'centro_departamento_sigla' => 'required|string|exists:parametro,value',
            'id_coordenador' => 'required|uuid|exists:users,uuid',
            'data_inicio' => 'required|date|before_or_equal:data_fim',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'situacao' => 'required|string|exists:parametro,value',
            'contexto' => 'required|string|exists:parametro,value',
            'resumo' => 'required|string',
            'palavras_chave' => 'required|string',
            'ods' => 'required|array',
            'financiamento_interno' => 'required|string|in:SIM,NÃO',
            'financiamento_externo' => 'required|string|in:SIM,NÃO',
            'bolsas_solicitadas' => 'required|integer',
            'bolsas_concedidas' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'titulo.required' => 'O campo título é obrigatório.',
            'titulo.string' => 'O título deve ser um texto.',

            'id_projeto.required' => 'O campo id do projeto é obrigatório.',
            'id_projeto.string' => 'O id do projeto deve ser um texto.',

            'ano.required' => 'O ano é obrigatório.',
            'ano.integer' => 'O ano deve ser um número inteiro.',

            'tipo.required' => 'O campo tipo é obrigatório.',
            'tipo.string' => 'O tipo deve ser um texto.',
            'tipo.exists' => 'O tipo deve existir na nossa base de dados.',

            'modalidade_edital.required' => 'O campo modalidade é obrigatório.',
            'modalidade_edital.string' => 'A modalidade deve ser um texto.',
            'modalidade_edital.exists' => 'A modalidade deve existir na nossa base de dados.',

            'area_tematica.required' => 'O campo área temática é obrigatório.',
            'area_tematica.string' => 'A área temática deve ser um texto.',
            'area_tematica.exists' => 'A área temática deve existir na nossa base de dados.',

            'centro_departamento_sigla.required' => 'O centro é obrigatório.',
            'centro_departamento_sigla.uuid' => 'O centro deve ser um UUID válido.',
            'centro_departamento_sigla.exists' => 'O centro selecionado não existe.',

            'id_coordenador.required' => 'O professor é obrigatório.',
            'id_coordenador.uuid' => 'O professor deve ser um UUID válido.',
            'id_coordenador.exists' => 'O professor selecionado não existe.',

            'data_inicio.required' => 'A data de início é obrigatória.',
            'data_inicio.date' => 'A data de início deve ser uma data válida.',
            'data_inicio.before_or_equal' => 'A data de início não pode ser posterior à data de término.',

            'data_fim.required' => 'A data de término é obrigatória.',
            'data_fim.date' => 'A data de término deve ser uma data válida.',
            'data_fim.after_or_equal' => 'A data de término não pode ser anterior à data de início.',

            'situacao.required' => 'A situação é obrigatório.',
            'situacao.string' => 'A situação deve ser válida.',
            'situacao.exists' => 'A situação selecionada não existe.',

            'contexto.required' => 'O contexto é obrigatório.',
            'contexto.string' => 'O contexto deve ser válida.',
            'contexto.exists' => 'O contexto selecionada não existe.',

            'ods.required' => 'Selecione no mínimo uma ods.',
            'ods.array' => 'As ods devem ser válidas',

            'financiamento_interno.required' => 'O financiamento interno é obrigatório.',
            'financiamento_interno.string' => 'O financiamento interno deve ser válido.',
            'financiamento_interno.in' => 'O financiamento interno deve ser válido.',

            'financiamento_externo.required' => 'O financiamento externo é obrigatório.',
            'financiamento_externo.string' => 'O financiamento externo deve ser válido.',
            'financiamento_externo.in' => 'O financiamento externo deve ser válido.',

            'bolsas_solicitadas.required' => 'As bolsas solicitadas são obrigatórias.',
            'bolsas_solicitadas.integer' => 'As bolsas solicitadas devem ser um número inteiro.',

            'bolsas_concedidas.required' => 'As bolsas concedidas são obrigatórias.',
            'bolsas_concedidas.integer' => 'As bolsas concedidas devem ser um número inteiro.',
        ];
    }
}
