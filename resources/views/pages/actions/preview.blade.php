@extends('templates.simple')

@section('styles')
@endsection
@section('content')
<div class="mx-auto p-4">
  <h2>Revisão da Importação</h2>

  @if($totalErrors > 0)
  <div class="alert alert-warning bg-yellow-100 p-4 mb-4 rounded">
    <strong>Atenção!</strong> Encontramos problemas em {{ $totalErrors }} linha(s). Corrija os campos destacados em
    vermelho ou exclua a linha antes de salvar.
  </div>
  @else
  <div class="alert alert-success bg-green-100 p-4 mb-4 rounded">
    Tudo certo! Revise os dados e clique em salvar.
  </div>
  @endif
  @if($duplicadosIgnorados > 0)
  <div class="alert alert-info bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4">
    <strong>Aviso:</strong> {{ $duplicadosIgnorados }} registro(s) foram ignorados porque já existem no banco de dados
    com estas mesmas informações.
  </div>
  @endif
  <form action="{{route('actions.storeImport')}}" method="POST" id="importForm">
    @csrf
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse table table-bordered">
        <thead>
          <tr>
            <th class="p-2 border">ano</th>
            <th class="p-2 border">id_projeto</th>
            <th class="p-2 border">titulo</th>
            <th class="p-2 border">centro_departamento</th>
            <th class="p-2 border">situacao</th>
            <th class="p-2 border">data_inicio</th>
            <th class="p-2 border">data_fim</th>
            <th class="p-2 border">data_atualizacao</th>
            <th class="p-2 border">resumo</th>
            <th class="p-2 border">palavras_chave</th>
            <th class="p-2 border">tipo_acao</th>
            <th class="p-2 border">area_tematica</th>
            <th class="p-2 border">modalidade</th>
            <th class="p-2 border">com_bolsa</th>
            <th class="p-2 border">ods</th>
            <th class="p-2 border">proponente</th>
            <th class="p-2 border">email_proponente</th>
            <th class="p-2 border"></th>
          </tr>
        </thead>
        <tbody>
        <tbody>
          @foreach($projectsData as $index => $data)
          <tr id="row_{{ $index }}" class="{{ !empty($data['errors']) ? 'table-danger' : '' }}">
            <!-- 1. Ano -->
            <td class="p-1" style="width: 80px;">
              <input type="text" name="projects[{{ $index }}][ano]" value="{{ $data['ano'] ?? '' }}"
                class="form-control form-control-sm form-control-flush text-center">
            </td>
            <!-- 2. ID Projeto -->
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][id_projeto]" value="{{ $data['id_projeto'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <!-- 3. Título (Com alerta de erro) -->
            <td class="p-1" style="min-width: 200px;">
              <input type="text" name="projects[{{ $index }}][titulo]" value="{{ $data['titulo'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['titulo']) ? 'is-invalid' : '' }}">

              @if(isset($data['errors']['titulo']))
              <div class="invalid-feedback d-block">{{ $data['errors']['titulo'] }}</div>
              @endif
            </td>
            <!-- 4. Centro/Departamento -->
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][centro_departamento]"
                value="{{ $data['centro_departamento'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <!-- 5. Situacao -->
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][situacao]" value="{{ $data['situacao'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <!-- 6. Data Início -->
            <td class="p-1">
              <input type="date" name="projects[{{ $index }}][data_inicio]" value="{{ $data['data_inicio'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <!-- 7. Data Fim -->
            <td class="p-1">
              <input type="date" name="projects[{{ $index }}][data_fim]" value="{{ $data['data_fim'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <!-- 8. Data Atualizacao -->
            <td class="p-1">
              <input type="datetime" name="projects[{{ $index }}][data_atualizacao]"
                value="{{ $data['data_atualizacao'] ?? '' }}" class="form-control form-control-sm form-control-flush">
            </td>
            <!-- 9. Resumo -->
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][resumo]" value="{{ $data['resumo'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <!-- 10. Palavras Chave -->
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][palavras_chave]"
                value="{{ $data['palavras_chave'] ?? '' }}" class="form-control form-control-sm form-control-flush">
            </td>
            <!-- 11. Tipo Ação -->
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][tipo_acao]" value="{{ $data['tipo_acao'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <!-- 11. Área Temática -->
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][area_tematica]" value="{{ $data['area_tematica'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <!-- 12. Modalidade -->
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][modalidade]" value="{{ $data['modalidade'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <!-- 13. Com bolsa -->
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][com_bolsa]" value="{{ $data['com_bolsa'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <!-- 14. Ods -->
            <td class="p-1">
              <input type="text" name="projects[{{ $index }}][ods]" value="{{ $data['ods'] ?? '' }}"
                class="form-control form-control-sm form-control-flush">
            </td>
            <!-- 16. Proponente -->
            <td class="p-1" style="min-width: 200px;">
              <input type="text" name="projects[{{ $index }}][proponente]" value="{{ $data['proponente'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['proponente']) ? 'is-invalid' : '' }}">

              @if(isset($data['errors']['proponente']))
              <div class="invalid-feedback d-block">{{ $data['errors']['proponente'] }}</div>
              @endif
            </td>
            <!-- 17. Email -->
            <td class="p-1" style="min-width: 200px;">
              <input type="text" name="projects[{{ $index }}][email_proponente]" value="{{ $data['email_proponente'] ?? '' }}"
                class="form-control form-control-sm form-control-flush input-watch {{ isset($data['errors']['email']) ? 'is-invalid' : '' }}">

              @if(isset($data['errors']['email']))
              <div class="invalid-feedback d-block">{{ $data['errors']['email'] }}</div>
              @endif
            </td>
            <!-- 18. Ações -->
            <td class="p-1 text-center align-middle">
              <button type="button" onclick="removeRow({{ $index }})" class="btn btn-sm btn-danger">
                Excluir
              </button>
            </td>
          </tr>
          @endforeach
        </tbody>
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-end">
      <button type="submit" class="btn btn-success">Salvar</button>
    </div>
  </form>
</div>
@endsection

@section('scripts')
<script>
    function removeRow(index) {
        const row = document.getElementById('row_' + index);
        if (row) {
            row.remove();
        }
    }

    document.querySelectorAll('.input-watch').forEach(input => {
        input.addEventListener('input', function() {
            if(this.value.trim() !== '') {
                this.classList.remove('border-red-500', 'bg-red-50');
                this.classList.add('border-gray-300');
            }
        });
    });
</script>
@endsection