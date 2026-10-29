<?php

namespace App\Jobs\Dados;

use App\Models\Localidade_Acao;
use App\Models\Sincronizacao;
use Throwable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExecutarSincronizacaoLocaisJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 3600;
    public int $tries = 1;
    public bool $failOnTimeout = true;

    public function __construct(
        public int $sincronizacaoId,
        public array $dados
    ) {}

    public function handle(): void {
        $sincronizacao = Sincronizacao::findOrFail(
            $this->sincronizacaoId
        );

        $resultados = [];
        $etapaAtual = 'Inicializando - POST';

        try {
            $sincronizacao->update([
                'status' => 'executando',
                'iniciado_em' => now(),
                'etapa_atual' => $etapaAtual,
                'finalizado_em' => null,
                'erro' => null,
            ]);

            $etapaAtual = 'Sincronizando localidades - POST';

            $sincronizacao->update([
                'etapa_atual' => $etapaAtual,
            ]);

            $dados = $this->dados;

            foreach ($dados as $item) {
                $item = (object) $item;
                Localidade_Acao::updateOrCreate(
                    [
                        'id_projeto' => $item->id_projeto ?? null, 
                        'cidade' => $item->cidade ?? null,
                    ],
                    [
                        'estado' => $item->estado ?? null,
                    ]
                );
            }

            $resultados['localidades'] = count($dados);

            $sincronizacao->update([
                'resultados' => $resultados,
            ]);

            // Finalização
            $sincronizacao->update([
                'status' => 'concluida',
                'etapa_atual' => 'Concluída - Localidades (POST)',
                'resultados' => $resultados,
                'finalizado_em' => now(),
                'erro' => null,
            ]);

        } catch (Throwable $e) {
            report($e);

            $sincronizacao->update([
                'status' => 'falhou',
                'etapa_atual' => $etapaAtual,
                'resultados' => $resultados,
                'erro' => mb_substr($e->getMessage(), 0, 60000),
                'finalizado_em' => now(),
            ]);

            throw $e;
        }
    }

    public function failed(?Throwable $exception): void
    {
        $sincronizacao = Sincronizacao::find(
            $this->sincronizacaoId
        );

        if (!$sincronizacao) {
            return;
        }

        $sincronizacao->update([
            'status' => 'falhou',
            'etapa_atual' => $sincronizacao->etapa_atual
                ?: 'Falha durante a sincronização',
            'erro' => mb_substr(
                $exception?->getMessage()
                    ?? 'O Job falhou sem informar o motivo.',
                0,
                60000
            ),
            'finalizado_em' => now(),
        ]);
    }
}