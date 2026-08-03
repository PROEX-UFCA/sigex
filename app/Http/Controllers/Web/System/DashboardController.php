<?php

namespace App\Http\Controllers\Web\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Acao;
use App\Models\Agenda_Acao; 
use App\Models\Resposta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $acoesEmAndamento = Acao::where('situacao', 'EM EXECUÇÃO')->count();

        $bolsasAtivas = Acao::where('situacao', 'EM EXECUÇÃO')->sum('bolsas_concedidas');

        $acoesPorArea = Acao::where('situacao', 'EM EXECUÇÃO')
            ->select('area_tematica', DB::raw('count(*) as total'))
            ->groupBy('area_tematica')
            ->orderBy('total', 'desc')
            ->get();

        $agora = Carbon::now();
        $daquiA7Dias = Carbon::now()->addDays(7);

        $eventosDaSemana = Agenda_Acao::with('acao')
            ->whereBetween('data_hora_inicio', [$agora, $daquiA7Dias])
            ->orderBy('data_hora_inicio', 'asc')
            ->get();

        
        $respostasGeograficas = DB::table('resposta')
            ->join('pergunta', 'resposta.id_pergunta', '=', 'pergunta.id')
            ->join('submissao', 'resposta.id_submissao', '=', 'submissao.id')
            ->join('acao', 'submissao.id_acao', '=', 'acao.id') 
            ->where('pergunta.tipo', 'location') 
            ->where('acao.situacao', 'EM EXECUÇÃO') 
            ->select(
                'resposta.valor', 
                'acao.id as acao_id', 
                'acao.titulo as acao_titulo', 
                'acao.img as acao_img',
                'acao.resumo as acao_resumo',
                'pergunta.enunciado as pergunta_texto'
            )
            ->get();

        $pontosMapa = [];
        foreach ($respostasGeograficas as $resposta) {
            $dadosLocal = json_decode($resposta->valor, true);
            
            if ($dadosLocal && isset($dadosLocal['lat']) && isset($dadosLocal['lng'])) {
                $pontosMapa[] = [
                    'lat' => $dadosLocal['lat'],
                    'lng' => $dadosLocal['lng'],
                    'nome_local' => $dadosLocal['nome'] ?? 'Local não informado',
                    'acao_id' => $resposta->acao_id,
                    'acao_titulo'    => $resposta->acao_titulo,
                    'acao_img'       => $resposta->acao_img,
                    'acao_resumo'    => $resposta->acao_resumo,
                    'pergunta_texto' => $resposta->pergunta_texto
                ];
            }
        }

        return view('pages.dashboard.index', [
            'totalAcoes' => $acoesEmAndamento,
            'totalBolsas' => $bolsasAtivas,
            'acoesPorArea' => $acoesPorArea,
            'eventosDaSemana' => $eventosDaSemana,
            'pontosMapa' => json_encode($pontosMapa)
        ]);
    }
}