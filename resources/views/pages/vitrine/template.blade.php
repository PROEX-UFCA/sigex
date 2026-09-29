<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>SIGEx - Vitrine</title>
    <!-- CSS files -->

    @yield('styles')

    <link href="{{ asset('assets/css/tabler-icons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/tabler.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/tabler-payments.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/tabler-vendors.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/demo.min.css') }}" rel="stylesheet" />
    <link rel="shortcut icon" href="{{asset('assets/img/illustrations/favicon.png')}}" type="image/x-icon">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" />


    <style>
        /* @import url('https://rsms.me/inter/inter.css'); */

        :root {
            --tblr-font-sans-serif: 'Alegreya', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }

        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }

        .title-clamp {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 3rem;
        }

        .first-access-modal .modal-dialog {
            width: min(720px, calc(100vw - 2rem));
            max-width: none;
            height: min(760px, calc(100vh - 2rem));
            margin: 1rem auto;
        }

        .first-access-modal .modal-content {
            height: 100%;
        }

        .first-access-modal .modal-body {
            overflow-y: auto;
        }

        .first-access-modal .form-selectgroup-item {
            width: auto;
            flex: 1 1 0;
        }

        .first-access-modal .first-access-choices {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .first-access-modal .first-access-choice .form-selectgroup-label {
            align-items: flex-start;
            min-height: 112px;
            padding: 1rem;
            line-height: 1.35;
            border: 1px solid rgba(83, 43, 29, 0.22);
            transition: border-color 160ms ease, background-color 160ms ease, box-shadow 160ms ease;
        }

        .first-access-modal .first-access-choice:hover .form-selectgroup-label {
            border-color: #F5BE56;
        }

        .first-access-modal .first-access-choice.is-selected .form-selectgroup-label {
            border-color: #F5BE56;
            background-color: rgba(245, 190, 86, 0.16);
            box-shadow: 0 0 0 2px rgba(245, 190, 86, 0.32);
        }

        .first-access-form-context {
            margin-bottom: 1.25rem;
            padding: 0.75rem 1rem;
            border-left: 4px solid #F5BE56;
            background: rgba(245, 190, 86, 0.14);
            color: #532B1D;
        }

        .first-access-form-context strong {
            display: block;
            color: #532B1D;
        }

        #loginModal .modal-header,
        #firstAccessModal .modal-header {
            background-color: #532B1D;
            color: #F5BE56;
        }

        #loginModal .modal-title,
        #firstAccessModal .modal-title {
            color: #F5BE56;
        }

        #loginModal .btn-close,
        #firstAccessModal .btn-close {
            filter: brightness(0) invert(1);
        }

        #loginModal .btn-primary,
        #firstAccessModal .btn-primary {
            background-color: #F5BE56;
            border-color: #F5BE56;
            color: #532B1D;
            font-weight: 700;
        }

        #loginModal .btn-primary:hover,
        #firstAccessModal .btn-primary:hover {
            background-color: #e5a943;
            border-color: #e5a943;
            color: #532B1D;
        }

        @media (max-width: 575.98px) {
            .first-access-modal .first-access-choices {
                grid-template-columns: 1fr;
            }

            .first-access-modal .first-access-choice .form-selectgroup-label {
                min-height: 0;
            }
        }

        @media (max-width: 575.98px) {
            .first-access-modal .modal-dialog {
                width: calc(100vw - 1rem);
                height: calc(100vh - 1rem);
                margin: 0.5rem auto;
            }
        }
    </style>
</head>

