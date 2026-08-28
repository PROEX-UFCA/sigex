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
            "adicionar_e_editar_grupo",
            "ver_todos_os_logs",
            "ver_seus_logs",

            "ver_usuários",
            "adicionar_usuário",
            "editar_usuário",
            "detalhar_usuário",

            "ver_todas_as_ações",
            "adicionar_ação",
            "importar_ações",
            "editar_ação",
            "ver_suas_ações",
            "detalhar_ação",
            "adicionar_equipe",
            "remover_equipe",
            "adicionar_agenda",
            "editar_agenda",
            "remover_agenda",
            "editar_imagens", 

            "ver_formulários",
            "adicionar_formulários",
            "editar_formulários",
            "deletar_formulários",

            "ver_relatórios",
            "adicionar_relatórios",
            "editar_relatórios",
            "responder_relatórios",
            "monitorar_relatórios",

            "importar_membros",

            "ver_dashboard",
        ];

        $roles = [
            [
                "role" => "Desenvolvimento",
                "permissions" => [
                    "adicionar_e_editar_grupo",
                    "ver_todos_os_logs",
                    "ver_seus_logs",

                    "ver_usuários",
                    "adicionar_usuário",
                    "editar_usuário",
                    "detalhar_usuário",

                    "ver_todas_as_ações",
                    "adicionar_ação",
                    "importar_ações",
                    "editar_ação",
                    "ver_suas_ações",
                    "detalhar_ação",
                    "adicionar_equipe",
                    "remover_equipe",
                    "adicionar_agenda",
                    "editar_agenda",
                    "remover_agenda",
                    "editar_imagens", 

                    "ver_formulários",
                    "adicionar_formulários",
                    "editar_formulários",
                    "deletar_formulários",

                    "ver_relatórios",
                    "adicionar_relatórios",
                    "editar_relatórios",
                    "responder_relatórios",
                    "monitorar_relatórios",

                    'importar_membros',

                    "ver_dashboard",
                ]
            ],
            [
                "role" => "Administrador",
                "permissions" => [
                    "ver_usuários",
                    "adicionar_usuário",
                    "editar_usuário",
                    "detalhar_usuário",

                    "ver_todas_as_ações",
                    "adicionar_ação",
                    "importar_ações",
                    "editar_ação",
                    "ver_suas_ações",
                    "detalhar_ação",
                    "adicionar_equipe",
                    "remover_equipe",
                    "adicionar_agenda",
                    "editar_agenda",
                    "remover_agenda",
                    "editar_imagens", 

                    "ver_formulários",
                    "adicionar_formulários",
                    "editar_formulários",
                    "deletar_formulários",

                    "ver_relatórios",
                    "adicionar_relatórios",
                    "editar_relatórios",
                    "responder_relatórios",
                    "monitorar_relatórios",

                    'importar_membros',

                    "ver_dashboard",
                ]
            ],
            [
                "role" => "Perfil Acadêmico",
                "permissions" => [
                    "ver_suas_ações",
                    "detalhar_ação",
                    "adicionar_equipe",
                    "remover_equipe",
                    "adicionar_agenda",
                    "editar_agenda",
                    "remover_agenda",
                    "editar_imagens", 
                ]
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        foreach ($roles as $key => $role) {
            $role_portal = Role::create([
                'name' => $role['role'],
                'guard_name' => 'web',
            ]);

            foreach ($role as $value) {
                $role_portal->givePermissionTo($roles[$key]['permissions']);
            }

            if($key == 0){
                $user->assignRole($role_portal);
            }
        }

        //////////////////////
        ///// Parâmetros /////
        //////////////////////

        // $parametros = [
        //     "MODALIDADE" => [
        //         "AÇÃO DE FLUXO CONTÍNUO",
        //         "VINCULADA A EDITAL",
        //         "UFCA ITINERANTE",
        //         "PROPE",
        //         "AMPLA CONCORRÊNCIA",
        //     ],
        //     "AREA_TEMATICA" => [
        //         "COMUNICAÇÃO",
        //         "EDUCAÇÃO",
        //         "TECNOLOGIA E PRODUÇÃO",
        //         "SAÚDE",
        //         "TRABALHO",
        //         "CULTURA",
        //         "MEIO AMBIENTE",
        //         "DIREITOS HUMANOS E JUSTIÇA",
        //     ],
        //     "TIPO" => [
        //         "PRESTAÇÃO DE SERVIÇOS",
        //         "EVENTO",
        //         "CURSO",
        //         "PROJETO",
        //         "PROGRAMA",
        //     ],
        //     "PERFIL" => [
        //         "Coordenador",
        //         "Administrador"
        //     ],
        //     "CENTRO_DEPARTAMENTO" => [
        //         "INSTITUTO INTERDISCIPLINAR DE SOCIEDADE, CULTURA E ARTE",
        //         "COORDENADORIA DE GESTÃO DAS AÇÕES",
        //         "CENTRO DE CIÊNCIAS E TECNOLOGIA",
        //         "CENTRO DE CIÊNCIAS SOCIAIS APLICADAS",
        //         "FACULDADE DE MEDICINA",
        //         "CENTRO DE CIÊNCIAS AGRÁRIAS E DA BIODIVERSIDADE",
        //         "INSTITUTO DE FORMAÇÃO DE EDUCADORES",
        //         "PRÓ-REITORIA DE EXTENSÃO",
        //         "DIVISÃO DE GESTÃO PEDAGÓGICA",
        //         "DIVISÃO DE ADMISSIBILIDADE E SELEÇÃO",
        //         "PRÓ-REITORIA DE PLANEJAMENTO E ORÇAMENTO",
        //         "COORDENAÇÃO DO CURSO DE MEDICINA",
        //         "COORDENADORIA DE QUALIDADE DE VIDA NO TRABALHO",
        //         "DIVISÃO DE SAÚDE E NUTRIÇÃO",
        //         "DIVISÃO DE SERVIÇO SOCIAL E ARTICULAÇÃO ESTUDANTIL",
        //         "COORDENADORIA DE INTEGRAÇÃO, FORTALECIMENTO E ASSESSORAMENTO DAS AÇÕES DE EXTENSÃO",
        //         "NÚCLEO DE GESTÃO",
        //         "DIRETORIA DO SISTEMA DE BIBLIOTECAS",
        //         "DIVISÃO DE SISTEMAS DE INFORMAÇÃO EDUCACIONAIS",
        //     ],
        //     "CETAGORIA_COORDENADOR" => [
        //         "Coordenador Geral",
        //         "Coordenador Secundário",
        //         "Bolsista",
        //         "Voluntário",
        //     ]
        // ];

        // foreach($parametros as $key => $parametro){
        //     foreach($parametro as $item){
        //         Parametro::create([
        //             "function" => $key,
        //             "value" => $item
        //         ]);
        //     }
        // }
    }
}
