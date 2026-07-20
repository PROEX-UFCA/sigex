<?php

namespace App\Http\Requests\Web\Action;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeamRequest extends FormRequest
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
            'id_usuario' => [
            'required',
            'uuid',
            'exists:users,uuid',
                Rule::unique('equipe_acao', 'id_usuario')->where(function ($query) {
                    return $query->where('id_acao', $this->route('uuid'));
                })->withoutTrashed()
            ],
            
            'tipo_membro' => 'required|string|exists:parametro,value',
            'categoria_membro' => 'required|string|exists:parametro,value',
            'status_membros' => 'required|string|exists:parametro,value',
            'tipo_vinculo' => 'required|string|exists:parametro,value',
            'data_inicio' => 'required|date|before_or_equal:data_fim',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
        ];
    }

    public function messages()
    {
        return [
            'id_usuario.required' => 'O campo usuário é obrigatório.',
            'id_usuario.uuid' => 'O usuário deve ser válido.',
            'id_usuario.exists' => 'O usuário deve ser válido.',
            'id_usuario.unique' => 'Usuário já inserido.',

            'tipo_membro.required' => 'O campo tipo de membro é obrigatório.',
            'tipo_membro.string' => 'O tipo de membro deve ser um texto.',
            'tipo_membro.exists' => 'O tipo de membro deve existir na nossa base de dados.',

            'categoria_membro.required' => 'O campo categoria do membro é obrigatório.',
            'categoria_membro.string' => 'A categoria do membro deve ser um texto.',
            'categoria_membro.exists' => 'A categoria do membro deve existir na nossa base de dados.',

            'status_membros.required' => 'O campo status do membro é obrigatório.',
            'status_membros.string' => 'A status do membro deve ser um texto.',
            'status_membros.exists' => 'A status do membro deve existir na nossa base de dados.',

            'tipo_vinculo.required' => 'O tipo de vínculo é obrigatório.',
            'tipo_vinculo.string' => 'O tipo de vínculo deve ser um texto.',
            'tipo_vinculo.exists' => 'O tipo de vínculo selecionado não existe.',

            'data_inicio.required' => 'A data de início é obrigatória.',
            'data_inicio.date' => 'A data de início deve ser uma data válida.',
            'data_inicio.before_or_equal' => 'A data de início não pode ser posterior à data de término.',

            'data_fim.required' => 'A data de término é obrigatória.',
            'data_fim.date' => 'A data de término deve ser uma data válida.',
            'data_fim.after_or_equal' => 'A data de término não pode ser anterior à data de início.',
        ];
    }
}
