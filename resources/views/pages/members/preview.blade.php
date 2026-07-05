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

  <form action="{{route('membros.storeImport')}}" method="POST" id="importForm">
    @csrf

    <input type="hidden" name="deleted_indexes" id="deleted_indexes" value="">

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse table table-bordered">
        <thead>
          <tr>
            <th class="p-2 border">id_projeto</th>
            <th class="p-2 border">id_pessoa</th>
            <th class="p-2 border">nome</th>
            <th class="p-2 border">tipo_membro</th>
            <th class="p-2 border">categoria_membro</th>
            <th class="p-2 border">email</th>
            <th class="p-2 border">status</th>
            <th class="p-2 border">data_inicio</th>
            <th class="p-2 border">data_fim</th>
            <th class="p-2 border">tipo_vinculo</th>
            <th class="p-2 border"></th>
          </tr>
        </thead>
        <tbody> 
          @foreach($paginatedMembers as $data)
          @php $index = $data['row_index']; @endphp
          <tr id="row_{{ $index }}" class="{{ !empty($data['errors']) ? 'table-danger' : '' }}">
            <td class="p-1">
              <input type="text" name="members[{{ $index }}][id_projeto]" value="{{ $data['id_projeto'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['id_projeto']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['id_projeto']))
              <div class="invalid-feedback d-block">{{ $data['errors']['id_projeto'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="members[{{ $index }}][id_pessoa]" value="{{ $data['id_pessoa'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['id_pessoa']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['id_pessoa']))
              <div class="invalid-feedback d-block">{{ $data['errors']['id_pessoa'] }}</div>
              @endif
            </td>
            <td class="p-1" style="min-width: 200px;">
              <input type="text" name="members[{{ $index }}][nome]" value="{{ $data['nome'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['nome']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['nome']))
              <div class="invalid-feedback d-block">{{ $data['errors']['nome'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="members[{{ $index }}][tipo_membro]" value="{{ $data['tipo_membro'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['tipo_membro']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['tipo_membro']))
              <div class="invalid-feedback d-block">{{ $data['errors']['tipo_membro'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="members[{{ $index }}][categoria_membro]" value="{{ $data['categoria_membro'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['categoria_membro']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['categoria_membro']))
              <div class="invalid-feedback d-block">{{ $data['errors']['categoria_membro'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="members[{{ $index }}][email]" value="{{ $data['email'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['email']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['email']))
              <div class="invalid-feedback d-block">{{ $data['errors']['email'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="members[{{ $index }}][status]" value="{{ $data['status'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['status']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['status']))
              <div class="invalid-feedback d-block">{{ $data['errors']['status'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="members[{{ $index }}][data_inicio]" value="{{ $data['data_inicio'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['data_inicio']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['data_inicio']))
              <div class="invalid-feedback d-block">{{ $data['errors']['data_inicio'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="members[{{ $index }}][data_fim]" value="{{ $data['data_fim'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['data_fim']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['data_fim']))
              <div class="invalid-feedback d-block">{{ $data['errors']['data_fim'] }}</div>
              @endif
            </td>
            <td class="p-1">
              <input type="text" name="members[{{ $index }}][tipo_vinculo]" value="{{ $data['tipo_vinculo'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['tipo_vinculo']) ? 'is-invalid' : '' }}">
              @if(isset($data['errors']['tipo_vinculo']))
              <div class="invalid-feedback d-block">{{ $data['errors']['tipo_vinculo'] }}</div>
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
      {{ $paginatedMembers->links() }}
    </div>

    <div class="d-flex justify-content-end">
      <button type="submit" class="btn btn-success">Salvar Lote ({{ $paginatedMembers->total() }} registros)</button>
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