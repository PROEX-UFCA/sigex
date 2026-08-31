<?php

namespace App\Http\Requests\Web\Auth;

use Illuminate\Foundation\Http\FormRequest;

class FirstRequest extends FormRequest
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
            'is_external_institution' => ['required', 'in:0,1'],

            // Campos obrigatórios para ambos os casos
            'email' => ['required', 'email', 'max:255'],
            'aceite' => ['required', 'accepted'],

            // Campos obrigatórios apenas se for instituição externa (is_external_institution == 1)
            'nome' => ['required_if:is_external_institution,1', 'nullable', 'string', 'max:255'],
            'cnpj' => ['required_if:is_external_institution,1', 'nullable', 'string', 'max:20', 'unique:instituicao_externa,cnpj'],
            'cep' => ['required_if:is_external_institution,1', 'nullable', 'string', 'max:10'],
            'logradouro' => ['required_if:is_external_institution,1', 'nullable', 'string', 'max:255'],
            'numero' => ['required_if:is_external_institution,1', 'nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:255'],
            'telefone_contato' => ['required_if:is_external_institution,1', 'nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'is_external_institution.required' => 'Informe se você é uma instituição externa.',
            'is_external_institution.in' => 'Opção selecionada é inválida.',

            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.max' => 'O e-mail não pode ter mais que 255 caracteres.',

            'nome.required_if' => 'O campo nome da instituição é obrigatório.',
            'nome.string' => 'O nome deve ser um texto válido.',

            'cnpj.required_if' => 'O campo CNPJ é obrigatório.',
            'cnpj.unique' => 'Essa instituição já está cadastrada.',
            
            'cep.required_if' => 'O campo CEP é obrigatório.',
            
            'logradouro.required_if' => 'O campo logradouro é obrigatório.',
            
            'numero.required_if' => 'O campo número é obrigatório.',

            'telefone_contato.required_if' => 'O campo telefone de contato é obrigatório.',

            'aceite.required' => 'Você precisa aceitar os termos de uso e a política de privacidade para continuar.',
            'aceite.accepted' => 'Você precisa aceitar os termos de uso e a política de privacidade para continuar.',
        ];
    }
}
