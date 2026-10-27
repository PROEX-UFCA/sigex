<?php

namespace App\Jobs;

use App\Models\Acao;
use App\Models\Equipe_Acao;
use App\Models\Localidade_Acao;
use App\Models\Parametro;
use App\Models\Sincronizacao;
use App\Models\User;
use App\Services\ApiSigexService;
use Throwable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

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
                $item = (object) $item;

                $centroDepartamento = !empty($item->sigla) ? Parametro::firstOrCreate([
                    'function' => 'CENTRO_DEPARTAMENTO_SIGLA',
                    'value' => mb_strtoupper(trim($item->sigla), 'UTF-8'),
                ]) : null;

                $tipoAcao = !empty($item->tipo_acao) ? Parametro::firstOrCreate([
                    'function' => 'TIPO',
                    'value' => mb_strtoupper(trim($item->tipo_acao), 'UTF-8'),
                ]) : null;

                $areaTematica = !empty($item->area_tematica) ? Parametro::firstOrCreate([
                    'function' => 'AREA_TEMATICA',
                    'value' => mb_strtoupper(trim($item->area_tematica), 'UTF-8'),
                ]) : null;

                $modalidade = !empty($item->edital) ? Parametro::firstOrCreate([
                    'function' => 'MODALIDADE_EDITAL',
                    'value' => mb_strtoupper(trim($item->edital), 'UTF-8'),
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
                        'modalidade_edital' => $item->edital ?? null,
                        'bolsas_solicitadas' => $item->bolsas_solicitadas ?? 0,
                        'bolsas_concedidas' => $item->bolsas_concedidas ?? 0,
                        'financiamento_interno' => $item->financiamento_interno ?? null,
                        'financiamento_externo' => $item->financiamento_externo ?? null,
                        'situacao' => $item->situacao ?? null,
                        'data_cadastro' => $item->data_cadastro ?? null,
                        'data_inicio' => $item->data_inicio ?? null,
                        'data_fim' => $item->data_fim ?? null,
                        'data_atualizacao' => $item->data_atualizacao ?? null,
                        'centro_departamento_sigla' => $item->sigla ?? null,
                        'tipo_acao' => $item->tipo_acao ?? null,
                        'area_tematica' => $item->area_tematica ?? null,
                        'resumo' => $item->resumo ?? null,
                        'palavras_chave' => $item->palavras_chave ?? null,
                        'ods' => $item->ods ?? null,
                        'contexto' => $item->contexto ?? null,
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

            // ETAPA 3: Membros
            $etapaAtual = 'Sincronizando membros';

            $sincronizacao->update([
                'etapa_atual' => $etapaAtual,
            ]);

            $dados = $api->buscarMembros();

            $tiposMembrosReq = [];
            $categoriasReq = [];
            $sexosReq = [];
            $statusReq = [];

            foreach ($dados as $item) {
                $item = (object) $item;
                $agora = now();

                if (!empty($item->tipo_membro)) {
                    $tiposMembrosReq[] = mb_strtoupper(
                        trim($item->tipo_membro),
                        'UTF-8'
                    );
                }

                if (!empty($item->categoria_membro)) {
                    $categoriasReq[] = mb_strtoupper(
                        trim($item->categoria_membro),
                        'UTF-8'
                    );
                }

                if (!empty($item->sexo)) {
                    $sexosReq[] = mb_strtoupper(
                        trim($item->sexo),
                        'UTF-8'
                    );
                }

                if (!empty($item->status)) {
                    $statusReq[] = mb_strtoupper(
                        trim((string) $item->status),
                        'UTF-8'
                    );
                }

                if($item->email != null){
                    $usuario = User::firstOrCreate(
                        ['email' => $item->email],
                        [
                            'uuid' => (string) Str::uuid(),
                            'email' => $item->email,
                            'name' => $item->nome_ascii,
                            'status' => 0,
                            'created_at' => $agora,
                            'updated_at' => $agora,
                        ]
                    );

                    $usuario->assignRole('Perfil Acadêmico');
    
                    $id_acao = Acao::where('id_projeto', $item->id_projeto)->first()->id;
    
                    Equipe_Acao::updateOrCreate(
                        [
                            'id_projeto' => $item->id_projeto, 
                            'id_pessoa' => $item->id_pessoa, 
                        ],
                        [
                            "id_usuario" =>$usuario->uuid,
                            "id_acao" =>$id_acao,
                            'tipo_membro' => $item->tipo_membro, 
                            'categoria_membro' => $item->categoria_membro, 
                            'status' => $item->status, 
                            'data_inicio' => $item->data_inicio, 
                            'data_fim' => $item->data_fim,
                            'sexo' => $item->sexo, 
                        ]
                    );
                }
            }

            $resultados['membros'] = count($dados);

            $tiposMembrosReq = array_values(array_unique($tiposMembrosReq));
            $categoriasReq = array_values(array_unique($categoriasReq));
            $sexosReq = array_values(array_unique($sexosReq));
            $statusReq = array_values(array_unique($statusReq));

            $this->garantirParametrosEmLote('TIPO_MEMBRO', $tiposMembrosReq);
            $this->garantirParametrosEmLote('CATEGORIA_MEMBRO', $categoriasReq);
            $this->garantirParametrosEmLote('SEXO', $sexosReq);
            $this->garantirParametrosEmLote('STATUS_MEMBROS', $statusReq);

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

    private function garantirParametrosEmLote(
        string $funcao,
        array $valores
    ): void {
        $valores = array_values(array_unique(array_filter(
            $valores,
            fn ($valor) => $valor !== null && $valor !== ''
        )));

        foreach ($valores as $valor) {
            Parametro::firstOrCreate([
                'function' => $funcao,
                'value' => mb_strtoupper(trim((string) $valor), 'UTF-8'),
            ]);
        }
    }

}