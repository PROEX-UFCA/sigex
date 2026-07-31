@php
    $oldStatus = old("validacao.{$id}.status", $data['validacao_salva']['status'] ?? 2);
    $oldFeedback = old("validacao.{$id}.feedback", $data['validacao_salva']['correcao'] ?? '');
@endphp

<input type="hidden" name="validacao[{{ $id }}][id_resposta]" value="{{ $id }}">

<div class="d-flex gap-3">
    <div class="form-check">
        <input class="form-check-input radio-status" type="radio"
            name="validacao[{{ $id }}][status]"
            id="aprovar_{{ $id }}" value="1" 
            {{ $oldStatus == 1 ? 'checked' : '' }} required>
        <label class="form-check-label text-success fw-semibold small" for="aprovar_{{ $id }}">
            Validar
        </label>
    </div>
    <div class="form-check">
        <input class="form-check-input radio-status" type="radio"
            name="validacao[{{ $id }}][status]"
            id="reprovar_{{ $id }}" value="0"
            {{ $oldStatus == 0 ? 'checked' : '' }}>
        <label class="form-check-label text-danger fw-semibold small" for="reprovar_{{ $id }}">
            Pedir Correção {{ $oldStatus == 0 ? 'checked' : '' }}
        </label>
    </div>
</div>

<div class="feedback-box mt-2" style="display: {{ $oldStatus == 0 ? 'block' : 'none' }};">
    <textarea class="form-control form-control-sm w-100"
        name="validacao[{{ $id }}][feedback]" rows="2"
        placeholder="Motivo da correção...">{{ $oldFeedback }}</textarea>
</div>

@if(!empty($data['validacao_salva']['avaliador']))
    <div class="mt-2 text-end">
        <small class="text-muted" style="font-size: 0.75rem;">
            Última avaliação por: <strong>{{ $data['validacao_salva']['avaliador']->name }}</strong>
        </small>
    </div>
@endif