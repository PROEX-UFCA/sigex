<?php

namespace App\Jobs\Dados;

use App\Models\Acao;
use App\Models\Equipe_Acao;
use App\Models\Parametro;
use App\Models\Sincronizacao;
use App\Models\User;
use Throwable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class ExecutarSincronizacaoMembrosJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 3600;
    public int $tries = 3;
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
        $indiceAtual = 0;

        try {
            $sincronizacao->update([
                'status' => 'executando',
                'iniciado_em' => now(),
                'etapa_atual' => $etapaAtual,
                'finalizado_em' => null,
                'erro' => null,
            ]);

            $etapaAtual = 'Sincronizando membros - POST';

            $sincronizacao->update([
                'etapa_atual' => $etapaAtual,
            ]);

            $dados = $this->dados;

            $tiposMembrosReq = [];
            $categoriasReq = [];
            $sexosReq = [];
            $statusReq = [];
            
            foreach ($dados as $indice => $item) {
                $indiceAtual = $indice;
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

                $id_usuario = null;

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
                    $id_usuario = $usuario->uuid;
                }

                $id_acao = Acao::where('id_projeto', $item->id_projeto)->first()->id;

                if($id_acao){
                    Equipe_Acao::updateOrCreate(
                        [
                            'id_projeto' => $item->id_projeto, 
                            'id_pessoa' => $item->id_pessoa, 
                        ],
                        [
                            "id_usuario" => $id_usuario,
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
                'etapa_atual' => 'Concluída - Membros (POST)',
                'resultados' => $resultados,
                'finalizado_em' => now(),
                'erro' => null,
            ]);

        } catch (Throwable $e) {
            report($e);

            $sincronizacao->update([
                'status' => 'falhou',
                'etapa_atual' => $etapaAtual,
                'resultados' => array_merge($resultados, [
                    'indice_ultimo_registro' => $indiceAtual,
                    'total_registros' => count($this->dados),
                ]),
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