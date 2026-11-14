<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen font-sans text-gray-900 antialiased dark:text-gray-100">
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
                <a href="{{ route('home') }}" class="flex items-center">
                    <img
                        src="{{ asset('images/logo.webp') }}"
                        alt="PRIMA Karunia Kreasi"
                        class="h-10 w-auto object-contain sm:h-12"
                    />
                </a>

                <nav class="flex items-center gap-4 text-sm font-medium">
                    <a href="#about" class="hidden text-gray-600 transition hover:text-gray-900 dark:text-gray-300 dark:hover:text-white sm:block">
                        {{ __('About') }}
                    </a>
                    <a href="#services" class="hidden text-gray-600 transition hover:text-gray-900 dark:text-gray-300 dark:hover:text-white sm:block">
                        {{ __('Services') }}
                    </a>

                    @auth
                        <a href="{{ url('/dashboard') }}" class="rounded-md border border-white/60 bg-white/50 px-4 py-2 text-gray-700 transition hover:bg-white/80 dark:border-white/10 dark:bg-gray-900/40 dark:text-gray-300 dark:hover:bg-gray-800/60">
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-md border border-white/60 bg-white/50 px-4 py-2 text-gray-700 transition hover:bg-white/80 dark:border-white/10 dark:bg-gray-900/40 dark:text-gray-300 dark:hover:bg-gray-800/60">
                            {{ __('Log in') }}
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 py-10 sm:px-6 lg:gap-8 lg:px-8 lg:py-14">
            <section id="hero" class="rounded-3xl bg-white/20 p-8 shadow-sm ring-1 ring-white/60 backdrop-blur-sm sm:p-12 lg:p-16 dark:bg-gray-900/25 dark:ring-white/10">
                <div class="max-w-3xl">
                    <p class="text-sm font-semibold uppercase tracking-widest text-indigo-600 dark:text-indigo-400">
                        {{ __('Company Profile') }}
                    </p>

                    <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl">
                        {{ $contents['hero_title'] ?? 'Welcome to Our Company' }}
                    </h1>

                    <p class="mt-6 text-lg leading-relaxed text-gray-600 dark:text-gray-300">
                        {{ $contents['hero_subtitle'] ?? 'We build digital experiences that help your business grow.' }}
                    </p>

                    <div class="mt-8">
                        <a href="#services" class="inline-flex items-center rounded-md bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 dark:focus:ring-offset-gray-900">
                            {{ $contents['hero_button_text'] ?? 'Learn More' }}
                        </a>
                    </div>
                </div>
            </section>

            <section id="about" class="rounded-3xl bg-white/30 p-8 shadow-sm ring-1 ring-white/60 backdrop-blur-sm sm:p-10 dark:bg-gray-900/30 dark:ring-white/10">
                <div class="grid gap-10 lg:grid-cols-2">
                    <h2 class="text-3xl font-bold tracking-tight">
                        {{ $contents['about_title'] ?? 'About Our Company' }}
                    </h2>

                    <p class="text-lg leading-relaxed text-gray-600 dark:text-gray-300">
                        {{ $contents['about_text'] ?? 'We are a team of professionals dedicated to helping your business succeed online.' }}
                    </p>
                </div>
            </section>

            <section id="services" class="rounded-3xl bg-white/30 p-8 shadow-sm ring-1 ring-white/60 backdrop-blur-sm sm:p-10 dark:bg-gray-900/30 dark:ring-white/10">
                <div class="max-w-2xl">
                    <h2 class="text-3xl font-bold tracking-tight">
                        {{ $contents['services_title'] ?? 'What We Do' }}
                    </h2>

                    <p class="mt-4 text-lg leading-relaxed text-gray-600 dark:text-gray-300">
                        {{ $contents['services_subtitle'] ?? 'A selection of the services we provide to our clients.' }}
                    </p>
                </div>

                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($services as $service)
                        <article class="rounded-2xl bg-white/45 p-6 shadow-sm ring-1 ring-white/70 backdrop-blur-sm dark:bg-gray-900/35 dark:ring-white/10">
                            <h3 class="text-lg font-semibold">
                                {{ $service['title'] }}
                            </h3>

                            <p class="mt-3 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                                {{ $service['description'] }}
                            </p>
                        </article>
                    @empty
                        <p class="text-gray-600 dark:text-gray-300">
                            {{ __('Our services will be published here soon.') }}
                        </p>
                    @endforelse
                </div>
            </section>

            <section id="contact" class="rounded-3xl bg-white/35 p-10 text-center shadow-sm ring-1 ring-white/60 backdrop-blur-sm sm:p-14 dark:bg-gray-900/30 dark:ring-white/10">
                <h2 class="text-3xl font-bold tracking-tight">
                    {{ $contents['cta_title'] ?? "Let's Work Together" }}
                </h2>

                <p class="mx-auto mt-4 max-w-2xl text-lg leading-relaxed text-gray-600 dark:text-gray-300">
                    {{ $contents['cta_text'] ?? 'Get in touch with our team to discuss your next project.' }}
                </p>

                <div class="mt-8">
                    <a href="mailto:{{ config('mail.from.address') }}" class="inline-flex items-center rounded-md bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 dark:focus:ring-offset-gray-900">
                        {{ $contents['cta_button_text'] ?? 'Get in Touch' }}
                    </a>
                </div>
            </section>
        </main>

        <footer class="mx-auto w-full max-w-7xl px-4 pb-8 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center justify-between gap-2 rounded-2xl bg-white/25 px-6 py-4 text-sm text-gray-600 shadow-sm ring-1 ring-white/50 backdrop-blur-sm dark:bg-gray-900/25 dark:text-gray-400 dark:ring-white/10 sm:flex-row">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. {{ __('All rights reserved.') }}</p>

                <a href="{{ route('home') }}" class="transition hover:text-gray-900 dark:hover:text-white">
                    {{ __('Back to top') }}
                </a>
            </div>
        </footer>
    </body>
</html>
