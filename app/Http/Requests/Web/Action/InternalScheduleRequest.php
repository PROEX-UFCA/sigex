<?php

namespace App\Http\Requests\Web\Action;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class InternalScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'pauta_interna' => ['nullable', 'string'],
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'pauta_interna.string' => 'A pauta interna deve ser um texto válido.',
        ]);
    }
}
