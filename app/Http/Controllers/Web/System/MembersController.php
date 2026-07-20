<?php

namespace App\Http\Controllers\Web\System;

use App\Http\Controllers\Controller;
use App\Models\Acao;
use App\Models\Equipe_Acao;
use App\Models\Parametro;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MembersController extends Controller
{
    private $userId;

    public function __construct()
    {
        $this->userId = Auth::user()->id;
    }

    public function previewImport(Request $request)
    {
        $cacheKey = 'import_preview_members_' . $this->userId;

        if ($request->hasFile('csv')) {
            $request->validate([
                'csv' => 'required|file|mimes:csv,txt|max:10240'
            ], [
                'csv.mimes' => 'O arquivo precisa ser um CSV válido.'
            ]);

            $file = $request->file('csv');
            $handle = fopen($file->getRealPath(), "r");

            $headerLine = fgets($handle);
            if ($headerLine === false) {
                fclose($handle);
                return redirect()->back()->with("error", "Arquivo CSV vazio ou inválido.");
            }

            $delimiter = substr_count($headerLine, ';') > substr_count($headerLine, ',') ? ';' : ',';

            $membersData = [];
            $rowIndex = 1;
            $duplicadosIgnorados = 0;

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $row = array_map(fn($field) => trim(mb_convert_encoding($field, 'UTF-8', 'auto')), $row);

                $checkData = [
                    'id_projeto' => $row[0] ?? null,
                    'id_pessoa' => $row[1] ?? null,
                    'categoria_membro' => $row[4] ?? null,
                    // 'tipo_membro' => $row[3] ?? null,
                    // 'nome' => $row[2] ?? null,
                    // 'email' => $row[5] ?? null,
                    // 'status' => $row[6] ?? null,
                    // 'data_inicio' => $row[7] ?? null,
                    // 'data_fim' => $row[8] ?? null,
                    // 'tipo_vinculo' => $row[9] ?? null,
                ];

                if (Equipe_Acao::where($checkData)->exists()) {
                    $duplicadosIgnorados++;
                    $rowIndex++;
                    continue; 
                }

                $errors = [];

                if (empty($row[0])) $errors['id_projeto'] = 'Campo obrigatório';
                if (empty($row[1])) $errors['id_pessoa'] = 'Campo obrigatório';
                if (empty($row[2])) $errors['nome'] = 'Campo obrigatório';
                if (empty($row[3])) $errors['tipo_membro'] = 'Campo obrigatório';
                if (empty($row[4])) $errors['categoria_membro'] = 'Campo obrigatório';
                if (empty($row[5])) $errors['email'] = 'Campo obrigatório';
                if (empty($row[6])) $errors['status'] = 'Campo obrigatório';
                if (empty($row[7])) $errors['data_inicio'] = 'Campo obrigatório';
                if (empty($row[8])) $errors['data_fim'] = 'Campo obrigatório';
                if (empty($row[9])) $errors['tipo_vinculo'] = 'Campo obrigatório';

                $membersData[$rowIndex] = [
                    'id_projeto' => $row[0] ?? null,
                    'id_pessoa' => $row[1] ?? null,
                    'nome' => $row[2] ?? null,
                    'tipo_membro' => $row[3] ?? null,
                    'categoria_membro' => $row[4] ?? null,
                    'email' => $row[5] ?? null,
                    'status' => $row[6] ?? null,
                    'data_inicio' => $row[7] ?? null,
                    'data_fim' => $row[8] ?? null,
                    'tipo_vinculo' => $row[9] ?? null,
                    'errors' => $errors,
                    'row_index' => $rowIndex
                ];
                $rowIndex++;
            }
            fclose($handle);

            Cache::put($cacheKey, [
                'members' => $membersData,
                'duplicados_members' => $duplicadosIgnorados
            ], now()->addHours(2));

        } else {
            $cacheData = Cache::get($cacheKey);
            if (!$cacheData || empty($cacheData['members'])) {
                return redirect()->route('actions.index')->with('error', 'A sessão de importação expirou ou não há dados.');
            }
            
            $allMembers = $cacheData['members'];
            $duplicadosIgnorados = $cacheData['duplicados_members'];
            $cacheFoiAtualizado = false;

            if ($request->filled('deleted_indexes')) {
                $deletedIndexes = explode(',', $request->deleted_indexes);
                foreach ($deletedIndexes as $idx) {
                    if (isset($allMembers[$idx])) {
                        unset($allMembers[$idx]);
                        $cacheFoiAtualizado = true;
                    }
                }
            }

            if ($request->has('members')) {
                foreach ($request->members as $index => $submittedData) {
                    if (isset($allMembers[$index])) {
                        $allMembers[$index] = array_merge($allMembers[$index], $submittedData);
                        
                        $errors = [];

                        if (empty($allMembers[$index]['id_projeto'])) $errors['id_projeto'] = 'Campo obrigatório';
                        if (empty($allMembers[$index]['id_pessoa'])) $errors['id_pessoa'] = 'Campo obrigatório';
                        if (empty($allMembers[$index]['nome'])) $errors['nome'] = 'Campo obrigatório';
                        if (empty($allMembers[$index]['tipo_membro'])) $errors['tipo_membro'] = 'Campo obrigatório';
                        if (empty($allMembers[$index]['categoria_membro'])) $errors['categoria_membro'] = 'Campo obrigatório';
                        if (empty($allMembers[$index]['email'])) $errors['email'] = 'Campo obrigatório';
                        if (empty($allMembers[$index]['status'])) $errors['status'] = 'Campo obrigatório';
                        if (empty($allMembers[$index]['data_inicio'])) $errors['data_inicio'] = 'Campo obrigatório';
                        if (empty($allMembers[$index]['data_fim'])) $errors['data_fim'] = 'Campo obrigatório';
                        if (empty($allMembers[$index]['tipo_vinculo'])) $errors['tipo_vinculo'] = 'Campo obrigatório';

                        $allMembers[$index]['errors'] = $errors;
                        $cacheFoiAtualizado = true;
                    }
                }
            }

            if ($cacheFoiAtualizado) {
                Cache::put($cacheKey, [
                    'members' => $allMembers,
                    'duplicados_members' => $duplicadosIgnorados
                ], now()->addHours(2));
            }

            $membersData = $allMembers;
        }

        $collection = collect($membersData)->sortByDesc(fn($member) => !empty($member['errors']))->values();
        
        $totalErrors = $collection->filter(fn($item) => count($item['errors']) > 0)->count();

        $perPage = 50;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $paginatedMembers = new LengthAwarePaginator(
            $currentItems, 
            $collection->count(), 
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('pages.members.preview', compact('paginatedMembers', 'totalErrors', 'duplicadosIgnorados'));
    }

    public function storeImport(Request $request)
    {
        $cacheKey = 'import_preview_members_' . $this->userId;
        $cacheData = Cache::get($cacheKey);

        if (!$cacheData || empty($cacheData['members'])) {
            return redirect()->route('actions.index')->with('error', 'Sessão de importação expirada ou sem dados válidos.');
        }

        $allMembers = $cacheData['members'];
        $duplicadosIgnorados = $cacheData['duplicados_members'];

        if ($request->filled('deleted_indexes')) {
            $deletedIndexes = explode(',', $request->deleted_indexes);
            foreach ($deletedIndexes as $idx) {
                unset($allMembers[$idx]);
            }
        }

        if ($request->has('members')) {
            foreach ($request->members as $index => $submittedData) {
                if (isset($allMembers[$index])) {
                    $allMembers[$index] = array_merge($allMembers[$index], $submittedData);
                    
                    $errors = [];
                    $requiredFields = ['id_projeto', 'id_pessoa', 'nome', 'tipo_membro', 'categoria_membro', 'email', 'status', 'data_inicio', 'data_fim', 'tipo_vinculo'];
                    
                    foreach ($requiredFields as $field) {
                        if (empty($allMembers[$index][$field])) {
                            $errors[$field] = 'Campo obrigatório';
                        }
                    }
                    $allMembers[$index]['errors'] = $errors;
                }
            }
        }

        Cache::put($cacheKey, ['members' => $allMembers, 'duplicados' => $duplicadosIgnorados], now()->addHours(2));

        $totalErrors = collect($allMembers)->filter(fn($item) => count($item['errors']) > 0)->count();

        if ($totalErrors > 0) {
            return redirect()->back()->with('error', "Não foi possível salvar. Ainda existem {$totalErrors} linha(s) com erro no lote. Corrija ou exclua as linhas.");
        }

        if (empty($allMembers)) {
            return redirect()->route('actions.index')->with('info', 'Nenhuma linha restou para ser importada.');
        }

        DB::beginTransaction();
        try {
            $agora = now();

            $emails = array_filter(array_unique(array_map('strtolower', array_column($allMembers, 'email'))));
            $projetosIds = array_filter(array_unique(array_column($allMembers, 'id_projeto')));
            
            $tiposMembrosReq = array_filter(array_unique(array_map(fn($v) => mb_strtoupper(trim($v), 'UTF-8'), array_column($allMembers, 'tipo_membro'))));
            $categoriasReq = array_filter(array_unique(array_map(fn($v) => mb_strtoupper(trim($v), 'UTF-8'), array_column($allMembers, 'categoria_membro'))));
            $vinculosReq = array_filter(array_unique(array_map(fn($v) => mb_strtoupper(trim($v), 'UTF-8'), array_column($allMembers, 'tipo_vinculo'))));
            $statusReq = array_filter(array_unique(array_map(fn($v) => mb_strtoupper(trim($v), 'UTF-8'), array_column($allMembers, 'status'))));

            $usuariosExistentes = User::whereIn('email', $emails)->pluck('uuid', 'email')->toArray();
            $acoesExistentes = Acao::whereIn('id_projeto', $projetosIds)->pluck('id', 'id_projeto')->toArray();
            
            $this->garantirParametrosEmLote('TIPO_MEMBRO', $tiposMembrosReq);
            $this->garantirParametrosEmLote('CATEGORIA_MEMBRO', $categoriasReq);
            $this->garantirParametrosEmLote('TIPO_VINCULO', $vinculosReq);
            $this->garantirParametrosEmLote('STATUS_MEMBROS', $statusReq);

            $emailsFaltantes = array_diff($emails, array_keys($usuariosExistentes));
            
            if (!empty($emailsFaltantes)) {
                $novosUsuarios = [];
                $emailParaNome = array_column($allMembers, 'nome', 'email');

                // 3.1 Prepara os dados e gera os UUIDs
                foreach ($emailsFaltantes as $email) {
                    $novosUsuarios[] = [
                        'uuid' => (string) Str::uuid(),
                        'name' => mb_strtoupper(trim($emailParaNome[$email] ?? 'USUÁRIO IMPORTADO'), 'UTF-8'),
                        'email' => $email,
                        'status' => 0,
                        'created_at' => $agora,
                        'updated_at' => $agora,
                    ];
                }

                // 3.2 Insere todos os usuários de uma vez (Não temos os IDs auto-incremento ainda)
                User::insert($novosUsuarios);

                // 3.3 Busca os usuários recém-criados para pegar os IDs verdadeiros
                $usuariosInseridos = User::whereIn('email', $emailsFaltantes)->get(['id', 'uuid', 'email']);
                
                $roleCoordenador = \Spatie\Permission\Models\Role::findByName('Perfil Acadêmico'); 
                $roleInserts = []; 

                foreach ($usuariosInseridos as $user) {
                    // Preenche o array do Spatie usando o ID auto-incremento correto
                    $roleInserts[] = [
                        'role_id' => $roleCoordenador->id,
                        'model_type' => User::class,
                        'model_id' => $user->id // <--- AQUI VAI O ID INTEIRO!
                    ];

                    // Adiciona ao array local mapeando o email para o UUID (para o insert final na Equipe_Acao)
                    $usuariosExistentes[$user->email] = $user->uuid; 
                }

                // 3.4 Insere as roles em lote com os IDs corretos
                DB::table('model_has_roles')->insert($roleInserts);
            }
            // -------------------------------------------------------------------
            // PASSO 4: MONTAR ARRAY FINAL DE INSERÇÃO NA MEMÓRIA
            // -------------------------------------------------------------------
            $membrosParaInserir = [];

            foreach ($allMembers as $linha) {
                $email = strtolower(trim($linha['email'] ?? ''));
                $id_user = $usuariosExistentes[$email] ?? null;
                $id_acao = $acoesExistentes[$linha['id_projeto']] ?? null;

                $membrosParaInserir[] = [
                    'id' => (string) Str::uuid(),
                    'id_usuario' => $id_user,
                    'id_acao' => $id_acao,
                    'id_projeto' => $linha['id_projeto'] ?? null,
                    'id_pessoa' => $linha['id_pessoa'] ?? null, 
                    'tipo_membro' => mb_strtoupper(trim($linha['tipo_membro'] ?? ''), 'UTF-8'),
                    'categoria_membro' => mb_strtoupper(trim($linha['categoria_membro'] ?? ''), 'UTF-8'),
                    'status' => $linha['status'] ?? null,
                    'data_inicio' => $linha['data_inicio'] ?? null,
                    'data_fim' => $linha['data_fim'] ?? null,
                    'tipo_vinculo' => mb_strtoupper(trim($linha['tipo_vinculo'] ?? ''), 'UTF-8'),
                    'created_at' => $agora,
                    'updated_at' => $agora,
                ];
            }

            // -------------------------------------------------------------------
            // PASSO 5: INSERÇÃO EM CHUNKS (Previne estouro do banco)
            // -------------------------------------------------------------------
            if (!empty($membrosParaInserir)) {
                // Insere de 1000 em 1000 registros
                collect($membrosParaInserir)->chunk(1000)->each(function ($chunk) {
                    Equipe_Acao::insert($chunk->toArray());
                });

                $usuarioLogado = $this->userId;
                $membroReferencia = Equipe_Acao::latest('created_at')->first(); // Pega apenas 1 para o log
                
                if ($membroReferencia && $usuarioLogado) {
                    activity()
                        ->causedBy($usuarioLogado)
                        ->performedOn($membroReferencia)
                        ->event('importado')
                        ->withProperties([
                            'attributes' => [
                                'message' => 'Uma importação em lote de ' . count($membrosParaInserir) . ' membros foi realizada.',
                            ]
                        ])
                        ->log('importado');
                }
            }

            DB::commit(); // CONFIRMA TUDO
            Cache::forget($cacheKey);

            return redirect()->route('actions.index')->with("success", "Membros importados e salvos com sucesso!");

        } catch (\Exception $e) {
            DB::rollBack(); // DESFAZ EM CASO DE ERRO
            return redirect()->route('actions.index')->with("error", "Erro ao salvar os dados: " . $e->getMessage());
        }
    }

    private function garantirParametrosEmLote($functionName, $valoresUnicos)
    {
        if (empty($valoresUnicos)) return;

        $existentes = Parametro::where('function', $functionName)
            ->whereIn('value', $valoresUnicos)
            ->pluck('value')
            ->toArray();

        $faltantes = array_diff($valoresUnicos, $existentes);

        if (!empty($faltantes)) {
            $agora = now();
            $inserts = array_map(fn($val) => [
                'id' => (string) Str::uuid(),
                'function' => $functionName,
                'value' => $val,
                'created_at' => $agora,
                'updated_at' => $agora
            ], $faltantes);

            Parametro::insert($inserts);
        }
    }

    public function deleteMember($uuid){
        try {
            Equipe_Acao::findOrFail($uuid)->delete();

            return redirect()->back()->with('success', 'Membro deletado com sucesso.');

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Erro ao tentar deletar membro, tente novamente mais tarde.');
        }
    }
}
