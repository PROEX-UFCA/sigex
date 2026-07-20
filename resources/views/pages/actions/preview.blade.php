@extends('templates.simple')

@section('styles')
@endsection

@section('content')
<div class="mx-auto p-4">
  <h2>Revisão da Importação</h2>

  @if($totalErrors > 0)
  <div class="alert alert-warning bg-yellow-100 p-4 mb-4 rounded">
    <strong>Atenção!</strong> Encontramos problemas em {{ $totalErrors }} linha(s) em todo o lote. Corrija os campos
    destacados em vermelho ou exclua a linha antes de salvar.
  </div>
  @else
  <div class="alert alert-success bg-green-100 p-4 mb-4 rounded">
    Tudo certo! Revise os dados e clique em salvar para importar todos os registros válidos.
  </div>
  @endif

  @if($duplicadosIgnorados > 0)
  <div class="alert alert-info bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4">
    <strong>Aviso:</strong> {{ $duplicadosIgnorados }} registro(s) foram ignorados no carregamento inicial porque já
    existem no banco de dados.
  </div>
  @endif

  <form action="{{route('actions.storeImport')}}" method="POST" id="importForm">
    @csrf

    <input type="hidden" name="deleted_indexes" id="deleted_indexes" value="">

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse table table-bordered">
        <thead>
          <tr>
            <th class="p-2 border">id_projeto</th>
            <th class="p-2 border">ano</th>
            <th class="p-2 border">titulo</th>
            <th class="p-2 border">modalidade_edital</th>
            <th class="p-2 border">bolsas_solicitadas</th>
            <th class="p-2 border">bolsas_concedidas</th>
            <th class="p-2 border">financiamento_interno</th>
            <th class="p-2 border">financiamento_externo</th>
            <th class="p-2 border">situacao</th>
            <th class="p-2 border">data_cadastro</th>
            <th class="p-2 border">data_inicio</th>
            <th class="p-2 border">data_fim</th>
            <th class="p-2 border">data_atualizacao</th>
            <th class="p-2 border">centro_departamento_sigla</th>
            <th class="p-2 border">tipo_acao</th>
            <th class="p-2 border">area_tematica</th>
            <th class="p-2 border">resumo</th>
            <th class="p-2 border">palavras_chave</th>
            <th class="p-2 border">ods</th>
            <th class="p-2 border">contexto</th>
            <th class="p-2 border"></th>
          </tr>
        </thead>
        <tbody> 
          @foreach($paginatedProjects as $data)
          @php $index = $data['row_index']; @endphp
          <tr id="row_{{ $index }}" class="{{ !empty($data['errors']) ? 'table-danger' : '' }}">
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][id_projeto]" value="{{ $data['id_projeto'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['id_projeto']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['id_projeto']))
              <div class="invalid-feedback d-block">{{ $data['errors']['id_projeto'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][ano]" value="{{ $data['ano'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['ano']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['ano']))
              <div class="invalid-feedback d-block">{{ $data['errors']['ano'] }}</div>
              @endif
            </td>
            <td class="p-1" style="min-width: 200px;">
              <input type="text" name="projects[{{ $index }}][titulo]" value="{{ $data['titulo'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['titulo']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['titulo']))
              <div class="invalid-feedback d-block">{{ $data['errors']['titulo'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][modalidade_edital]" value="{{ $data['modalidade_edital'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['modalidade_edital']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['modalidade_edital']))
              <div class="invalid-feedback d-block">{{ $data['errors']['modalidade_edital'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][bolsas_solicitadas]" value="{{ $data['bolsas_solicitadas'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['bolsas_solicitadas']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['bolsas_solicitadas']))
              <div class="invalid-feedback d-block">{{ $data['errors']['bolsas_solicitadas'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][bolsas_concedidas]" value="{{ $data['bolsas_concedidas'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['bolsas_concedidas']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['bolsas_concedidas']))
              <div class="invalid-feedback d-block">{{ $data['errors']['bolsas_concedidas'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][financiamento_interno]" value="{{ $data['financiamento_interno'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['financiamento_interno']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['financiamento_interno']))
              <div class="invalid-feedback d-block">{{ $data['errors']['financiamento_interno'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][financiamento_externo]" value="{{ $data['financiamento_externo'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['financiamento_externo']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['financiamento_externo']))
              <div class="invalid-feedback d-block">{{ $data['errors']['financiamento_externo'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][situacao]" value="{{ $data['situacao'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['situacao']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['situacao']))
              <div class="invalid-feedback d-block">{{ $data['errors']['situacao'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][data_cadastro]" value="{{ $data['data_cadastro'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['data_cadastro']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['data_cadastro']))
              <div class="invalid-feedback d-block">{{ $data['errors']['data_cadastro'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][data_inicio]" value="{{ $data['data_inicio'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['data_inicio']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['data_inicio']))
              <div class="invalid-feedback d-block">{{ $data['errors']['data_inicio'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][data_fim]" value="{{ $data['data_fim'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['data_fim']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['data_fim']))
              <div class="invalid-feedback d-block">{{ $data['errors']['data_fim'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][data_atualizacao]" value="{{ $data['data_atualizacao'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['data_atualizacao']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['data_atualizacao']))
              <div class="invalid-feedback d-block">{{ $data['errors']['data_atualizacao'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][centro_departamento_sigla]" value="{{ $data['centro_departamento_sigla'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['centro_departamento_sigla']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['centro_departamento_sigla']))
              <div class="invalid-feedback d-block">{{ $data['errors']['centro_departamento_sigla'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][tipo_acao]" value="{{ $data['tipo_acao'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['tipo_acao']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['tipo_acao']))
              <div class="invalid-feedback d-block">{{ $data['errors']['tipo_acao'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][area_tematica]" value="{{ $data['area_tematica'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['area_tematica']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['area_tematica']))
              <div class="invalid-feedback d-block">{{ $data['errors']['area_tematica'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][resumo]" value="{{ $data['resumo'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['resumo']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['resumo']))
              <div class="invalid-feedback d-block">{{ $data['errors']['resumo'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][palavras_chave]" value="{{ $data['palavras_chave'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['palavras_chave']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['palavras_chave']))
              <div class="invalid-feedback d-block">{{ $data['errors']['palavras_chave'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][ods]" value="{{ $data['ods'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['ods']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['ods']))
              <div class="invalid-feedback d-block">{{ $data['errors']['ods'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][contexto]" value="{{ $data['contexto'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['contexto']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['contexto']))
              <div class="invalid-feedback d-block">{{ $data['errors']['contexto'] }}</div>
              @endif
            </td>
            
            <td class="p-1 text-center align-middle">
              <button type="button" onclick="removeRow({{ $index }})" class="btn btn-sm btn-danger">Excluir</button>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-4 mb-4 d-flex justify-content-center">
      {{ $paginatedProjects->links() }}
    </div>

    <div class="d-flex justify-content-end">
      <button type="submit" class="btn btn-success">Salvar Lote ({{ $paginatedProjects->total() }} registros)</button>
    </div>
  </form>
</div>
@endsection

@section('scripts')
<script>
    let deletedIndexes = [];

    function removeRow(index) {
        const row = document.getElementById('row_' + index);
        if (row) {
            row.remove();
            deletedIndexes.push(index);
            document.getElementById('deleted_indexes').value = deletedIndexes.join(',');
        }
    }

    document.querySelectorAll('.input-watch').forEach(input => {
        input.addEventListener('input', function() {
            if(this.value.trim() !== '') {
                this.classList.remove('border-red-500', 'bg-red-50', 'is-invalid');
                this.classList.add('border-gray-300');
            }
        });
    });

    document.querySelectorAll('.pagination a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const form = document.getElementById('importForm');
            form.action = this.href;
            form.submit();
        });
    });
</script>
@endsection