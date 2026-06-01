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
            <th class="p-2 border">Ano</th>
            <th class="p-2 border">ID Projeto</th>
            <th class="p-2 border">Título</th>
            <th class="p-2 border">Centro/Depto</th>
            <th class="p-2 border">Situação</th>
            <th class="p-2 border">Data Início</th>
            <th class="p-2 border">Data Fim</th>
            <th class="p-2 border">Data Atualização</th>
            <th class="p-2 border">Resumo</th>
            <th class="p-2 border">Palavras Chave</th>
            <th class="p-2 border">Tipo Ação</th>
            <th class="p-2 border">Área Temática</th>
            <th class="p-2 border">Modalidade</th>
            <th class="p-2 border">Com Bolsa</th>
            <th class="p-2 border">ODS</th>
            <th class="p-2 border">Proponente</th>
            <th class="p-2 border">Email Proponente</th>
            <th class="p-2 border">Ações</th>
          </tr>
        </thead>
        <tbody>
          @foreach($paginatedProjects as $data)
          @php $index = $data['row_index']; @endphp
          <tr id="row_{{ $index }}" class="{{ !empty($data['errors']) ? 'table-danger' : '' }}">
            <td class="p-1" style="width: 80px;">
              <input type="text" name="projects[{{ $index }}][ano]" value="{{ $data['ano'] ?? '' }}"
                class="form-control form-control-sm form-control-flush text-center">
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][id_projeto]" value="{{ $data['id_projeto'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <td class="p-1" style="min-width: 200px;">
              <input type="text" name="projects[{{ $index }}][titulo]" value="{{ $data['titulo'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['titulo']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['titulo']))
              <div class="invalid-feedback d-block">{{ $data['errors']['titulo'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][centro_departamento]"
                value="{{ $data['centro_departamento'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][situacao]" value="{{ $data['situacao'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <td class="p-1">
              <input type="date" name="projects[{{ $index }}][data_inicio]" value="{{ $data['data_inicio'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <td class="p-1">
              <input type="date" name="projects[{{ $index }}][data_fim]" value="{{ $data['data_fim'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <td class="p-1">
              <input type="datetime" name="projects[{{ $index }}][data_atualizacao]"
                value="{{ $data['data_atualizacao'] ?? '' }}" class="form-control form-control-sm form-control-flush">
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][resumo]" value="{{ $data['resumo'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][palavras_chave]"
                value="{{ $data['palavras_chave'] ?? '' }}" class="form-control form-control-sm form-control-flush">
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][tipo_acao]" value="{{ $data['tipo_acao'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][area_tematica]" value="{{ $data['area_tematica'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][modalidade]" value="{{ $data['modalidade'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][com_bolsa]" value="{{ $data['com_bolsa'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][ods]" value="{{ $data['ods'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <td class="p-1" style="min-width: 200px;">
              <input type="text" name="projects[{{ $index }}][proponente]" value="{{ $data['proponente'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['proponente']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['proponente']))
              <div class="invalid-feedback d-block">{{ $data['errors']['proponente'] }}</div>
              @endif
            </td>
            <td class="p-1" style="min-width: 200px;">
              <input type="text" name="projects[{{ $index }}][email_proponente]"
                value="{{ $data['email_proponente'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['email']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['email']))
              <div class="invalid-feedback d-block">{{ $data['errors']['email'] }}</div>
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