<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'PRISMA UHN - Portal Riset, Pengabdian & Insentif Dosen')</title>
    <meta name="description" content="PRISMA UHN adalah Portal Penelitian, Pengabdian Masyarakat, HKI, dan Insentif Dosen Universitas Harkat Negeri. Selaras BIMA Kemdiktisaintek.">
    
    <!-- Fonts: Plus Jakarta Sans for High Readability -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://img.icons8.com/color/96/prism.png">

    <!-- Styles & Scripts -->
    @if(file_exists(public_path('build/manifest.json')))
        @php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
            $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
        @endphp
        @if($cssFile)
            <link rel="stylesheet" href="/build/{{ $cssFile }}">
        @endif
        @if($jsFile)
            <script type="module" src="/build/{{ $jsFile }}"></script>
        @endif
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        svg {
            max-width: 100%;
            height: auto;
        }
        .w-6 { width: 1.5rem !important; }
        .h-6 { height: 1.5rem !important; }
        .w-5 { width: 1.25rem !important; }
        .h-5 { height: 1.25rem !important; }
        .w-4 { width: 1rem !important; }
        .h-4 { height: 1rem !important; }
        .w-10 { width: 2.5rem !important; }
        .h-10 { height: 2.5rem !important; }
        *, *::before, *::after {
            font-variant-numeric: normal !important;
            font-feature-settings: "zero" 0 !important;
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 16px;
        }
        code, kbd, samp, pre, .font-mono, [class*="font-mono"] {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            font-variant-numeric: normal !important;
            font-feature-settings: "zero" 0 !important;
            letter-spacing: 0.025em;
        }
        .text-gradient, .text-gradient-blue, .text-gradient-maroon {
            background: linear-gradient(135deg, #681727 0%, #a82541 50%, #c99738 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .custom-sidebar-scroll {
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }
        .custom-sidebar-scroll::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-blue-600 selection:text-white flex flex-col min-h-screen">
    @yield('content')
</body>
</html>
