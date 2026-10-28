<?php

namespace App\Http\Requests\Web\Settings\Profile;

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
    public function rules()
    {
        return [
            'name' => 'nullable|string|max:255',

            // Novos campos
            // 'birth' => 'nullable|date|before:today',
            'phone' => 'nullable|string|max:20|regex:/^\(?\d{2}\)?[\s-]?\d{4,5}-?\d{4}$/',
            'cpf' => 'nullable|string|max:14|regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/',
            'matricula_siape' => 'nullable|string',
            'centro' => 'nullable|string|exists:parametro,value',
            'curso' => 'nullable|string|exists:parametro,value',
        ];
    }

    public function messages()
    {
        return [
            // Nome
            'name.string' => 'O nome deve ser um texto.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',

            'matricula_siape.string' => 'A matrícula ou siape deve ser um texto.',

            // Telefone
            'phone.string' => 'O telefone deve ser um texto.',
            'phone.max' => 'O telefone não pode ter mais de 20 caracteres.',
            'phone.regex' => 'O formato do telefone é inválido. Use o formato (99) 99999-9999.',

            'cpf.string' => 'O cpf deve ser um texto.',
            'cpf.max' => 'O cpf não pode ter mais de 14 caracteres.',
            'cpf.regex' => 'O formato do cpf é inválido (Use o formato XXX.XXX.XXX-XX).',

            'centro.uuid' => 'O centro deve ser um UUID válido.',
            'centro.exists' => 'O centro selecionado não existe.',
        
            'curso.uuid' => 'O curso deve ser um UUID válido.',
            'curso.exists' => 'O curso selecionado não existe.',
        ];
    }
}
