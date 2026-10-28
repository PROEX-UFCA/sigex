<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class Captcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secret = config('services.captcha.secret_key');

        if (blank($secret)) {
            return;
        }

        if (blank($value)) {
            $fail('Não foi possível validar que você é humano. Recarregue a página e tente novamente.');
            return;
        }

        try {
            $response = Http::asForm()->timeout(5)->post(config('services.captcha.verify_url'), [
                'secret'   => $secret,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);
        } catch (Throwable $e) {
            Log::warning('Captcha indisponível, liberando requisição.', ['error' => $e->getMessage()]);
            return;
        }

        $body = $response->json() ?? [];

        if (! ($body['success'] ?? false)) {
            $fail('Não foi possível validar que você é humano. Recarregue a página e tente novamente.');
            return;
        }

        if (isset($body['score']) && $body['score'] < (float) config('services.captcha.threshold')) {
            $fail('Sua requisição foi bloqueada por suspeita de automação. Tente novamente.');
        }
    }
}