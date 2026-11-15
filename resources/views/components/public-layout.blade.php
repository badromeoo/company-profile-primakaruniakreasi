@props([
    'title' => null,
    'description' => null,
    'noindex' => false,
    'seo' => null,
])

@php
    $pageTitle = $title ? $title.' — '.config('app.name', 'Laravel') : config('app.name', 'Laravel');
    $isHome = request()->routeIs('home');
    $homeUrl = route('home');

    $publicMenu = [
        ['label' => 'Services', 'href' => $isHome ? '#services' : $homeUrl.'#services'],
        ['label' => 'Our Clients', 'href' => $isHome ? '#clients' : $homeUrl.'#clients'],
        ['label' => 'Projects', 'href' => $isHome ? '#projects' : $homeUrl.'#projects'],
        ['label' => 'Blog', 'href' => route('blog.index'), 'active' => request()->routeIs('blog.*')],
        ['label' => 'About Us', 'href' => $isHome ? '#about' : $homeUrl.'#about'],
        ['label' => 'FAQ', 'href' => $isHome ? '#faq' : $homeUrl.'#faq'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @if (filled($seo))
            {!! $seo !!}
        @else
            <title>{{ $pageTitle }}</title>

            @if ($description)
                <meta name="description" content="{{ $description }}">
            @endif

            @if ($noindex)
                <meta name="robots" content="noindex, nofollow">
            @endif
        @endif

        <link rel="alternate" type="application/rss+xml" title="{{ config('app.name') }} Blog" href="{{ url('/feed') }}">

        <!-- Fonts -->
        <x-font-links />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen text-body font-sans text-gray-900 antialiased dark:text-gray-100">
        {{-- Preloader: tampil sejak paint pertama, memudar setelah aset selesai dimuat. --}}
        <div
            id="page-preloader"
            class="fixed inset-0 z-50 flex items-center justify-center bg-white transition-opacity duration-500 ease-out dark:bg-gray-950"
            aria-hidden="true"
        >
            <img
                src="{{ asset('images/logo.webp') }}"
                alt="PRIMA Karunia Kreasi"
                class="h-16 w-auto animate-pulse object-contain sm:h-20"
            />
        </div>

        <noscript>
            <style>
                #page-preloader {
                    display: none !important;
                }
            </style>
        </noscript>

        <script>
            (() => {
                const preloader = document.getElementById('page-preloader');

                if (!preloader) {
                    return;
                }

                let isHidden = false;

                const hidePreloader = () => {
                    if (isHidden) {
                        return;
                    }

                    isHidden = true;

                    preloader.classList.add('pointer-events-none', 'opacity-0');
                    window.setTimeout(() => preloader.remove(), 500);
                };

                if (document.readyState === 'complete') {
                    window.setTimeout(hidePreloader, 300);
                } else {
                    window.addEventListener('load', () => window.setTimeout(hidePreloader, 300), { once: true });
                }

                // Jaring pengaman: overlay tidak boleh mengunci halaman bila event load terlewat.
                window.setTimeout(hidePreloader, 3000);
            })();
        </script>

        {{-- Global background canvas: one fixed layer behind every section. --}}
        <div
            class="pointer-events-none fixed inset-0 -z-10 bg-[#eff4fd] bg-cover bg-center bg-no-repeat dark:bg-gray-950"
            style="background-image: url('{{ asset('images/background-web.webp') }}')"
            aria-hidden="true"
        >
            {{-- Contrast scrim: subtle on top, stronger towards the deeper blue at the bottom. --}}
            <div class="absolute inset-0 bg-gradient-to-b from-white/5 via-white/15 to-white/60 dark:from-gray-950/75 dark:via-gray-950/80 dark:to-gray-950/90"></div>
        </div>

        <header class="sticky top-0 z-40 border-b border-white/40 bg-white/45 backdrop-blur-md dark:border-white/10 dark:bg-gray-900/40">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <a href="{{ $homeUrl }}" class="flex items-center">
                    <img src="{{ asset('images/logo.webp') }}" alt="PRIMA Karunia Kreasi" class="h-10 w-auto object-contain sm:h-12">
                </a>

                <nav class="hidden items-center gap-6 text-meta font-medium lg:flex">
                    @foreach ($publicMenu as $item)
                        <a
                            href="{{ $item['href'] }}"
                            @if ($item['active'] ?? false) aria-current="page" @endif
                            class="transition {{ ($item['active'] ?? false) ? 'font-semibold text-gray-900 dark:text-white' : 'text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white' }}"
                        >
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <button
                    type="button"
                    id="public-menu-toggle"
                    aria-controls="public-menu"
                    aria-expanded="false"
                    aria-label="Buka menu navigasi"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-white/60 bg-white/50 text-gray-700 transition hover:bg-white/80 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-white/10 dark:bg-gray-900/40 dark:text-gray-300 dark:hover:bg-gray-800/60 dark:focus:ring-offset-gray-900 lg:hidden"
                >
                    <svg id="public-menu-icon-open" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg id="public-menu-icon-close" class="hidden h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </header>

        {{-- Panel mobile dipisah dari <header> agar backdrop-filter mem-blur konten halaman; blur & latar disamakan dengan navbar. --}}
        <div
            id="public-menu"
            class="invisible pointer-events-none fixed inset-x-0 top-16 z-30 -translate-y-2 bg-white/45 opacity-0 shadow-lg backdrop-blur-md transition-all duration-200 ease-out dark:bg-gray-900/40 lg:hidden"
        >
            <nav class="mx-auto flex max-w-7xl flex-col items-center gap-1 px-4 py-3 text-center sm:px-6">
                @foreach ($publicMenu as $item)
                    <a
                        href="{{ $item['href'] }}"
                        @if ($item['active'] ?? false) aria-current="page" @endif
                        class="w-full rounded-md px-3 py-2.5 text-center text-meta font-medium transition {{ ($item['active'] ?? false) ? 'bg-white/60 text-gray-900 dark:bg-white/10 dark:text-white' : 'text-gray-700 hover:bg-white/60 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-white/10 dark:hover:text-white' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>


        <script>
            (() => {
                const toggle = document.getElementById('public-menu-toggle');
                const panel = document.getElementById('public-menu');
                const iconOpen = document.getElementById('public-menu-icon-open');
                const iconClose = document.getElementById('public-menu-icon-close');

                if (!toggle || !panel) {
                    return;
                }

                const isOpen = () => !panel.classList.contains('invisible');

                const closedClasses = ['invisible', 'pointer-events-none', '-translate-y-2', 'opacity-0'];

                const setOpen = (open) => {
                    closedClasses.forEach((className) => {
                        panel.classList.toggle(className, !open);
                    });

                    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                    toggle.setAttribute('aria-label', open ? 'Tutup menu navigasi' : 'Buka menu navigasi');

                    if (iconOpen) {
                        iconOpen.classList.toggle('hidden', open);
                    }

                    if (iconClose) {
                        iconClose.classList.toggle('hidden', !open);
                    }
                };

                toggle.addEventListener('click', () => setOpen(!isOpen()));

                panel.querySelectorAll('a').forEach((link) => {
                    link.addEventListener('click', () => setOpen(false));
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && isOpen()) {
                        setOpen(false);
                        toggle.focus();
                    }
                });

                document.addEventListener('click', (event) => {
                    if (!isOpen() || panel.contains(event.target) || toggle.contains(event.target)) {
                        return;
                    }

                    setOpen(false);
                });

                window.addEventListener('resize', () => {
                    if (window.innerWidth >= 1024 && isOpen()) {
                        setOpen(false);
                    }
                });
            })();
        </script>

        {{ $slot }}

        <footer class="mx-auto w-full max-w-7xl px-4 pb-8 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center justify-between gap-2 rounded-2xl bg-white/25 px-6 py-4 text-meta text-gray-600 shadow-sm ring-1 ring-white/50 backdrop-blur-sm dark:bg-gray-900/25 dark:text-gray-400 dark:ring-white/10 sm:flex-row">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. {{ __('All rights reserved.') }}</p>

                <a href="{{ $homeUrl }}" class="transition hover:text-gray-900 dark:hover:text-white">
                    {{ __('Back to top') }}
                </a>
            </div>
        </footer>
    </body>
</html>

