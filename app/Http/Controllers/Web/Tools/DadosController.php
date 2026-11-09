<?php

namespace App\Http\Controllers\Web\Tools;

use App\Http\Controllers\Controller;
use App\Jobs\ExecutarSincronizacaoJob;
use App\Models\Sincronizacao;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DadosController extends Controller
{
    private $data = [];

    public function index()
    {
        $sincronizacoes = Sincronizacao::latest('id')
            ->paginate(20);

        return view('pages.dados.index', compact(
            'sincronizacoes'
        ));
    }

    public function iniciar(): RedirectResponse
    {
        $emAndamento = Sincronizacao::whereIn('status', [
            'pendente',
            'executando',
        ])->exists();

        if ($emAndamento) {
            return redirect()
                ->back()
                ->with('error', 'Já existe uma sincronização em andamento.');
        }

        $sincronizacao = Sincronizacao::create([
            'status' => 'pendente',
            'etapa_atual' => 'Aguardando execução',
            'resultados' => [],
            'user_id' => Auth::user()->uuid,
        ]);

        ExecutarSincronizacaoJob::dispatch(
            $sincronizacao->id
        );

        return redirect()
            ->back()
            ->with('success', 'Sincronização adicionada à fila.');
    }

    public function status(): JsonResponse
    {
        $sincronizacoes = Sincronizacao::latest('id')
            ->paginate(20);

        return response()->json($sincronizacoes);
    }
}

