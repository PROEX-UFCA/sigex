@extends('pages.vitrine.template')

@section('content')
<div class="page-header">
  <div class="">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="text-uppercase">
          <a href="{{ route('profile.index') }}" class="text-yellow fw-bold f">Perfil da Instituição</a>
        </div>
        <h2 class="page-title">
          Informações
        </h2>
      </div>
      <div class="col-auto ms-auto">
      </div>
    </div>
  </div>
</div>
<div class="page-body row">
    <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-2 badge bg-brown w-auto mb-3 p-2 d-inline-flex">
            <i class="ti ti-user icon fs-2 m-0 p-0"></i>
            <h3 class="text-capitalize fs-2 m-0 p-0">{{ $instituicao->nome }}</h3>
        </div>
        <div class="text-muted row">
            <div class="col">
                <h4 class="border-bottom border-brown pb-2">Dados gerais</h4>
                <p><strong>CNPJ:</strong> {{ $instituicao->cnpj }}</p>
                <p><strong>Email:</strong> {{ $instituicao->email }}</p>
                <p><strong>Telefone:</strong> {{ $instituicao->telefone_contato }}</p>
            </div>
            <div class="col">
                <h4 class="border-bottom border-brown pb-2">Endereço</h4>
                <p>{{ $instituicao->logradouro }}, {{ $instituicao->numero }}</p>
                <p>{{ $instituicao->complemento }}</p>
                <p><strong>CEP:</strong> {{ $instituicao->cep }}</p>
            </div>
        </div>
    </div>
</div>
@endsection