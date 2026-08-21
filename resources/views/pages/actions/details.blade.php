@extends('templates.template')

@section('styles')
@endsection
@section('content')
<div class="page-body row">  
  <div class="container-xl">
    <div class="row g-5">
      <div class="col-12 col-md-3 col-lg-2">

        <div class="sticky-top" style="top: 1rem; z-index: 1020;"> 
          <a href="{{route('actions.my')}}" class="btn w-100 mb-3">
            Voltar página
          </a>
        </div>

        <div class="sticky-top" style="top: 4rem;"> 
            <div class="nav flex-column nav-pills " id="v-tabs" role="tablist" aria-orientation="vertical">
              <button class="nav-link text-start active" id="acao-tab" data-bs-toggle="tab" data-bs-target="#acao-pane" type="button" role="tab" aria-selected="true">
                <i class="ti ti-info-circle me-2"></i>Ação
              </button>
              <button class="nav-link text-start" id="ods-tab" data-bs-toggle="tab" data-bs-target="#ods-pane" type="button" role="tab" aria-selected="false">
                <i class="ti ti-leaf me-2"></i>ODS
              </button>
              <button class="nav-link text-start" id="capa-ilustrativa-tab" data-bs-toggle="tab" data-bs-target="#capa-ilustrativa-pane" type="button" role="tab" aria-selected="false">
                <i class="ti ti-photo me-2"></i>Capa
              </button>
              <button class="nav-link text-start" id="membros-tab" data-bs-toggle="tab" data-bs-target="#membros-pane" type="button" role="tab" aria-selected="false">
                <i class="ti ti-users me-2"></i>Membros
              </button>
              <button class="nav-link text-start" id="cronograma-externo-tab" data-bs-toggle="tab" data-bs-target="#cronograma-externo-pane" type="button" role="tab" aria-selected="false">
                <i class="ti ti-calendar me-2"></i>Cronograma
              </button>
              <button class="nav-link text-start" id="agenda-interna-tab" data-bs-toggle="tab" data-bs-target="#agenda-interna-pane" type="button" role="tab" aria-selected="false">
                <i class="ti ti-notebook me-2"></i>Agenda
              </button>
            </div>
          </div>
        </div>
          
        <div class="col-12 col-md-9">
          <div class="tab-content" id="v-tabs-content">
            
            <div class="tab-pane fade show active" id="acao-pane" role="tabpanel" tabindex="0">
              <div class="card card-lg mb-3">
                <div class="card-body p-5">
                  <div class="markdown mb-6">
                    <h1>{{$action->titulo}}</h1>
                    
                    <p class="p-0 fw-bold text-cyan">Resumo da ação:</p>
                    <div class="card mb-5 p-0 px-5 overflow-hidden line-clamp-3 [&>*]:!m-0 [&>*]:!p-0 [&>p]:!mb-1">
                      {!! $action->resumo !!}
                    </div>

                    <h4 class="text-cyan mb-3 border-bottom pb-2">Informações Gerais</h4>
                    <div class="datagrid mb-5">
                      <div class="datagrid-item">
                        <div class="datagrid-title">Id do projeto</div>
                        <div class="datagrid-content">{{$action->id_projeto}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Ano</div>
                        <div class="datagrid-content">{{$action->ano}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Situação</div>
                        <div class="datagrid-content">
                          <span class="status status-azure" style="font-size: 12px;">{{$action->situacao}}</span>
                        </div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Tipo da ação</div>
                        <div class="datagrid-content">{{$action->tipo_acao}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Área temática</div>
                        <div class="datagrid-content">{{$action->area_tematica}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Centro/Departamento</div>
                        <div class="datagrid-content">{{$action->centro_departamento_sigla}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Palavras chave</div>
                        <div class="datagrid-content">{{$action->palavras_chave}}</div>
                      </div>
                    </div>

                    <h4 class="text-cyan mb-3 border-bottom pb-2">Datas e Prazos</h4>
                    <div class="datagrid mb-5">
                      <div class="datagrid-item">
                        <div class="datagrid-title">Data de cadastro</div>
                        <div class="datagrid-content">{{$action->data_cadastro ? \Carbon\Carbon::parse($action->data_cadastro)->format('d/m/Y') : '/'}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Data de início</div>
                        <div class="datagrid-content">{{$action->data_inicio ? \Carbon\Carbon::parse($action->data_inicio)->format('d/m/Y') : '-' }}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Data do fim</div>
                        <div class="datagrid-content">{{$action->data_fim ? \Carbon\Carbon::parse($action->data_fim)->format('d/m/Y') : '-' }}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Última atualização</div>
                        <div class="datagrid-content">{{$action->data_atualizacao ? \Carbon\Carbon::parse($action->data_atualizacao)->format('d/m/Y') : '-' }}</div>
                      </div>
                    </div>

                    <h4 class="text-cyan mb-3 border-bottom pb-2">Recursos e Financiamento</h4>
                    <div class="datagrid mb-3">
                      <div class="datagrid-item">
                        <div class="datagrid-title">Modalidade/Edital</div>
                        <div class="datagrid-content">{{$action->modalidade_edital}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Financiamento interno?</div>
                        <div class="datagrid-content">{{$action->financiamento_interno}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Financiamento externo?</div>
                        <div class="datagrid-content">{{$action->financiamento_externo}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Bolsas solicitadas</div>
                        <div class="datagrid-content">{{$action->bolsas_solicitadas}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Bolsas concedidas</div>
                        <div class="datagrid-content">{{$action->bolsas_concedidas}}</div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="ods-pane" role="tabpanel" tabindex="0">
              <div class="card card-lg mb-3">
                <div class="card-body p-5">
                  <h3 class="m-0 mb-4">Objetivos de Desenvolvimento Sustentável</h3>
                  <p class="text-muted mb-4 border-start border-3 border-info ps-3">
                    Abaixo estão os Objetivos de Desenvolvimento Sustentável (ODS) da ONU vinculados a este projeto. Os ícones coloridos representam os objetivos que esta ação atende diretamente.
                  </p>

                  @if(!empty($action->ods))
                    @php
                      $odsArray = array_map('trim', explode(';', $action->ods));
                  @endphp

                    <div class="w-100 w-lg-75 mx-auto">
                      <div class="row row-cols-3 row-cols-md-6 g-1 justify-content-center align-items-center p-2 shadow-sm">
                        
                        @for ($i = 1; $i <= 17; $i++)
                          @php
                            $isActive = in_array((string)$i, $odsArray);
                            $imgName = $isActive ? "{$i}.png" : "{$i}_light.png";
                          @endphp
                          
                          <div class="col">
                            <img 
                              src="https://sig.ufca.edu.br/sigaa/img/ODS/{{ $imgName }}" 
                              class="img-fluid w-100" 
                              alt="ODS {{ $i }}"
                              title="ODS {{ $i }}"
                            >
                          </div>
                        @endfor

                        <div class="col">
                          <img 
                            src="https://sig.ufca.edu.br/sigaa/img/ODS/ods_.png" 
                            class="img-fluid w-100" 
                            alt="Logo ODS Geral"
                          >
                        </div>

                      </div>
                    </div>
                  @else
                    <div class="alert alert-info mb-0">
                      Nenhum ODS vinculado a esta ação no momento.
                    </div>
                  @endif

                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="capa-ilustrativa-pane" role="tabpanel" tabindex="0">
              <div class="card card-lg mb-3">
                <div class="card-body p-5">
                  <div class="d-flex align-items-center flex-wrap justify-content-between mb-2">
                    <h3 class="m-0">Capa Ilustrativa da Ação</h3>
                    <button class="btn" data-bs-toggle="offcanvas" data-bs-target="#modal-add-banner"
                      aria-controls="offcanvasExample">
                      Inserir
                    </button>
                    <x-modal.offcanvas route="{{ route('actions.addBanner', $action->id) }}" id="modal-add-banner"
                    class="offcanvas-end" title="Adicionar capa">
                    <x-slot:content>
                      <div class="text-start">
                        <label for="banner" class="form-label fw-bold">Anexe uma imagem que será a capa do projeto no portal.</label>
                        <div class="input-group">
                          <input type="file" class="form-control" id="banner" name="banner" accept=".jpg,.png,.jpeg,.webp">
                        </div>
                        <small class="text-muted mt-1 d-block italic">Formatos aceitos: JPG, PNG ou WEBP.</small>
                        <small class="text-muted mt-1 d-block italic">Tamanho máximo: 2MB</small>
                      </div>
                    </x-slot:content>
                  </x-modal.offcanvas>
                </div>
                <div>
                  <p class="text-muted mb-4 border-start border-3 border-info ps-3">
                    A imagem escolhida será a capa da ação no portal SIGEX. Certifique-se de inserir uma imagem com o formato "paisagem" para uma melhor visualização.
                  </p>
                </div>
                  @if ($action->img)
                  <p><img src="{{ asset('storage/' . $action->img) }}" alt="Image Alt"></p>
                  @else
                  <div class="alert alert-warning">
                    Ainda não foi enviada uma capa.
                  </div>
                  @endif
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="membros-pane" role="tabpanel" tabindex="0">
              <div class="card card-lg mb-3">
                <div class="card-body p-5">
                  <div class="d-flex align-items-center flex-wrap justify-content-between mb-2">
                    <h3 class="m-0">Membros da ação</h3>
                  </div>
                  <div class="table-responsive p-0 mb-5">
                    <table class="table table-striped table-bordered align-middle mb-0 text-nowrap">
                      <thead>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Matricula/Siape</th>
                        <th>Centro/Departamento</th>
                        <th>Categoria</th>
                        <th>Id pessoa</th>
                        <th>tipo de membro</th>
                        <th>status</th>
                        <th>data inicio</th>
                        <th>data fim</th>
                      </thead>
                      <tbody>
                        @foreach ($action->equipe as $item)
                        <tr>
                          <td>{{ $item->user->name }}</td>
                          <td>{{ $item->user->email }}</td>
                          <td>{{ $item->user->phone ?? '-' }}</td>
                          <td>{{ $item->user->matricula_siape ?? '-' }}</td>
                          <td>{{ $item->user->centro_departamento ?? '-' }}</td>
                          <td>{{ $item->categoria_membro }}</td>
                          <td>{{ $item->id_pessoa }}</td>
                          <td>{{ $item->tipo_membro }}</td>
                          <td>{{ $item->status }}</td>
                          <td>{{ $item->data_inicio }}</td>
                          <td>{{ $item->data_fim }}</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="cronograma-externo-pane" role="tabpanel" tabindex="0">
              <div class="card card-lg mb-3">
                <div class="card-body p-5">
                  <div class="d-flex align-items-center flex-wrap justify-content-between mb-2">
                    <h3 class="m-0">Cronograma Externo da Ação</h3>
                    @can('adicionar_agenda')
                    <button class="btn" data-bs-toggle="offcanvas" data-bs-target="#modal-add-agenda"
                      aria-controls="offcanvasExample">
                      Inserir
                    </button>
                    <x-modal.offcanvas route="{{ route('actions.storeSchedule', $action->id) }}" id="modal-add-agenda"
                      class="offcanvas-end" title="Adicionar cronograma externo">
                      <x-slot:content>
                        @include('components.form-elements.input.input', [
                        'title' => 'Título do evento',
                        'type' => 'text',
                        'class' => 'mb-3',
                        'name' => 'titulo',
                        'required' => 'true',
                        'placeholder' => 'Digite o título do evento',
                        'value' => old('titulo') ?? '',
                        ])
                        @include('components.form-elements.input.input', [
                        'title' => 'Local ou formato do evento',
                        'type' => 'text',
                        'class' => 'mb-3',
                        'name' => 'local_formato',
                        'required' => 'true',
                        'placeholder' => 'Digite o local ou formato do evento',
                        'value' => old('local_formato') ?? '',
                        ])
                        @include('components.form-elements.input.input', [
                        'title' => 'Data e hora de início do evento',
                        'type' => 'datetime-local',
                        'class' => 'mb-3',
                        'name' => 'data_hora_inicio',
                        'required' => 'true',
                        'placeholder' => 'Digite a data e hora de início',
                        'value' => old('data_hora_inicio') ?? '',
                        ])
                        @include('components.form-elements.input.input', [
                        'title' => 'Data e hora do fim do evento',
                        'type' => 'datetime-local',
                        'class' => 'mb-3',
                        'name' => 'data_hora_fim',
                        'required' => 'true',
                        'placeholder' => 'Digite a data e hora do fim',
                        'value' => old('data_hora_fim') ?? '',
                        ])
                        @include('components.form-elements.textarea.textarea', [
                        'title' => 'Descrição do evento',
                        'class' => 'mb-3',
                        'name' => 'descricao',
                        'required' => 'true',
                        'placeholder' => 'Descricao do evento',
                        'value' => old('descricao') ?? '',
                        ])
                      </x-slot:content>
                    </x-modal.offcanvas>
                    @endcan
                  </div>

                  <div>
                    <p class="text-muted mb-4 border-start border-3 border-info ps-3">
                      Use essa seção para inserir a programação da ação, ela será disponibilizada para instituições e pessoas interessadas no portal do SIGEX.
                    </p>
                  </div>

                  <div class="table-responsive p-0 mb-3">
                    <table class="table table-striped table-bordered align-middle mb-0 text-nowrap">
                      <thead>
                        <th>Evento</th>
                        <th>Data/Hora de início</th>
                        <th>Data/Hora de fim</th>
                        <th>Local</th>
                        <th>Descrição</th>
                        <th></th>
                        <th></th>
                      </thead>
                      <tbody>
                        @foreach ($action->agenda as $item)
                        <tr>
                          <td>{{ $item->titulo_evento }}</td>
                          <td>{{ $item->data_hora_inicio ? \Carbon\Carbon::parse($item->data_hora_inicio)->format('d/m/Y \à\s H:i') : '/' }}</td>
                          <td>{{ $item->data_hora_fim ? \Carbon\Carbon::parse($item->data_hora_fim)->format('d/m/Y \à\s H:i') : '/' }}</td>
                          <td>{{ $item->local_formato }}</td>
                          <td>{{ $item->descricao }}</td>
                        
                        @can('editar_agenda')
                          <td>
                            <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover" title="Editar">
                              <button type="button" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="offcanvas" data-bs-target="#modal-edit-agenda-{{ $item->id }}" aria-controls="offcanvasExample">
                                <i class="ti ti-pencil"></i>
                              </button>
                            </span>

                            <x-modal.offcanvas route="{{ route('actions.updateSchedule', $item->id) }}" id="modal-edit-agenda-{{ $item->id }}" class="offcanvas-end" title="Editar cronograma externo">
                              <x-slot:content>
                                @method('PUT')
                                
                                @include('components.form-elements.input.input', [
                                  'title' => 'Título do evento',
                                  'type' => 'text',
                                  'class' => 'mb-3',
                                  'name' => 'titulo',
                                  'required' => 'true',
                                  'placeholder' => 'Digite o título do evento',
                                  'value' => old('titulo') ?? $item->titulo_evento,
                                ])
                                
                                @include('components.form-elements.input.input', [
                                  'title' => 'Local ou formato do evento',
                                  'type' => 'text',
                                  'class' => 'mb-3',
                                  'name' => 'local_formato',
                                  'required' => 'true',
                                  'placeholder' => 'Digite o local ou formato do evento',
                                  'value' => old('local_formato') ?? $item->local_formato,
                                ])
                                
                                @include('components.form-elements.input.input', [
                                  'title' => 'Data e hora de início do evento',
                                  'type' => 'datetime-local',
                                  'class' => 'mb-3',
                                  'name' => 'data_hora_inicio',
                                  'required' => 'true',
                                  'value' => old('data_hora_inicio') ?? $item->data_hora_inicio,
                                ])
                                
                                @include('components.form-elements.input.input', [
                                  'title' => 'Data e hora do fim do evento',
                                  'type' => 'datetime-local',
                                  'class' => 'mb-3',
                                  'name' => 'data_hora_fim',
                                  'required' => 'true',
                                  'value' => old('data_hora_fim') ?? $item->data_hora_fim,
                                ])
                                
                                @include('components.form-elements.textarea.textarea', [
                                  'title' => 'Descrição do evento',
                                  'class' => 'mb-3',
                                  'name' => 'descricao',
                                  'required' => 'true',
                                  'placeholder' => 'Descricao do evento',
                                  'value' => old('descricao') ?? $item->descricao,
                                ])
                              </x-slot:content>
                            </x-modal.offcanvas>
                          </td>
                        @endcan

                        @can('remover_agenda')
                        <td>
                          <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover" title="Deletar">
                            <button type="button" class="btn btn-sm btn-danger btn-icon" data-bs-toggle="modal" data-bs-target="#modal-delete-agenda-{{ $item->id }}">
                              <i class="ti ti-trash"></i>
                            </button>
                          </span>

                          <div class="modal fade" id="modal-delete-agenda-{{ $item->id }}" tabindex="-1" aria-labelledby="modalLabelAgenda{{ $item->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                
                                <div class="modal-header bg-danger text-white">
                                  <h5 class="modal-title" id="modalLabelAgenda{{ $item->id }}">Confirmar Exclusão</h5>
                                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                
                                <div class="modal-body text-start text-wrap">
                                  Tem certeza que deseja deletar o evento <strong>{{ $item->titulo_evento }}</strong> do cronograma externo? <br><br>
                                  <span class="text-muted small">Esta ação removerá permanentemente o evento do sistema.</span>
                                </div>
                                
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                  
                                  <form action="{{ route('actions.deleteSchedule', $item->id) }}" method="POST" class="m-0 p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Sim, Deletar Evento</button>
                                  </form>
                                </div>
                                
                              </div>
                            </div>
                          </div>
                        </td>
                        @endcan

                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade show" id="agenda-interna-pane" role="tabpanel" tabindex="0">
              <div class="card card-lg mb-3">
                <div class="card-body p-5">
                  <h3 class="m-0">Agenda Interna da Ação</h3>
                  <div>Em processo de desenvolvimento.</div>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    let hash = window.location.hash;
    if (hash) {
        let tabTarget = document.querySelector('button[data-bs-target="' + hash + '"]');
        if (tabTarget) {
            new bootstrap.Tab(tabTarget).show();
            window.scrollTo(0, 0);
        }
    }

    const tabs = document.querySelectorAll('button[data-bs-toggle="tab"]');
    tabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', event => {
            window.history.replaceState(null, null, event.target.dataset.bsTarget);
        });
    });
});
</script>
@endsection