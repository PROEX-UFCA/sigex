<?php

namespace App\Jobs;

use App\Models\Sincronizacao;
use App\Services\ApiSigexService;
use Throwable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExecutarSincronizacaoJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 3600;
    public int $tries = 1;
    public bool $failOnTimeout = true;

    public function __construct(
        public int $sincronizacaoId
    ) {}

    public function handle(
        ApiSigexService $api
    ): void {
        $sincronizacao = Sincronizacao::findOrFail(
            $this->sincronizacaoId
        );

        $resultados = [];
        $etapaAtual = 'Inicializando';

        $api->autenticar();

        try {
            $sincronizacao->update([
                'status' => 'executando',
                'iniciado_em' => now(),
                'etapa_atual' => $etapaAtual,
                'finalizado_em' => null,
                'erro' => null,
            ]);

            // ETAPA 1: Ações
            $etapaAtual = 'Sincronizando ações';

            $sincronizacao->update([
                'etapa_atual' => $etapaAtual,
            ]);

            $dados = $api->buscarAcoes();

            foreach ($dados as $item) {

                Acao::updateOrCreate(
                    ['id_externo' => $item['id']],
                    [
                        'titulo' => $item['titulo'],
                        'descricao' => $item['descricao'],
                        'status' => $item['status'],
                    ]
                );
            }

            $resultados['acoes'] = count($dados);

            $sincronizacao->update([
                'resultados' => $resultados,
            ]);

            //ETAPA 2: Localidades
            $etapaAtual = 'Sincronizando localidades';

            $sincronizacao->update([
                'etapa_atual' => $etapaAtual,
            ]);

            $dados = $api->buscarLocalidades();

            foreach ($dados as $item) {

                Localidade::updateOrCreate(
                    ['id_externo' => $item['id']],
                    [
                        'nome' => $item['nome'],
                        'cidade' => $item['cidade'],
                    ]
                );
            }

            $resultados['localidades'] = count($dados);

            $sincronizacao->update([
                'resultados' => $resultados,
            ]);

            // ETAPA 3: Membros
            $etapaAtual = 'Sincronizando membros';

            $sincronizacao->update([
                'etapa_atual' => $etapaAtual,
            ]);

            $dados = $api->buscarMembros();

            foreach ($dados as $item) {

                Membro::updateOrCreate(
                    ['id_externo' => $item['id']],
                    [
                        'nome' => $item['nome'],
                        'email' => $item['email'],
                        'cpf' => $item['cpf'],
                    ]
                );
            }

            $resultados['membros'] = count($dados);

            // Finalização
            $sincronizacao->update([
                'status' => 'concluida',
                'etapa_atual' => 'Concluída',
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