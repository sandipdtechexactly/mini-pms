<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Minimal UI: Pico.css -->
        <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@2/css/pico.min.css">
        <!-- Icons: Remix Icon -->
        <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
        <style>
            /* Premium gradient table */
            table.premium {
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 10px 25px rgba(0,0,0,0.08);
                width: 100%;
            }
            table.premium thead th {
                background: linear-gradient(135deg, #6366f1 0%, #22d3ee 100%);
                color: #fff;
                font-weight: 600;
                white-space: nowrap;
            }
            table.premium tbody tr:hover {
                background: linear-gradient(135deg, rgba(99,102,241,0.10) 0%, rgba(34,211,238,0.10) 100%);
            }
            /* Compact icon buttons */
            .btn, [role="button"] { display: inline-flex; align-items: center; gap: .4rem; }
            .btn-sm { font-size: .85rem; padding: .35rem .6rem; border-radius: 8px; }
            .btn-primary { background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%); color: #fff; border: 0; }
            .btn-outline { border: 1px solid rgba(0,0,0,.15); background: transparent; }
            .icon { font-size: 1.05rem; line-height: 1; }
            /* Badges */
            .badge { padding: .25rem .5rem; border-radius: 999px; font-size: .75rem; }
            .badge-success { background: #16a34a; color: #fff; }
            .badge-primary { background: #2563eb; color: #fff; }
            .badge-warning { background: #d97706; color: #fff; }
            .badge-secondary { background: #64748b; color: #fff; }

            /* Global spacing & typography for better hierarchy */
            :root {
                --container-padding: clamp(12px, 2vw, 24px);
                --radius-lg: 12px;
                --shadow-lg: 0 10px 25px rgba(0,0,0,0.08);
            }
            body { font-size: clamp(14px, 1.1vw, 16px); line-height: 1.6; }
            h1 { font-size: clamp(1.4rem, 2.6vw, 2rem); margin: .5rem 0 1rem; }
            h2 { font-size: clamp(1.2rem, 2.2vw, 1.5rem); margin: .5rem 0 1rem; }
            h3 { font-size: clamp(1.05rem, 1.8vw, 1.25rem); }
            main.container { padding-inline: var(--container-padding); max-width: 1200px; margin-inline: auto; }

            /* Table responsiveness */
            .table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
            table.premium.compact th, table.premium.compact td { padding: .65rem .8rem; }
            table.premium.compact td { vertical-align: middle; }

            /* Mobile-first adjustments */
            @media (max-width: 768px) {
                .btn-sm { font-size: .8rem; padding: .35rem .55rem; }
                .icon { font-size: 1rem; }
                table.premium.compact th, table.premium.compact td { padding: .55rem .65rem; }
                table.premium thead th { position: sticky; top: 0; }
            }
        </style>
        <!-- Optional: keep app assets if present -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <main class="container">
                @if(session('success'))
                    <article class="success">{{ session('success') }}</article>
                @endif
                @if(session('error'))
                    <article class="contrast">{{ session('error') }}</article>
                @endif

                @if(isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </main>
        </div>
    </body>
</html>
