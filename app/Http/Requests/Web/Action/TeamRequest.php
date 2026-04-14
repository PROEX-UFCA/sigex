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
                })
            ],
            'categoria' => 'required|string|exists:parametro,value',
        ];
    }

    public function messages()
    {
        return [
            'id_usuario.required' => 'O campo usuário é obrigatório.',
            'id_usuario.uuid' => 'O usuário deve ser válido.',
            'id_usuario.exists' => 'O usuário deve ser válido.',
            'id_usuario.unique' => 'Usuário já inserido.',

            'categoria.required' => 'O campo categoria é obrigatório.',
            'categoria.string' => 'O campo categoria deve ser válido.',
            'categoria.exists' => 'O campo categoria deve ser válido.',
        ];
    }
}
