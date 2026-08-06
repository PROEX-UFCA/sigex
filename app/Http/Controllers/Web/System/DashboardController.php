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
    public function index(Request $request)
    {
        $anosSelecionados = $request->input('anos', [date('Y')]);

        $filtroAno = function ($query) use ($anosSelecionados) {
            $query->where(function ($q) use ($anosSelecionados) {
                foreach ($anosSelecionados as $ano) {
                    $anoLimpo = trim($ano);
                    
                    $q->orWhere(function ($subQ) use ($anoLimpo) {
                        $subQ->where('data_inicio', '<=', $anoLimpo . '-12-31')
                             ->where('data_fim', '>=', $anoLimpo . '-01-01');
                    });
                }
            });
        };

        // =====================
        //         MAPA
        // =====================
        
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

        // =====================
        //    TAB DE EQUIPE
        // =====================

        $situacoesValidas = ['EM EXECUÇÃO', 'CONCLUÍDA'];

        $acoesEmAndamento = Acao::whereIn('situacao', $situacoesValidas)
            ->where($filtroAno)
            ->count();

        $bolsasAtivas = Acao::whereIn('situacao', $situacoesValidas)
            ->where($filtroAno)
            ->sum('bolsas_concedidas');

        $acoesPorArea = Acao::whereIn('situacao', $situacoesValidas)
            ->where($filtroAno)
            ->select('area_tematica', DB::raw('count(*) as total'))
            ->groupBy('area_tematica')
            ->orderBy('total', 'desc')
            ->get();

        
        // =====================
        //    TAB DE CENTROS
        // =====================

        $departamentosPrincipais = ['CCAB', 'CCSA', 'CCT', 'FAMED', 'IFE', 'IISCA'];

        $acoesPorDepartamento = Acao::whereIn('situacao', $situacoesValidas)
            ->where($filtroAno)
            ->selectRaw("
                CASE
                    WHEN UPPER(TRIM(centro_departamento_sigla)) IN (?, ?, ?, ?, ?, ?)
                        THEN UPPER(TRIM(centro_departamento_sigla))
                    ELSE 'Demais registros'
                END as departamento_grupo,
                COUNT(*) as total
            ", $departamentosPrincipais)
            ->groupBy('departamento_grupo')
            ->orderByDesc('total')
            ->get();

        // =====================
        //    TAB DE EVENTOS
        // =====================

        $agora = Carbon::now();
        $daquiA7Dias = Carbon::now()->addDays(7);

        $eventosDaSemana = Agenda_Acao::with('acao')
            ->whereBetween('data_hora_inicio', [$agora, $daquiA7Dias])
            ->orderBy('data_hora_inicio', 'asc')
            ->get();

        $filtroAnoEventos = function ($query) use ($anosSelecionados) {
            $query->where(function ($q) use ($anosSelecionados) {
                foreach ($anosSelecionados as $ano) {
                    $anoLimpo = trim($ano);
                    
                    $q->orWhere(function ($subQ) use ($anoLimpo) {
                        $subQ->where('data_hora_inicio', '<=', $anoLimpo . '-12-31 23:59:59')
                             ->where('data_hora_fim', '>=', $anoLimpo . '-01-01 00:00:00');
                    });
                }
            });
        };

        $totalEventos = Agenda_Acao::where($filtroAnoEventos)->count();

        $eventosAgrupados = Agenda_Acao::where($filtroAnoEventos)
            ->selectRaw('MONTH(data_hora_inicio) as mes_numero, COUNT(*) as total')
            ->groupBy('mes_numero')
            ->orderBy('mes_numero')
            ->pluck('total', 'mes_numero')
            ->toArray();

        $mesesNomes = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $distribuicaoMensal = [];

        for ($i = 1; $i <= 12; $i++) {
            $distribuicaoMensal[] = [
                'mes' => $mesesNomes[$i - 1],
                'total' => $eventosAgrupados[$i] ?? 0
            ];
        }

        return view('pages.dashboard.index', [
            'totalAcoes' => $acoesEmAndamento,
            'totalBolsas' => $bolsasAtivas,
            'acoesPorArea' => $acoesPorArea,
            'eventosDaSemana' => $eventosDaSemana,
            'pontosMapa' => json_encode($pontosMapa),
            'totalEventos' => $totalEventos,
            'distribuicaoMensal' => $distribuicaoMensal,
            'acoesPorDepartamento' => $acoesPorDepartamento
        ]);

    }
}