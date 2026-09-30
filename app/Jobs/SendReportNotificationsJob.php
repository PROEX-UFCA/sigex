<?php 

namespace App\Jobs;

use App\Mail\ReportCreatedMail;
use App\Models\Acao;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendReportNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public $report,
        public array $targets
    ) {}

    public function handle(): void
    {
        $actionIds = array_filter(array_column($this->targets, 'id_acao'));
        $userIds = array_filter(array_column($this->targets, 'id_usuario'));

        $acoes = Acao::whereIn('id', $actionIds)->get()->keyBy('id');
        $usuarios = User::whereIn('id', $userIds)->get()->keyBy('id');

        foreach ($this->targets as $item) {
            $email = null;
            $nomeAcao = null;
            $nome = null;

            if (!empty($item['id_acao']) && isset($acoes[$item['id_acao']])) {
                $acao = $acoes[$item['id_acao']];
                $nomeAcao = $acao->titulo;
                $coordenador = $acao->coordenador();
                $email = $coordenador->user->email ?? $coordenador->email ?? null;
                $nome = $coordenador->user->name ?? $coordenador->name ?? null;
            }

            if (empty($email) && !empty($item['id_usuario']) && isset($usuarios[$item['id_usuario']])) {
                $usuario = $usuarios[$item['id_usuario']];
                $email = $usuario->email ?? null;
                $nome = $usuario->name ?? null;
            }

            if ($email) {
                Mail::to($email)->send(new ReportCreatedMail([
                    'nome' => $nome,
                    'titulo_relatorio' => $this->report->titulo,
                    'nome_acao' => $nomeAcao,
                    'data_inicio' => date('d/m/Y H:i:s', strtotime($this->report->data_inicio)),
                    'prazo' => date('d/m/Y H:i:s', strtotime($this->report->prazo)),
                    'url' => route('login'),
                ]));
            }
        }
    }
}