<body class="body-marketing body-gradient">
    <script src="{{ asset('assets/js/demo-theme.min.js?1684106062') }}"></script>
    <div class="page">
        <header class="navbar navbar-expand-lg py-3 bg-brown text-yellow">
            <div class="container">
                <a href="/vitrine" aria-label="Tabler" class="navbar-brand navbar-brand-autodark">
                    <img src="{{asset('assets/img/illustrations/logo_proex_top.png')}}" alt="" style="width: 225px;">
                </a>
                <button class="navbar-toggler text-yellow" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                    <nav class="navbar-nav ms-auto gap-3 align-items-center">

                        <div class="nav-item">
                            <a class="row nav-link active px-1 px-xl-3 {{ request()->routeIs('vitrine.vitrine') ? 'border-bottom border-1 border-yellow' : '' }}"
                                href="{{ route('vitrine.vitrine') }}">
                                <i class="col-auto ti ti-home icon fs-2 m-0 p-0"></i>
                                <span class="fw-bold col nav-link-title">Início</span>
                            </a>
                        </div>

                        <div class="nav-item">
                            <a class="row nav-link text-yellow px-1 px-xl-3 {{ request()->routeIs('vitrine.catalogo') ? 'border-bottom border-1 border-yellow' : '' }}"
                                href="{{ route('vitrine.catalogo') }}" title="Catálogo">
                                <i class="col-auto ti ti-layout-grid icon fs-2 m-0 p-0"></i>
                                <span class="fw-bold col nav-link-title">Catálogo</span>
                            </a>
                        </div>

                        <div class="d-flex p-0">
                            <x-table.vitrine-search route="{{ route('vitrine.catalogo') }}"></x-table.vitrine-search>
                        </div>

                        @auth
                            <div class="nav-item btn btn-yellow dropdown py-1 px-2 m-0">
                                <a href="#" class="nav-link d-flex lh-1 p-0" data-bs-toggle="dropdown">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ti ti-user icon fs-2 m-0 p-0"></i>
                                        <div class="d-flex flex-column text-start gap-0" style="max-width: 140px;">
                                            <div class="fw-bold fs-5 text-truncate">{{ Auth::user()->name }}</div>
                                            <div class="fw-light mt-1 fs-6 text-truncate">
                                                {{ ucfirst(Auth::user()->roles->first()->name) ?? '' }}
                                            </div>
                                        </div>
                                    </div>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    @if(Auth::user()->id_instituicao)
                                        <a href="{{ route('vitrine.profile') }}" class="dropdown-item m-0">Perfil</a>
                                    @else
                                        <a href="{{ route('profile.index') }}" class="dropdown-item m-0">Perfil</a>
                                    @endif
                                    <div class="dropdown-divider m-0"></div>
                                    <a href="{{ route('logout') }}" class="dropdown-item text-danger">Sair</a>
                                </div>
                            </div>
                        @else
                            <div class="nav-item m-0">
                                <button type="button" class="btn btn-yellow" data-bs-toggle="modal"
                                    data-bs-target="#loginModal">
                                    <i class="ti ti-user icon"></i>
                                    Faça seu login
                                </button>
                            </div>
                        @endauth
                    </nav>
                </div>
            </div>
        </header>
        <div class="page-wrapper">
            <div class="container">
                @if (!session()->has('login_modal') && !session()->has('first_access_modal') && !$errors->has('email') && !$errors->has('password'))
                    @include('components.message.message')
                @endif
                @yield('content')
            </div>

            <footer class="footer footer-transparent d-print-none">
                <div class="container-xl">
                    <div class="row text-center align-items-center flex-row-reverse">
                        <div class="col-lg-auto ms-lg-auto">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item"><a href="" target="_blank" class="link-secondary"
                                        rel="noopener">Suport</a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item">
                                    <a href="." class="link-secondary">Proex</a>&copy; 2026.
                                    All rights reserved.
                                </li>
                                <li class="list-inline-item">
                                    <a href="./changelog.html" class="link-secondary" rel="noopener">
                                        v1.0.0-beta
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <div class="modal modal-blur fade" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Fazer login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form action="{{ route('login.store') }}" method="post" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="login-email">Email</label>
                            <input type="email" class="form-control" id="login-email" name="email"
                                placeholder="Digite seu email" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="login-password">Senha</label>
                            <input type="password" class="form-control" id="login-password" name="password"
                                placeholder="Digite sua senha" required>
                        </div>
                        @if (session()->has('login_modal') || $errors->has('email') || $errors->has('password'))
                            @include('components.message.message')
                        @endif
                    </div>
                    <div class="modal-footer d-block">
                        <button type="submit" class="btn btn-primary w-100">Fazer login</button>
                        <div class="d-grid gap-2 mt-3">
                            <a class="btn btn-outline-secondary w-100" href="{{ route('login.reset') }}">
                                <i class="ti ti-key me-1" aria-hidden="true"></i>Recuperar senha
                            </a>
                            <button type="button" class="btn btn-outline-secondary w-100"
                                data-bs-target="#firstAccessModal" data-bs-toggle="modal">
                                <i class="ti ti-user-plus me-1" aria-hidden="true"></i>Solicitar primeiro acesso
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade first-access-modal" id="firstAccessModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Primeiro acesso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    @include('pages.authentication.partials.first-access-form')
                </div>
                <div class="modal-footer justify-content-center">
                    <span class="text-muted">Já possui acesso?</span>
                    <button type="button" class="btn btn-link p-0" data-bs-target="#loginModal"
                        data-bs-toggle="modal">Fazer login</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery-3.5.1.js') }}"></script>
    {{--
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script> --}}
    <script src="{{ asset('assets/js/echarts.min.js') }}"></script>
    <script src="{{ asset('assets/js/tabler.min.js?1684106062') }}" defer></script>
    <script src="{{ asset('assets/js/demo.min.js?1684106062') }}" defer></script>
    {{--
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> --}}
    <script src="{{ asset('assets/js/kanban/dataTables.min.js') }}"></script>
    @yield('scripts')

