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


    <style>
        /* @import url('https://rsms.me/inter/inter.css'); */

        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }

        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
    </style>
</head>

<body class="body-marketing body-gradient">
    <script src="{{ asset('assets/js/demo-theme.min.js?1684106062') }}"></script>
    <div class="page">
        <header class="navbar navbar-expand-lg navbar-transparent py-3">
            <div class="container">
                <a href=".." aria-label="Tabler" class="navbar-brand navbar-brand-autodark">
                    <img src="{{asset('assets/img/illustrations/logo_proex_top.png')}}" alt="" style="width: 200px;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                    <nav class="navbar-nav ms-auto">
                        <div class="nav-item">
                            <a class="nav-link active" href="{{route('vitrine.vitrine')}}"><span class="nav-link-title">Início</span></a>
                        </div>
                        <div class="nav-item">
                            <a class="nav-link" href="../marketing/testimonials.html"><span
                            class="nav-link-title">Filtros</span></a>
                        </div>
                        <div class="nav-item">
                            <a class="nav-link" href="../marketing/pricing.html"><span
                            class="nav-link-title">Catálogo</span></a>
                        </div>
                        <div class="d-flex justify-content-end p-0 m-0">
                            <x-table.search route=""></x-table.search>
                        </div>
                        <div class="nav-item ms-4">
                            <a href="{{route('login')}}" class="btn btn-yellow">
                                <i class="ti ti-user icon"></i>
                                Faça seu login 
                            </a>
                        </div>
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