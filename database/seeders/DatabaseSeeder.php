<?php

namespace Database\Seeders;

use App\Models\Parametro;
use App\Models\User;
use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'status' => true
        ]);

        $permissions = [
            "1" => "adicionar_grupo",
            "2" => "adicionar_usuário",
            "3" => "ver_dashboard",
            "4" => "ver_todos_os_logs",
            "5" => "ver_seus_logs",
        ];

        $role = Role::create([
            'name' => 'Desenvolvimento',
            'guard_name' => 'web',
        ]);

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
            $role->givePermissionTo($permission);
        }

        $user->assignRole($role);

        //////////////////////////////
        ////// TUPLAS DE TESTES //////
        //////////////////////////////

        $agora = Carbon::now();
        $senha = Hash::make('senha123');

        // 1. CRIAR INSTITUIÇÃO EXTERNA
        $idInstituicao = Str::uuid();
        DB::table('instituicao_externa')->insert([
            'id' => $idInstituicao,
            'nome' => 'Escola Dr. Romão Sampaio',
            'cnpj' => '12.345.678/0001-99',
            'cep' => '63000-000',
            'logradouro' => 'Rua da Paz',
            'numero' => '100',
            'complemento' => 'Sede principal',
            'telefone_contato' => '88999999999',
        ]);

        // 2. CRIAR USUÁRIOS
        $idCoordenador = Str::uuid();
        $idEstudante = Str::uuid();
        $idRepresentante = Str::uuid();

        DB::table('users')->insert([
            [
                'uuid' => $idCoordenador,
                'id_instituicao' => null,
                'name' => 'Prof. Alan Turing',
                'email' => 'alan.turing@ufca.edu.br',
                'password' => $senha,
                'status' => 1,
                'cpf' => '111.111.111-11',
                'centro_departamento' => 'Ciência da Computação',
                'matricula_siape' => '1234567',
                'perfil_ativo' => 'COORDENADOR',
                'phone' => '88911111111',
                'created_at' => $agora,
                'updated_at' => $agora,
            ],
            [
                'uuid' => $idEstudante,
                'id_instituicao' => null,
                'name' => 'Otavio',
                'email' => 'otavio@ufca.edu.br',
                'password' => $senha,
                'status' => 1,
                'cpf' => '222.222.222-22',
                'centro_departamento' => 'Ciência da Computação',
                'matricula_siape' => '2024100123',
                'perfil_ativo' => 'ESTUDANTE',
                'phone' => '88922222222',
                'created_at' => $agora,
                'updated_at' => $agora,
            ],
            [
                'uuid' => $idRepresentante,
                'id_instituicao' => $idInstituicao, // Vinculado à instituição externa
                'name' => 'João Representante',
                'email' => 'contato@alianca.org.br',
                'password' => $senha,
                'status' => 1,
                'cpf' => '333.333.333-33',
                'centro_departamento' => null,
                'matricula_siape' => null,
                'perfil_ativo' => 'REPRESENTANTE_EXTERNO',
                'phone' => '88933333333',
                'created_at' => $agora,
                'updated_at' => $agora,
            ]
        ]);

        $idAcao1 = Str::uuid();
        $idAcao2 = Str::uuid();
        $idAcao3 = Str::uuid();
        $idAcao4 = Str::uuid();

        DB::table('acao')->insert([
            [
                'id' => $idAcao1,
                'id_coordenador' => $idCoordenador,
                'id_atividade' => 'ATIV-001',
                'id_projeto' => 'PROJ-EXT-2026',
                'titulo' => 'Oficina de Lógica e Algoritmos para a Comunidade',
                'centro_departamento' => 'Ciência da Computação',
                'data_inicio' => Carbon::create(2026, 4, 1)->toDateString(),
                'data_fim' => Carbon::create(2026, 11, 30)->toDateString(),
                'ano' => 2026,
                'tipo_acao' => 'Curso',
                'area_tematica' => 'Educação e Tecnologia',
                'modalidade' => 'Ampla Concorrência',
            ],
            [
                'id' => $idAcao2,
                'id_coordenador' => $idCoordenador,
                'id_atividade' => 'ATIV-002',
                'id_projeto' => 'PROJ-EXT-2026',
                'titulo' => 'Maratona de Programação: Estruturas de Dados e Grafos',
                'centro_departamento' => 'Ciência da Computação',
                'data_inicio' => Carbon::create(2026, 5, 10)->toDateString(),
                'data_fim' => Carbon::create(2026, 5, 12)->toDateString(),
                'ano' => 2026,
                'tipo_acao' => 'Evento',
                'area_tematica' => 'Tecnologia e Produção',
                'modalidade' => 'Ampla Concorrência',
            ],
            [
                'id' => $idAcao3,
                'id_coordenador' => $idCoordenador,
                'id_atividade' => 'ATIV-003',
                'id_projeto' => 'PROJ-DEV-2026',
                'titulo' => 'Desenvolvimento de Sistemas Web e Multi-tenant',
                'centro_departamento' => 'Ciência da Computação',
                'data_inicio' => Carbon::create(2026, 8, 1)->toDateString(),
                'data_fim' => Carbon::create(2026, 12, 15)->toDateString(),
                'ano' => 2026,
                'tipo_acao' => 'Projeto',
                'area_tematica' => 'Trabalho',
                'modalidade' => 'Ampla Concorrência',
            ],
            [
                'id' => $idAcao4,
                'id_coordenador' => $idCoordenador,
                'id_atividade' => 'ATIV-004',
                'id_projeto' => "PROJ-DEV-2029",
                'titulo' => 'Seminário de Sistemas Operacionais Avançados',
                'centro_departamento' => 'Ciência da Computação',
                'data_inicio' => Carbon::create(2026, 9, 20)->toDateString(),
                'data_fim' => Carbon::create(2026, 9, 21)->toDateString(),
                'ano' => 2026,
                'tipo_acao' => 'Evento',
                'area_tematica' => 'Tecnologia e Produção',
                'modalidade' => 'Ampla Concorrência',
            ]
        ]);

        // 4. ADICIONAR MEMBROS NAS EQUIPES DAS AÇÕES
        DB::table('equipe_acao')->insert([
            // Equipe Ação 1
            ['id' => Str::uuid(), 'id_acao' => $idAcao1, 'id_usuario' => $idCoordenador, 'categoria' => 'Coordenador'],
            ['id' => Str::uuid(), 'id_acao' => $idAcao1, 'id_usuario' => $idEstudante, 'categoria' => 'Bolsista'],

            // Equipe Ação 2 (Maratona)
            ['id' => Str::uuid(), 'id_acao' => $idAcao2, 'id_usuario' => $idCoordenador, 'categoria' => 'Coordenador'],
            ['id' => Str::uuid(), 'id_acao' => $idAcao2, 'id_usuario' => $idEstudante, 'categoria' => 'Voluntário'],

            // Equipe Ação 3 (Sistemas Web)
            ['id' => Str::uuid(), 'id_acao' => $idAcao3, 'id_usuario' => $idCoordenador, 'categoria' => 'Coordenador'],
            ['id' => Str::uuid(), 'id_acao' => $idAcao3, 'id_usuario' => $idEstudante, 'categoria' => 'Bolsista'],

            // Equipe Ação 4 (Sistemas Operacionais)
            ['id' => Str::uuid(), 'id_acao' => $idAcao4, 'id_usuario' => $idCoordenador, 'categoria' => 'Coordenador'],
        ]);

        // 5. CRIAR EVENTOS NA AGENDA DAS AÇÕES
        DB::table('agenda_acao')->insert([
            // Eventos da Ação 1
            [
                'id' => Str::uuid(),
                'id_acao' => $idAcao1,
                'titulo_evento' => 'Aula Inaugural: Introdução ao Pensamento Computacional',
                'data_hora_inicio' => Carbon::create(2026, 4, 5, 14, 0, 0),
                'data_hora_fim' => Carbon::create(2026, 4, 5, 17, 0, 0),
                'local_formato' => 'Auditório Central',
                'descricao' => 'Primeira aula de nivelamento e apresentação do curso.',
            ],
            // Eventos da Ação 2 (Maratona)
            [
                'id' => Str::uuid(),
                'id_acao' => $idAcao2,
                'titulo_evento' => 'Competição de Algoritmos (Fase Única)',
                'data_hora_inicio' => Carbon::create(2026, 5, 10, 8, 0, 0),
                'data_hora_fim' => Carbon::create(2026, 5, 10, 18, 0, 0),
                'local_formato' => 'Laboratórios 1, 2 e 3',
                'descricao' => 'Resolução de problemas de grafos e programação dinâmica.',
            ],
            // Eventos da Ação 3 (Sistemas Web)
            [
                'id' => Str::uuid(),
                'id_acao' => $idAcao3,
                'titulo_evento' => 'Workshop: Arquitetura de Banco de Dados',
                'data_hora_inicio' => Carbon::create(2026, 8, 15, 19, 0, 0),
                'data_hora_fim' => Carbon::create(2026, 8, 15, 22, 0, 0),
                'local_formato' => 'Google Meet',
                'descricao' => 'Definição da estrutura do banco para o novo sistema.',
            ]
        ]);

        // 6. CRIAR DISPONIBILIDADE NA AGENDA DA INSTITUIÇÃO EXTERNA
        DB::table('instituicao_externa_agenda')->insert([
            [
                'id' => Str::uuid(),
                'id_instituicao' => $idInstituicao,
                'data_disponivel' => Carbon::create(2026, 5, 10)->toDateString(),
                'hora_inicio' => '13:00:00',
                'hora_fim' => '17:00:00',
                'observacao' => 'Sala de computadores disponível para 20 pessoas.',
            ]
        ]);

        // 7. REGISTRAR O INTERESSE (MATCH)
        DB::table('interesse_acao')->insert([
            'id' => Str::uuid(),
            'id_instituicao' => $idInstituicao,
            'id_acao' => $idAcao1,
        ]);

        //////////////////////
        ///// Parâmetros /////
        //////////////////////

        $parametros = [
            "MODALIDADE" => [
                "AÇÃO DE FLUXO CONTÍNUO",
                "VINCULADA A EDITAL",
                "UFCA ITINERANTE",
                "PROPE",
                "AMPLA CONCORRÊNCIA",
            ],
            "AREA_TEMATICA" => [
                "COMUNICAÇÃO",
                "EDUCAÇÃO",
                "TECNOLOGIA E PRODUÇÃO",
                "SAÚDE",
                "TRABALHO",
                "CULTURA",
                "MEIO AMBIENTE",
                "DIREITOS HUMANOS E JUSTIÇA",
            ],
            "TIPO" => [
                "PRESTAÇÃO DE SERVIÇOS",
                "EVENTO",
                "CURSO",
                "PROJETO",
                "PROGRAMA",
            ],
            "PERFIL" => [
                "Coordenador",
                "Administrador"
            ],
            "CENTRO_DEPARTAMENTO" => [
                "INSTITUTO INTERDISCIPLINAR DE SOCIEDADE, CULTURA E ARTE",
                "COORDENADORIA DE GESTÃO DAS AÇÕES",
                "CENTRO DE CIÊNCIAS E TECNOLOGIA",
                "CENTRO DE CIÊNCIAS SOCIAIS APLICADAS",
                "FACULDADE DE MEDICINA",
                "CENTRO DE CIÊNCIAS AGRÁRIAS E DA BIODIVERSIDADE",
                "INSTITUTO DE FORMAÇÃO DE EDUCADORES",
                "PRÓ-REITORIA DE EXTENSÃO",
                "DIVISÃO DE GESTÃO PEDAGÓGICA",
                "DIVISÃO DE ADMISSIBILIDADE E SELEÇÃO",
                "PRÓ-REITORIA DE PLANEJAMENTO E ORÇAMENTO",
                "COORDENAÇÃO DO CURSO DE MEDICINA",
                "COORDENADORIA DE QUALIDADE DE VIDA NO TRABALHO",
                "DIVISÃO DE SAÚDE E NUTRIÇÃO",
                "DIVISÃO DE SERVIÇO SOCIAL E ARTICULAÇÃO ESTUDANTIL",
                "COORDENADORIA DE INTEGRAÇÃO, FORTALECIMENTO E ASSESSORAMENTO DAS AÇÕES DE EXTENSÃO",
                "NÚCLEO DE GESTÃO",
                "DIRETORIA DO SISTEMA DE BIBLIOTECAS",
                "DIVISÃO DE SISTEMAS DE INFORMAÇÃO EDUCACIONAIS",
            ]
        ];

        foreach($parametros as $key => $parametro){
            foreach($parametro as $item){
                Parametro::create([
                    "function" => $key, 
                    "value" => $item 
                ]);
            }
        }
    }
}
