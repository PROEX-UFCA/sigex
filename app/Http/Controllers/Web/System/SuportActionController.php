<?php

namespace App\Http\Controllers\Web\System;

use App\Http\Controllers\Controller;
use App\Models\Acao;
use Illuminate\Http\Request;

class SuportActionController extends Controller
{
    private $data = [];

    public function indexEditLot(){
        $this->data['actions'] = Acao::where('situacao', 'EM EXECUÇÃO')->paginate(30);

        return view('pages.actions.lot', $this->data);
    }

    public function storeEditLot(){
        try {
            $actions = Acao::where('situacao', 'EM EXECUÇÃO')->get();
    
            foreach ($actions as $action) {
                $action->situacao = 'CONCLUÍDA';
                $action->save();
    
                foreach ($action->equipe as $membro) {
                    $membro->status = 'FINALIZADO';
                    $membro->save();
                }
            }

            return redirect()->back()->with('success', 'Ações concluidas com sucesso.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Erro ao concluir ações, tente novamente mais tarde.');
        }

    }
}
