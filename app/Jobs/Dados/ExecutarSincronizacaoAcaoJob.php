<?php

namespace App\Jobs\Dados;

use App\Models\Acao;
use App\Models\Parametro;
use App\Models\Sincronizacao;
use Throwable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExecutarSincronizacaoAcaoJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 3600;
    public int $tries = 1;
    public bool $failOnTimeout = true;

    public function __construct(
        public int $sincronizacaoId,
        public $dados
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

            // ETAPA 1: Ações
            $etapaAtual = 'Sincronizando ações - POST';

            $sincronizacao->update([
                'etapa_atual' => $etapaAtual,
            ]);

            $dados = $this->dados;

            foreach ($dados as $item) {
                $item = (object) $item;

                $centroDepartamento = !empty($item->centro_departamento_sigla) ? Parametro::firstOrCreate([
                    'function' => 'CENTRO_DEPARTAMENTO_SIGLA',
                    'value' => mb_strtoupper(trim($item->centro_departamento_sigla), 'UTF-8'),
                ]) : null;

                $tipoAcao = !empty($item->tipo_acao) ? Parametro::firstOrCreate([
                    'function' => 'TIPO',
                    'value' => mb_strtoupper(trim($item->tipo_acao), 'UTF-8'),
                ]) : null;

                $areaTematica = !empty($item->area_tematica) ? Parametro::firstOrCreate([
                    'function' => 'AREA_TEMATICA',
                    'value' => mb_strtoupper(trim($item->area_tematica), 'UTF-8'),
                ]) : null;

                $modalidade = !empty($item->modalidade_edital) ? Parametro::firstOrCreate([
                    'function' => 'MODALIDADE_EDITAL',
                    'value' => mb_strtoupper(trim($item->modalidade_edital), 'UTF-8'),
                ]) : null;

                $situacao = !empty($item->situacao) ? Parametro::firstOrCreate([
                    'function' => 'SITUACAO',
                    'value' => mb_strtoupper(trim($item->situacao), 'UTF-8'),
                ]) : null;

                $contexto = !empty($item->contexto) ? Parametro::firstOrCreate([
                    'function' => 'CONTEXTO',
                    'value' => mb_strtoupper(trim($item->contexto), 'UTF-8'),
                ]) : null;

                Acao::updateOrCreate(
                    ['id_projeto' => $item->id_projeto],
                    [
                        'ano' => $item->ano ?? null,
                        'titulo' => $item->titulo ?? null,
                        'modalidade_edital' => $modalidade->value ?? null,
                        'bolsas_solicitadas' => $item->bolsas_solicitadas ?? 0,
                        'bolsas_concedidas' => $item->bolsas_concedidas ?? 0,
                        'financiamento_interno' => $item->financiamento_interno ?? null,
                        'financiamento_externo' => $item->financiamento_externo ?? null,
                        'situacao' => $situacao->value ?? null,
                        'data_cadastro' => $item->data_cadastro ?? null,
                        'data_inicio' => $item->data_inicio ?? null,
                        'data_fim' => $item->data_fim ?? null,
                        'data_atualizacao' => $item->data_atualizacao ?? null,
                        'centro_departamento_sigla' => $centroDepartamento->value ?? null,
                        'tipo_acao' => $tipoAcao->value ?? null,
                        'area_tematica' => $areaTematica->value ?? null,
                        'resumo' => $item->resumo ?? null,
                        'palavras_chave' => $item->palavras_chave ?? null,
                        'ods' => $item->ods ?? null,
                        'contexto' => $contexto->value ?? null,
                    ]
                );
            }

            $resultados['acoes'] = count($dados);

            $sincronizacao->update([
                'resultados' => $resultados,
            ]);

            // Finalização
            $sincronizacao->update([
                'status' => 'concluida',
                'etapa_atual' => 'Concluída - Ações (POST)',
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