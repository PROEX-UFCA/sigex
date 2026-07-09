<?php

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Settings\ProfileController;
use App\Http\Controllers\Web\Settings\RolesController;
use App\Http\Controllers\Web\Settings\UsersController;
use App\Http\Controllers\Web\System\ActionController;
use App\Http\Controllers\Web\System\FormController;
use App\Http\Controllers\Web\System\HomeController;
use App\Http\Controllers\Web\System\MembersController;
use App\Http\Controllers\Web\System\ReportController;
use App\Http\Controllers\Web\Tools\LogsController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');


Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('login/enviar', [LoginController::class, 'store'])->name('login.store');

Route::get('login/resetar', [LoginController::class, 'reset'])->name('login.reset');
Route::post('login/solicitar', [LoginController::class, 'send'])->name('login.send');

Route::get('login/editar/{token}', [LoginController::class, 'edit'])->name('login.edit');
Route::get('login/registrar/{token}', [LoginController::class, 'register'])->name('login.register');
Route::post('login/atualizar/{token}', [LoginController::class, 'update'])->name('login.update');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('home.index');

    Route::get('users/sair', [UsersController::class, 'logout'])->name('logout');
    
    Route::get('perfil', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('perfil', [ProfileController::class, 'update'])->name('profile.store');
    
    Route::group(['middleware' => ['auth', 'permission:adicionar_e_editar_grupo']], function () {
        Route::get('gupos', [RolesController::class, 'index'])->name('roles.index');
        Route::post('grupos/adicionar', [RolesController::class, 'store'])->name('roles.store');
        Route::post('grupos/atualizar/{id}', [RolesController::class, 'update'])->name('roles.update');
    });

    Route::group(['middleware' => ['auth', 'permission:ver_usuários']], function () {
        Route::get('usuarios', [UsersController::class, 'index'])->name('users.index');
        Route::get('usuarios/adicionar', [UsersController::class, 'create'])->name('users.create')->middleware(['auth' => 'permission:adicionar_usuário']);
        Route::post('usuarios/adicionar', [UsersController::class, 'store'])->name('users.store')->middleware(['auth' => 'permission:adicionar_usuário']);
        Route::get('usuarios/detalhar/{id}', [UsersController::class, 'show'])->name('users.show')->middleware(['auth' => 'permission:detalhar_usuário']);
        Route::put('usuarios/{id}/grupo', [UsersController::class, 'updateRole'])->name('users.updateRole')->middleware(['auth', 'permission:editar_usuário']);
        Route::post('usuarios/atualizar/{id}', [UsersController::class, 'update'])->name('users.update')->middleware(['auth' => 'permission:editar_usuário']);
        Route::delete('usuarios/deletar/{id}', [UsersController::class, 'destroy'])->name('users.destroy')->middleware(['auth' => 'permission:detalhar_usuário']);
    });

    Route::group(['middleware' => ['auth', 'permission:ver_todos_os_logs']], function () {
        Route::get('atividades', [LogsController::class, 'index'])->name('logs.index');
    });

    Route::group(['middleware' => ['auth', 'permission:ver_seus_logs']], function () {
        Route::get('usuarios/atividade', [LogsController::class, 'getUserLogs'])->name('logs.user');
    });
    
    Route::group(['middleware' => ['auth', 'permission:ver_todas_as_ações']], function () {
        Route::get('acoes', [ActionController::class, 'index'])->name('actions.index');
        
        Route::get('acoes/adicionar', [ActionController::class, 'create'])->name('actions.create')->middleware(['auth' => 'permission:adicionar_ação']);

        Route::post('acoes/adicionar', [ActionController::class, 'store'])->name('actions.store')->middleware(['auth' => 'permission:adicionar_ação']);

        Route::post('acoes/importar', [ActionController::class, 'previewImport'])->name('actions.previewImport')->middleware(['auth' => 'permission:importar_ações']);

        Route::get('acoes/importar', [ActionController::class, 'previewImport'])->name('actions.import')->middleware(['auth' => 'permission:importar_ações']);

        Route::post('acoes/importar/salvar', [ActionController::class, 'storeImport'])->name('actions.storeImport')->middleware(['auth' => 'permission:importar_ações']);
        
        Route::get('acoes/editar/{uuid}', [ActionController::class, 'edit'])->name('actions.edit')->middleware(['auth' => 'permission:editar_ação']);

        Route::post('acoes/atualizar/{uuid}', [ActionController::class, 'update'])->name('actions.update')->middleware(['auth' => 'permission:editar_ação']);

        Route::post('membros/importar', [MembersController::class, 'previewImport'])->name('membros.previewImport')->middleware(['auth' => 'permission:importar_membros']);

        Route::post('membros/importar/salvar', [MembersController::class, 'storeImport'])->name('membros.storeImport')->middleware(['auth' => 'permission:importar_membros']);
    });
        
    Route::group(['middleware' => ['auth', 'permission:ver_suas_ações']], function () {
        Route::get('acoes/minhas', [ActionController::class, 'my'])->name('actions.my');

        Route::get('acoes/relatorio/{uuid}', [ReportController::class, 'report'])->name('actions.report');

        Route::post('acoes/adicionar/banner/{uuid}', [ActionController::class, 'addBanner'])->name('actions.addBanner');

        Route::get('acoes/detalhes/{uuid}', [ActionController::class, 'details'])->name('actions.details')->middleware(['auth' => 'permission:detalhar_ação']);
        
        Route::post('acoes/equipe/{uuid}', [ActionController::class, 'storeTeam'])->name('actions.storeTeam')->middleware(['auth' => 'permission:adicionar_equipe']);
        
        Route::post('acoes/agenda/{uuid}', [ActionController::class, 'storeSchedule'])->name('actions.storeSchedule')->middleware(['auth' => 'permission:adicionar_agenda']);

        Route::delete('membro/deletar/{uuid}', [MembersController::class, 'deleteMember'])->name('members.delete');
    });

    Route::get('formularios', [FormController::class, 'index'])->name('forms.index')->middleware(['auth' => 'permission:ver_formulários']);
    Route::delete('formularios/remover/{uuid}', [FormController::class, 'destroy'])->name('forms.destroy')->middleware(['auth' => 'permission:deletar_formulários']);
    Route::post('formularios/atualizar/{uuid}', [FormController::class, 'update'])->name('forms.update')->middleware(['auth' => 'permission:editar_formulários']);
    
    Route::group(['middleware' => ['auth', 'permission:adicionar_formulários']], function () {
        Route::post('formularios/adicionar', [FormController::class, 'store'])->name('forms.store')->middleware(['auth' => 'permission:deletar_formulários']);
        Route::get('secao/{uuid}', [FormController::class, 'session'])->name('sessions.index');
        Route::delete('secao/deletar/{uuid}', [FormController::class, 'sessionDelete'])->name('sessions.delete');
        Route::post('secao/inserir/{uuid}', [FormController::class, 'sessionStore'])->name('sessions.store');
        Route::post('secao/atualizar/{uuid}', [FormController::class, 'sessionUpdate'])->name('sessions.update');
        Route::post('secao/adicionar/pergunta/{uuid}', [FormController::class, 'storeQuestion'])->name('sessions.storeQuestion');
        Route::delete('secao/deletar/pergunta/{uuid}', [FormController::class, 'deleteQuestion'])->name('sessions.deleteQuestion');
    });

    Route::get('relatorios', [ReportController::class, 'index'])->name('report.index')->middleware(['auth' => 'permission:ver_relatórios']);
    Route::get('relatorios/adicionar', [ReportController::class, 'create'])->name('report.create')->middleware(['auth' => 'permission:adicionar_relatórios']);
    Route::post('relatorios/inserir', [ReportController::class, 'store'])->name('report.store')->middleware(['auth' => 'permission:adicionar_relatórios']);
    Route::post('relatorios/atualizar/{uuid}', [ReportController::class, 'update'])->name('report.update')->middleware(['auth' => 'permission:editar_relatórios']);
    Route::post('respostas/auto-save', [ReportController::class, 'autoSave'])->name('respostas.autosave');
    Route::delete('relatorio/finalizar/{uuid}', [ReportController::class, 'finish'])->name('report.finish');

});
