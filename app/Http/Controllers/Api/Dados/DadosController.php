<?php

namespace App\Http\Controllers\Api\Dados;

use App\Http\Controllers\Controller;
use App\Jobs\Dados\ExecutarSincronizacaoAcaoJob;
use App\Models\Sincronizacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DadosController extends Controller
{
    public function sincronizarAcoes(Request $dados){
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        try {
            $sincronizacao = Sincronizacao::create([
                'status' => 'pendente',
                'etapa_atual' => 'Aguardando execução - POST',
                'resultados' => [],
                'user_id' => Auth::user()->uuid,
            ]);
    
            ExecutarSincronizacaoAcaoJob::dispatch(
                $sincronizacao->id,
                $dados
            );

            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Lote recebido e adicionado à fila.',
            ], 202);

        } catch (\Exception $th) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => $th->getMessage(),
            ], 500);
        }
    }
}