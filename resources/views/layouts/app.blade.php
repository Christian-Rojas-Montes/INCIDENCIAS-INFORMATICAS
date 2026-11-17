<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        try {
            const savedTheme = localStorage.getItem('incidencias-theme');
            document.documentElement.dataset.theme = savedTheme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        } catch (error) {
            document.documentElement.dataset.theme = 'light';
        }
    </script>
    <title>@yield('title', 'Panel') · {{ config('app.name', 'Atención de Incidencias') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --ink: #172b4d; --muted: #6b7a90; --brand: #2457d6; --line: #e8edf4; --page: #f5f7fb; --surface: #fff; --field: #fff; }
        html { color-scheme: light; }
        html[data-theme="dark"] { color-scheme: dark; --ink: #e7edf7; --muted: #a4b1c5; --brand: #83a8ff; --line: #2c3b51; --page: #101827; --surface: #182438; --field: #111d2e; }
        body { background: var(--page); color: var(--ink); font-family: 'DM Sans', sans-serif; transition: background-color .35s ease, color .35s ease; }
        h1,h2,h3,h4,h5,.navbar-brand { font-family: 'Manrope', sans-serif; }
        .navbar { background: var(--surface); border-bottom: 1px solid var(--line); transition: background-color .35s ease, border-color .35s ease; }
        .navbar-brand { color: var(--ink); font-weight: 800; letter-spacing: -.04em; }
        .brand-mark { width: 36px; height: 36px; border-radius: 11px; display: inline-grid; place-items: center; color: #fff; background: var(--brand); margin-right: .55rem; }
        .nav-link { color: #5e6d82; font-weight: 600; font-size: .92rem; }
        .nav-link.active,.nav-link:hover { color: var(--brand); }
        .page-title { font-weight: 800; letter-spacing: -.045em; }
        .card { color: var(--ink); background: var(--surface); border: 1px solid var(--line); border-radius: 16px; box-shadow: 0 5px 20px rgba(30,55,90,.035); transition: background-color .35s ease, color .35s ease, border-color .35s ease, box-shadow .35s ease; }
        .card-header { background: transparent; border-bottom: 1px solid var(--line); }
        .btn { border-radius: 9px; font-weight: 600; }
        .btn-primary { background: var(--brand); border-color: var(--brand); }
        .form-control,.form-select { color: var(--ink); background-color: var(--field); border-color: #dbe3ef; border-radius: 9px; min-height: 43px; transition: background-color .35s ease, color .35s ease, border-color .35s ease; }
        .form-control:focus,.form-select:focus { border-color: #8daaf2; box-shadow: 0 0 0 .2rem rgba(36,87,214,.12); }
        .table > :not(caption) > * > * { padding: .85rem .8rem; border-bottom-color: var(--line); vertical-align: middle; }
        .table thead th { color: var(--muted); font-size: .75rem; letter-spacing: .06em; text-transform: uppercase; font-weight: 700; }
        .text-muted { color: var(--muted) !important; }
        .metric-card { min-height: 132px; }
        .metric-number { font-family: 'Manrope', sans-serif; font-weight: 800; letter-spacing: -.05em; }
        .metric-icon { display: grid; place-items: center; height: 42px; width: 42px; border-radius: 12px; background: #edf2ff; color: var(--brand); font-size: 1.1rem; }
        .hero-panel { background: linear-gradient(120deg,#193c98,#2866e8 72%,#53a6ff); border: 0; color: white; overflow: hidden; }
        .hero-panel .text-white-50 { color: rgba(255,255,255,.72) !important; }
        .table-responsive { border-radius: 12px; }
        .pagination { margin-bottom: 0; }
        footer { color: var(--muted); font-size: .85rem; }
        .theme-toggle { color: var(--ink); background: transparent; border: 1px solid var(--line); min-width: 42px; }
        .theme-toggle:hover { color: var(--brand); background: color-mix(in srgb, var(--brand) 9%, var(--surface)); border-color: var(--brand); }
        .theme-icon { display: inline-block; transition: transform .45s cubic-bezier(.2,.8,.2,1); }
        html[data-theme="dark"] .theme-icon { transform: rotate(180deg); }
        html[data-theme="dark"] .navbar-brand, html[data-theme="dark"] .nav-link { color: var(--ink); }
        html[data-theme="dark"] .nav-link.active, html[data-theme="dark"] .nav-link:hover { color: var(--brand); }
        html[data-theme="dark"] .navbar-toggler { border-color: var(--line); }
        html[data-theme="dark"] .navbar-toggler-icon { filter: invert(1); }
        html[data-theme="dark"] .brand-mark { background: #315cb2; }
        html[data-theme="dark"] .metric-icon { color: #b8ccff; background: #263b62; }
        html[data-theme="dark"] .hero-panel .btn-light { color: #193c98; background: #fff; border-color: #fff; }
        html[data-theme="dark"] .table { --bs-table-color: var(--ink); --bs-table-bg: transparent; --bs-table-border-color: var(--line); --bs-table-striped-color: var(--ink); --bs-table-hover-color: var(--ink); }
        html[data-theme="dark"] .table > :not(caption) > * > * { color: var(--ink); background-color: transparent; border-bottom-color: var(--line); }
        html[data-theme="dark"] .form-control, html[data-theme="dark"] .form-select { border-color: #3a4b64; }
        html[data-theme="dark"] .form-control::placeholder { color: #91a0b7; }
        html[data-theme="dark"] .form-select option { color: var(--ink); background: var(--surface); }
        html[data-theme="dark"] .pagination { --bs-pagination-color: var(--ink); --bs-pagination-bg: var(--surface); --bs-pagination-border-color: var(--line); --bs-pagination-hover-color: #fff; --bs-pagination-hover-bg: #30445f; --bs-pagination-hover-border-color: #405773; --bs-pagination-disabled-bg: var(--surface); --bs-pagination-disabled-border-color: var(--line); }
        html[data-theme="dark"] .btn-light { color: var(--ink); background: #26364d; border-color: #354760; }
        html[data-theme="dark"] .btn-light:hover { background: #30445f; border-color: #405773; }
        html[data-theme="dark"] .text-bg-light { color: #e7edf7 !important; background-color: #2a3a52 !important; }
        html[data-theme="dark"] .bg-primary-subtle { background-color: #263b62 !important; }
        html[data-theme="dark"] .alert-success { color: #b9efd1; background: #163b31; border-color: #27604c; }
        html[data-theme="dark"] .alert-danger { color: #ffc4c7; background: #48262d; border-color: #753a43; }
        html[data-theme="dark"] .alert-warning { color: #ffe2a6; background: #483a20; border-color: #705923; }
        html[data-theme="dark"] .border { border-color: var(--line) !important; }
        html.theme-transition body::before { content: ''; position: fixed; inset: 0; z-index: 2000; pointer-events: none; background: var(--page); clip-path: circle(0 at var(--theme-x, 50%) var(--theme-y, 50%)); animation: theme-reveal .62s cubic-bezier(.22,.75,.2,1) forwards; }
        @keyframes theme-reveal { to { clip-path: circle(150vmax at var(--theme-x, 50%) var(--theme-y, 50%)); opacity: 0; } }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; animation-duration: .01ms !important; animation-iteration-count: 1 !important; } }
    </style>
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-xl sticky-top">
    <div class="container-fluid px-lg-5">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}"><span class="brand-mark">✦</span> Mesa TI</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Abrir navegación"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mainNav">
            <div class="navbar-nav ms-xl-4 gap-xl-1">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Resumen</a>
                <a class="nav-link {{ request()->routeIs('incidencias.*') ? 'active' : '' }}" href="{{ route('incidencias.index') }}">Incidencias</a>
                <a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">Usuarios</a>
                <a class="nav-link {{ request()->routeIs('tecnicos.*') ? 'active' : '' }}" href="{{ route('tecnicos.index') }}">Técnicos</a>
                <a class="nav-link {{ request()->routeIs('prioridades.*') ? 'active' : '' }}" href="{{ route('prioridades.index') }}">Prioridades</a>
                <a class="nav-link {{ request()->routeIs('seguimientos.*') ? 'active' : '' }}" href="{{ route('seguimientos.index') }}">Seguimiento</a>
            </div>
            <button class="btn theme-toggle ms-xl-3 mt-2 mt-xl-0" id="theme-toggle" type="button" aria-label="Activar modo oscuro" title="Activar modo oscuro"><span class="theme-icon" id="theme-icon" aria-hidden="true">☾</span><span class="d-xl-none ms-2" id="theme-label">Modo oscuro</span></button>
            <span class="ms-auto small text-muted d-none d-xl-inline">Sistema de Atención de Incidencias</span>
        </div>
    </div>
</nav>
<main class="container-fluid px-lg-5 py-4 py-lg-5">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button></div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button></div>
    @endif
    @yield('content')
</main>
<footer class="container-fluid px-lg-5 pb-4">Sistema de Atención de Incidencias Informáticas · Parte II</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (() => {
        const root = document.documentElement;
        const toggle = document.getElementById('theme-toggle');
        const icon = document.getElementById('theme-icon');
        const label = document.getElementById('theme-label');

        const updateToggle = () => {
            const dark = root.dataset.theme === 'dark';
            const action = dark ? 'Activar modo claro' : 'Activar modo oscuro';
            icon.textContent = dark ? '☀' : '☾';
            toggle.setAttribute('aria-label', action);
            toggle.setAttribute('title', action);
            label.textContent = dark ? 'Modo claro' : 'Modo oscuro';
        };

        toggle.addEventListener('click', (event) => {
            const bounds = toggle.getBoundingClientRect();
            root.style.setProperty('--theme-x', `${bounds.left + bounds.width / 2}px`);
            root.style.setProperty('--theme-y', `${bounds.top + bounds.height / 2}px`);
            root.classList.remove('theme-transition');
            void root.offsetWidth;
            root.dataset.theme = root.dataset.theme === 'dark' ? 'light' : 'dark';
            root.classList.add('theme-transition');
            try { localStorage.setItem('incidencias-theme', root.dataset.theme); } catch (error) {}
            updateToggle();
            window.setTimeout(() => root.classList.remove('theme-transition'), 700);
        });

        updateToggle();
    })();
</script>
@stack('scripts')
</body>
</html>
