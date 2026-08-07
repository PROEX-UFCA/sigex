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
        $ano = $request->input('ano', date('Y'));

        $filtroAno = function ($query) use ($ano) {
            $query->where(function ($q) use ($ano) {
                    $anoLimpo = trim($ano);
                    
                    $q->orWhere(function ($subQ) use ($anoLimpo) {
                        $subQ->where('data_inicio', '<=', $anoLimpo . '-12-31')
                             ->where('data_fim', '>=', $anoLimpo . '-01-01');
                    });
            });
        };

    //MAPA
        
        $respostasGeograficas = DB::table('resposta')
            ->join('pergunta', 'resposta.id_pergunta', '=', 'pergunta.id')
            ->join('submissao', 'resposta.id_submissao', '=', 'submissao.id')
            ->join('acao', 'submissao.id_acao', '=', 'acao.id') 
            ->where('pergunta.tipo', 'location') 
            ->where('acao.situacao', 'EM EXECUÇÃO') //pensando em talvez mudar essa checagem pra checagem de ano.
            ->whereNull('acao.deleted_at') 
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

    //EQUIPE

        $situacoesValidas = ['EM EXECUÇÃO', 'CONCLUÍDA'];

        $acoesEmAndamento = Acao::whereIn('situacao', $situacoesValidas)
            ->where($filtroAno)
            ->whereNull('acao.deleted_at')
            ->count();

        $bolsasAtivas = Acao::whereIn('situacao', $situacoesValidas)
            ->where($filtroAno)
            ->whereNull('acao.deleted_at')
            ->sum('bolsas_concedidas');

        $filtroAnoEquipe = function ($query) use ($ano) {
            $query->where(function ($q) use ($ano) {
                    $anoLimpo = trim($ano);
                    $q->orWhere(function ($subQ) use ($anoLimpo) {
                        $subQ->where('equipe_acao.data_inicio', '<=', $anoLimpo . '-12-31')
                             ->where('equipe_acao.data_fim', '>=', $anoLimpo . '-01-01');
                    });
            });
        };

        $equipeBase = DB::table('equipe_acao')
            ->join('acao', 'equipe_acao.id_acao', '=', 'acao.id')
            ->whereIn('acao.situacao', $situacoesValidas)
            ->where('equipe_acao.tipo_vinculo', 'AÇÃO')
            ->whereNull('acao.deleted_at')
            ->whereNull('equipe_acao.deleted_at') 
            ->where($filtroAnoEquipe);

        $regraUnica = "COUNT(DISTINCT COALESCE(equipe_acao.id_pessoa, equipe_acao.id_usuario))";

        $totalPessoas = (clone $equipeBase)
            ->selectRaw("{$regraUnica} as total")
            ->value('total');

        $pessoasPorTipo = (clone $equipeBase)
            ->select('equipe_acao.tipo_membro', DB::raw("{$regraUnica} as total"))
            ->groupBy('equipe_acao.tipo_membro')
            ->orderBy('total', 'desc')
            ->get();

    //AREAS

        $acoesPorArea = Acao::whereIn('situacao', $situacoesValidas)
            ->where($filtroAno)
            ->whereNull('acao.deleted_at')
            ->select('area_tematica', DB::raw('count(*) as total'))
            ->groupBy('area_tematica')
            ->orderBy('total', 'desc')
            ->get();

        
    //CENTROS

        $departamentosPrincipais = ['CCAB', 'CCSA', 'CCT', 'FAMED', 'IFE', 'IISCA'];

        $acoesPorDepartamento = Acao::whereIn('situacao', $situacoesValidas)
            ->where($filtroAno)
            ->whereNull('acao.deleted_at')
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

    //EVENTOS

        $agora = Carbon::now();
        $daquiA7Dias = Carbon::now()->addDays(7);

        $eventosDaSemana = Agenda_Acao::with('acao')
            ->whereBetween('data_hora_inicio', [$agora, $daquiA7Dias])
            ->whereNull('agenda_acao.deleted_at')
            ->orderBy('data_hora_inicio', 'asc')
            ->get();

        $filtroAnoEventos = function ($query) use ($ano) {
            $query->where(function ($q) use ($ano) {
                    $anoLimpo = trim($ano);
                    
                    $q->orWhere(function ($subQ) use ($anoLimpo) {
                        $subQ->where('data_hora_inicio', '<=', $anoLimpo . '-12-31 23:59:59')
                             ->where('data_hora_fim', '>=', $anoLimpo . '-01-01 00:00:00');
                    });
            });
        };

        $totalEventos = Agenda_Acao::where($filtroAnoEventos)->count();

        $eventosAgrupados = Agenda_Acao::where($filtroAnoEventos)
            ->whereNull('agenda_acao.deleted_at')
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
            'acoesPorDepartamento' => $acoesPorDepartamento,
            'totalPessoas' => $totalPessoas,
            'pessoasPorTipo' => $pessoasPorTipo
        ]);

    }
}