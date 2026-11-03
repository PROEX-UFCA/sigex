<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class ApiSigexService
{
    private string $token;

    public function autenticar(): void
    {
        $response = Http::post(
            config('services.sigex.url') . '/login',
            [
                'email' => config('services.sigex.email'),
                'password' => config('services.sigex.password'),
            ]
        );

        if ($response->unauthorized()) {
            throw new Exception('Credenciais inválidas na API.');
        }

        if (!$response->successful()) {
            throw new Exception(
                'Erro ao autenticar: ' . $response->body()
            );
        }

        $this->token = $response->json('token');
    }

    private function request(string $rota)
    {
        $response = Http::withToken($this->token)
            ->acceptJson()
            ->get(config('services.sigex.url') . $rota);

        if (!$response->successful()) {
            throw new Exception(
                "Erro ao consultar {$rota}: " . $response->body()
            );
        }

        return $response->json();
    }

    public function buscarAcoes()
    {
        return $this->request('/acoes');
    }

    public function buscarLocalidades()
    {
        return $this->request('/localidades');
    }

    public function buscarMembros()
    {
        return $this->request('/membros');
    }
}