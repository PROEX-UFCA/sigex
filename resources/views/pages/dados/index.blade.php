@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body">
    <div class="">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Sincronização de dados</h2>
                <p class="text-muted mb-0">
                    Acompanhe as execuções do sistema.
                </p>
            </div>

            <form method="POST" action="{{ route('dados.sincronizar') }}">
                @csrf
                <button type="submit" class="btn btn-primary" id="btnSincronizar">
                    <i class="fas fa-sync-alt"></i>
                    Sincronizar
                </button>
            </form>
        </div>

        <div id="listaSincronizacoes" class="row g-3">
            @forelse($sincronizacoes as $s)
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <strong>
                                Sincronização #{{ $s->id }}
                            </strong>

                            <span class="badge
                @if($s->status === 'concluida') bg-success
                @elseif($s->status === 'falhou') bg-danger
                @elseif($s->status === 'executando') bg-primary
                @else bg-secondary
                @endif">
                                {{ ucfirst($s->status) }}
                            </span>
                        </div>

                        <p class="mb-2">
                            <strong>Etapa:</strong>
                            {{ $s->etapa_atual }}
                        </p>

                        <p class="small text-muted mb-2">
                            Início:
                            {{ date('d/m/Y H:i:s', strtotime($s->iniciado_em))
                            ?? 'Aguardando' }}
                        </p>

                        <p class="small text-muted mb-3">
                            Término:
                            {{ date('d/m/Y H:i:s', strtotime($s->finalizado_em))
                            ?? 'Em andamento' }}
                        </p>

                        @if($s->resultados)
                        <hr>
                        <strong>Registros processados</strong>

                        <ul class="small mt-2 mb-0">
                            @foreach($s->resultados as $etapa => $total)
                            <li>
                                {{ ucfirst(str_replace('_', ' ', $etapa)) }}:
                                {{ $total }}
                            </li>
                            @endforeach
                        </ul>
                        @endif

                        @if($s->erro)
                        <div class="alert alert-danger mt-3 mb-0 small">
                            {{ $s->erro }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-light border">
                    Nenhuma sincronização realizada ainda.
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
  const lista = document.getElementById('listaSincronizacoes');
  const botao = document.getElementById('btnSincronizar');
  const urlStatus = @json(route('dados.status'));

  async function atualizarStatus() {
      try {
          const resposta = await fetch(urlStatus, {
              headers: { 'Accept': 'application/json' }
          });

          if (!resposta.ok) return;

          const dados = await resposta.json();

          const ativos = dados.some(s =>
              ['pendente', 'executando'].includes(s.status)
          );

          botao.disabled = ativos;
          botao.innerHTML = ativos
              ? '<span class="spinner-border spinner-border-sm me-1"></span> Sincronizando...'
              : '<i class="fas fa-sync-alt"></i> Sincronizar';

          lista.replaceChildren();

          if (!dados.length) {
              const coluna = document.createElement('div');
              coluna.className = 'col-12';
              coluna.textContent = 'Nenhuma sincronização realizada ainda.';
              lista.appendChild(coluna);
              return;
          }

          dados.forEach(s => {
              const coluna = document.createElement('div');
              coluna.className = 'col-md-6 col-xl-4';

              const card = document.createElement('div');
              card.className = 'card h-100 shadow-sm';

              const corpo = document.createElement('div');
              corpo.className = 'card-body';

              const titulo = document.createElement('h5');
              titulo.textContent = `Sincronização #${s.id}`;

              const status = document.createElement('span');
              status.className = 'badge ' + ({
                  concluida: 'bg-success',
                  falhou: 'bg-danger',
                  executando: 'bg-primary',
                  pendente: 'bg-secondary'
              }[s.status] || 'bg-secondary');
              status.textContent = s.status;

              const etapa = document.createElement('p');
              etapa.textContent = `Etapa: ${s.etapa_atual || '-'}`;

              const inicio = document.createElement('p');
              inicio.textContent = `Início: ${formatarData(s.iniciado_em) || 'Aguardando'}`;

              const fim = document.createElement('p');
              fim.textContent = `Término: ${formatarData(s.finalizado_em) || 'Em andamento'}`;

              corpo.append(titulo, status, etapa, inicio, fim);

              if (s.resultados) {
                  const subtitulo = document.createElement('strong');
                  subtitulo.textContent = 'Registros processados';
                  corpo.appendChild(subtitulo);

                  const ul = document.createElement('ul');

                  Object.entries(s.resultados).forEach(([nome, total]) => {
                      const li = document.createElement('li');
                      li.textContent = `${nome}: ${total}`;
                      ul.appendChild(li);
                  });

                  corpo.appendChild(ul);
              }

              if (s.erro) {
                  const erro = document.createElement('p');
                  erro.className = 'alert alert-danger mt-2';
                  erro.textContent = s.erro;
                  corpo.appendChild(erro);
              }

              card.appendChild(corpo);
              coluna.appendChild(card);
              lista.appendChild(coluna);
          });
      } catch (erro) {
          console.error('Erro ao consultar sincronizações:', erro);
      }
  }

  atualizarStatus();
  setInterval(atualizarStatus, 2000);
});


function formatarData(data) {
    if (!data) return null;

    const dataObj = new Date(data);

    if (isNaN(dataObj.getTime())) return data;

    return dataObj.toLocaleString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    });
}

</script>
@endsection