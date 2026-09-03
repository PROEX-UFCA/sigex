<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Proex</title>
    <!-- CSS files -->

    @yield('styles')

    <link href="{{ asset('assets/css/tabler-icons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/tabler.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/tabler-payments.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/tabler-vendors.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/demo.min.css') }}" rel="stylesheet" />
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
    </style>
</head>

<body class="body-marketing body-gradient">
    <script src="{{ asset('assets/js/demo-theme.min.js?1684106062') }}"></script>
    <div class="page">
        <header class="navbar navbar-expand-lg navbar-transparent py-3">
            <div class="container">
                <a href=".." aria-label="Tabler" class="navbar-brand navbar-brand-autodark">
                    <img src="{{asset('assets/img/illustrations/logo_proex_top.png')}}" alt="" style="width: 225px;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                    <nav class="navbar-nav ms-auto gap-2">
                        <div class="nav-item">
                            <a class="nav-link active px-1 px-xl-3" href="{{route('vitrine.vitrine')}}"><span class="nav-link-title">Início</span></a>
                        </div>
                        <div class="nav-item">
                            <a class="nav-link px-1 px-xl-3" href="../marketing/testimonials.html"><span
                            class="nav-link-title text-brown">Filtros</span></a>
                        </div>
                        <div class="nav-item">
                            <a class="nav-link px-1 px-xl-3" href="../marketing/pricing.html"><span
                            class="nav-link-title text-brown">Catálogo</span></a>
                        </div>
                        <div class="d-flex justify-content-end p-0 m-0">
                            <x-table.search route=""></x-table.search>
                        </div>

                        @auth
                            <div class="nav-item btn btn-yellow dropdown py-0 my-2 mx-2">
                                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Abrir menu do usuário">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ti ti-user icon fs-2 m-0 p-0"></i>
                                        
                                        <div class="d-flex flex-column text-start gap-0" style="max-width: 140px;">
                                            <div class="fw-bold fs-5 text-truncate" title="{{ Auth::user()->name }}">
                                                {{ Auth::user()->name }}
                                            </div>
                                            <div class="fw-light mt-1 fs-6 text-truncate" title="{{ Auth::user()->roles->first()->name }}">
                                                {{ ucfirst(Auth::user()->roles->first()->name) ?? '' }}
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <a href="{{ route('profile.index') }}" class="dropdown-item m-0">Perfil</a>
                                    <div class="dropdown-divider m-0"></div>
                                    <a href="{{ route('logout') }}" class="dropdown-item text-danger">Sair</a>
                                </div>
                            </div>
                        @else
                            <div class="nav-item px-2">
                                <a href="{{route('login')}}" class="btn btn-yellow">
                                    <i class="ti ti-user icon"></i>
                                    Faça seu login 
                                </a>
                            </div>
                        @endauth

                    </nav>
                </div>
            </div>
        </header>
        <div class="page-wrapper">
            <div class="container">
                @include('components.message.message')
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

    <script src="{{ asset('assets/js/jquery-3.5.1.js') }}"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script> --}}
    <script src="{{ asset('assets/js/echarts.min.js') }}"></script>
    <script src="{{ asset('assets/js/tabler.min.js?1684106062') }}" defer></script>
    <script src="{{ asset('assets/js/demo.min.js?1684106062') }}" defer></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> --}}
    <script src="{{ asset('assets/js/kanban/dataTables.min.js') }}"></script>
    @yield('scripts')

</body>

</html>
<script>
    document.addEventListener("DOMContentLoaded", function() {
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
      })
  });
</script>