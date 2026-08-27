<?php

namespace App\Http\Requests\Web\Action;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GaleriaRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'imagens' => ['required', 'array', 'max:10'],
            'imagens.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'imagens.required' => 'É necessário enviar pelo menos uma imagem.',
            'imagens.max' => 'Você pode enviar no máximo 10 imagens por vez.',
            'imagens.*.image' => 'Os arquivos devem ser imagens válidas.',
            'imagens.*.mimes' => 'As imagens devem ser nos formatos JPEG, PNG, JPG ou WEBP.',
            'imagens.*.max' => 'Cada imagem não pode ultrapassar 5MB.',
        ];
    }
}
