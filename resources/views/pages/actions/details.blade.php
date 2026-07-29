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
              <button class="nav-link text-start" id="banner-tab" data-bs-toggle="tab" data-bs-target="#banner-pane" type="button" role="tab" aria-selected="false">
                <i class="ti ti-photo me-2"></i>Banner
              </button>
              <button class="nav-link text-start" id="membros-tab" data-bs-toggle="tab" data-bs-target="#membros-pane" type="button" role="tab" aria-selected="false">
                <i class="ti ti-users me-2"></i>Membros
              </button>
              <button class="nav-link text-start" id="ods-tab" data-bs-toggle="tab" data-bs-target="#ods-pane" type="button" role="tab" aria-selected="false">
                <i class="ti ti-leaf me-2"></i>ODS
              </button>
              <button class="nav-link text-start" id="agenda-tab" data-bs-toggle="tab" data-bs-target="#agenda-pane" type="button" role="tab" aria-selected="false">
                <i class="ti ti-calendar me-2"></i>Agenda
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
                    <div class="card mb-3 p-0 px-5 overflow-hidden line-clamp-3 [&>*]:!m-0 [&>*]:!p-0 [&>p]:!mb-1">
                      {!! $action->resumo !!}
                    </div>
                    <p class="p-0 fw-bold text-cyan">Detalhes da ação:</p>
                    <div class="datagrid mb-3">
                      <div class="datagrid-item">
                        <div class="datagrid-title">Id do projeto</div>
                        <div class="datagrid-content">{{$action->id_projeto}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Ano</div>
                        <div class="datagrid-content">{{$action->ano}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Modalidade/Edital</div>
                        <div class="datagrid-content">{{$action->modalidade_edital}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Bolsas solicitadas</div>
                        <div class="datagrid-content">{{$action->bolsas_solicitadas}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Bolsas concedidas</div>
                        <div class="datagrid-content">{{$action->bolsas_concedidas}}</div>
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
                        <div class="datagrid-title">Situacão</div>
                        <div class="datagrid-content">{{$action->situacao}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Data de cadastro</div>
                        <div class="datagrid-content">{{$action->data_cadastro}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Data de inicio</div>
                        <div class="datagrid-content">{{$action->data_inicio}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Data do fim</div>
                        <div class="datagrid-content">{{$action->data_fim}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Data de atualizacao</div>
                        <div class="datagrid-content">{{$action->data_atualizacao}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Centro/Departamento/Sigla</div>
                        <div class="datagrid-content">{{$action->centro_departamento_sigla}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Tipo da acão</div>
                        <div class="datagrid-content">{{$action->tipo_acao}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Area temática</div>
                        <div class="datagrid-content">{{$action->area_tematica}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Ods</div>
                        <div class="datagrid-content">{{$action->ods}}</div>
                      </div>
                      <div class="datagrid-item">
                        <div class="datagrid-title">Palavras chave</div>
                        <div class="datagrid-content">{{$action->palavras_chave}}</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="banner-pane" role="tabpanel" tabindex="0">
              <div class="card card-lg mb-3">
                <div class="card-body p-5">
                  <div class="d-flex align-items-center flex-wrap justify-content-between mb-2">
                    <h3 class="m-0">Banner da ação</h3>
                    <button class="btn" data-bs-toggle="offcanvas" data-bs-target="#modal-add-banner"
                      aria-controls="offcanvasExample">
                      Inserir
                    </button>
                    <x-modal.offcanvas route="{{ route('actions.addBanner', $action->id) }}" id="modal-add-banner"
                      class="offcanvas-end" title="Adicionar banner">
                      <x-slot:content>
                        <div class="text-start">
                          <label for="banner" class="form-label fw-bold">Anexe um banner</label>
                          <div class="input-group">
                            <input type="file" class="form-control" id="banner" name="banner" accept=".jpg,.png,.jpeg,.webp">
                          </div>
                          <small class="text-muted mt-1 d-block italic">Formatos aceitos: JPG, PNG ou WEBP.</small>
                        </div>
                      </x-slot:content>
                    </x-modal.offcanvas>
                  </div>
                  @if ($action->img)
                  <p><img src="{{ asset('storage/' . $action->img) }}" alt="Image Alt"></p>
                  @else
                  <div class="alert alert-warning">
                    Não foi enviado banner.
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

            <div class="tab-pane fade" id="ods-pane" role="tabpanel" tabindex="0">
                <div class="card card-lg mb-3">
                <div class="card-body p-5">
                    <h3 class="m-0 mb-3">Objetivos de Desenvolvimento Sustentável</h3>
                    <p class="text-muted">A listagem estilo SIGAA será implementada aqui.</p>
                </div>
                </div>
            </div>

            <div class="tab-pane fade" id="agenda-pane" role="tabpanel" tabindex="0">
              <div class="card card-lg mb-3">
                <div class="card-body p-5">
                  <div class="d-flex align-items-center flex-wrap justify-content-between mb-2">
                    <h3 class="m-0">Agenda da ação</h3>
                    @can('adicionar_agenda')
                    <button class="btn" data-bs-toggle="offcanvas" data-bs-target="#modal-add-agenda"
                      aria-controls="offcanvasExample">
                      Inserir
                    </button>
                    <x-modal.offcanvas route="{{ route('actions.storeSchedule', $action->id) }}" id="modal-add-agenda"
                      class="offcanvas-end" title="Adicionar agenda">
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
                          <td>{{ $item->data_hora_inicio }}</td>
                          <td>{{ $item->data_hora_fim }}</td>
                          <td>{{ $item->local_formato }}</td>
                          <td>{{ $item->descricao }}</td>
                          <td><a href="">Editar</a></td>
                          <td>
                            <form action="{{ route('members.delete', $item->id) }}" method="POST"
                              onsubmit="return confirm('Deseja realmente deletar este membro?')">
                              @csrf
                              @method('DELETE')
                              <button class="btn btn-sm btn-danger p-1 px-" type="submit">
                                Deletar
                              </button>
                            </form>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
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
@endsection