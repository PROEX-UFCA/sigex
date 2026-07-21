<?php

namespace App\Http\Requests\Web\Form;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
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
            'tipo'        => ['required', 'string', 'in:text,textarea,number,file,select,radio,checkbox,location,date,datetime-local,tabela'],
            'enunciado'   => ['required', 'string', 'max:1000'],
            'obrigatoria' => ['required', 'boolean'],
            'min'         => ['nullable', 'integer'],
            'max'         => ['nullable', 'integer', 'gte:min'],
            'step'        => ['nullable', 'numeric'],
            'regex'       => ['nullable', 'string'],
            'accept'      => ['nullable', 'array'],
            'accept.*'    => ['string'],
            'opcoes'      => ['required_if:tipo,select,radio,checkbox', 'array'],
            'opcoes.*'    => ['required_with:opcoes', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo.required' => 'Escolha o tipo da pergunta.',
            'tipo.in' => 'O tipo de pergunta selecionado é inválido.',
            'enunciado.required' => 'O enunciado da pergunta é obrigatório.',
            'obrigatoria.required' => 'Informe se a pergunta é obrigatória.',
            'max.gte' => 'O valor máximo não pode ser menor que o valor mínimo.',
            'opcoes.required_if' => 'Você precisa adicionar pelo menos uma opção para este tipo de pergunta.',
            'opcoes.*.required_with' => 'A opção não pode estar vazia.',
        ];
    }
}