</body>

</html>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        @if (session()->has('login_modal') || $errors->has('email') || $errors->has('password'))
            bootstrap.Modal.getOrCreateInstance(document.getElementById('loginModal')).show();
        @endif

        @if (session()->has('first_access_modal') || $errors->hasAny(['is_external_institution', 'aceite', 'nome', 'cnpj', 'cep', 'logradouro', 'numero', 'complemento', 'telefone_contato']))
            bootstrap.Modal.getOrCreateInstance(document.getElementById('firstAccessModal')).show();
        @endif

            const firstAccessModal = document.getElementById('firstAccessModal');
        if (firstAccessModal) {
            const radios = firstAccessModal.querySelectorAll('input[name="is_external_institution"]');
            const internalForm = firstAccessModal.querySelector('#modal-form-internal');
            const externalForm = firstAccessModal.querySelector('#modal-form-external');
            const formContext = firstAccessModal.querySelector('#first-access-form-context');
            const choices = firstAccessModal.querySelectorAll('.first-access-choice');

            function toggleFirstAccessForm(value) {
                const internal = value === '0';
                internalForm.classList.toggle('d-none', !internal);
                externalForm.classList.toggle('d-none', internal);
                choices.forEach((choice) => {
                    choice.classList.toggle('is-selected', choice.querySelector('input').value === value);
                });
                formContext.classList.remove('d-none');
                formContext.innerHTML = internal
                    ? '<strong>Cadastro da comunidade UFCA</strong>Preencha seus dados para solicitar o primeiro acesso.'
                    : '<strong>Cadastro de instituição</strong>Preencha os dados da instituição interessada nas ações de extensão.';

                [internalForm, externalForm].forEach((container) => {
                    container.querySelectorAll('input, select, textarea').forEach((input) => {
                        const enabled = container === (internal ? internalForm : externalForm);
                        input.disabled = !enabled;
                        input.required = enabled && input.hasAttribute('data-required');
                    });
                });
            }

            radios.forEach((radio) => {
                radio.addEventListener('change', () => toggleFirstAccessForm(radio.value));
                if (radio.checked) toggleFirstAccessForm(radio.value);
            });

            $('#modal-cnpj').mask('00.000.000/0000-00');
            $('#modal-phone').mask('(00) 00000-0000');
            $('#modal-cep').mask('00000-000');
        }
    });
</script>