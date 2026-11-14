<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>

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

        {{-- Menu publik: target masih anchor; ganti ke route() saat halaman detail dibuat. --}}
        @php($publicMenu = [
            ['label' => 'Services', 'href' => '#services'],
            ['label' => 'Our Clients', 'href' => '#clients'],
            ['label' => 'Projects', 'href' => '#projects'],
            ['label' => 'Blog', 'href' => '#blog'],
            ['label' => 'About Us', 'href' => '#about'],
            ['label' => 'FAQ', 'href' => '#faq'],
        ])

        <header class="sticky top-0 z-40 border-b border-white/40 bg-white/45 backdrop-blur-md dark:border-white/10 dark:bg-gray-900/40">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img
                        src="{{ asset('images/logo.webp') }}"
                        alt="PRIMA Karunia Kreasi"
                        class="h-10 w-auto object-contain sm:h-12"
                    />
                </a>

                <nav class="hidden items-center gap-6 text-meta font-medium lg:flex">
                    @foreach ($publicMenu as $item)
                        <a href="{{ $item['href'] }}" class="text-gray-600 transition hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
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
                    <a href="{{ $item['href'] }}" class="w-full rounded-md px-3 py-2.5 text-center text-meta font-medium text-gray-700 transition hover:bg-white/60 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-white/10 dark:hover:text-white">
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

        <main class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 py-10 sm:px-6 lg:gap-8 lg:px-8 lg:py-14">
            <section id="hero" class="rounded-3xl bg-white/20 p-8 shadow-sm ring-1 ring-white/60 backdrop-blur-sm sm:p-12 lg:p-16 dark:bg-gray-900/25 dark:ring-white/10">
                {{-- TODO: pindahkan ID video ke home_contents (hero_video_id) bila ingin dikelola lewat CMS. --}}
                @php($youtubeVideoId = 'zuEcnUWsWg0')

                <div class="grid gap-10 lg:grid-cols-2 lg:items-center lg:gap-12">
                    <div>
                        <p class="text-meta font-semibold uppercase tracking-widest text-indigo-600 dark:text-indigo-400">
                            {{ __('Company Profile') }}
                        </p>

                        <h1 class="mt-4 text-hero font-bold">
                            {{ $contents['hero_title'] ?? 'Welcome to Our Company' }}
                        </h1>

                        <p class="mt-6 text-lead text-gray-600 dark:text-gray-300">
                            {{ $contents['hero_subtitle'] ?? 'We build digital experiences that help your business grow.' }}
                        </p>

                        <div class="mt-8">
                            <a href="#services" class="inline-flex items-center rounded-md bg-gray-900 px-5 py-3 text-meta font-semibold text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 dark:focus:ring-offset-gray-900">
                                {{ $contents['hero_button_text'] ?? 'Learn More' }}
                            </a>
                        </div>
                    </div>

                    <div>
                        <div class="overflow-hidden rounded-2xl shadow-sm ring-1 ring-white/60 dark:ring-white/10">
                            <div class="aspect-video">
                                <iframe
                                    src="https://www.youtube-nocookie.com/embed/{{ $youtubeVideoId }}?rel=0"
                                    title="Profil PRIMA Karunia Kreasi"
                                    class="h-full w-full"
                                    loading="lazy"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    referrerpolicy="strict-origin-when-cross-origin"
                                    allowfullscreen
                                ></iframe>
                            </div>
                        </div>

                        <p class="mt-3 text-meta text-gray-600 dark:text-gray-300">
                            <a href="https://www.youtube.com/watch?v={{ $youtubeVideoId }}" target="_blank" rel="noopener noreferrer" class="font-semibold underline-offset-2 transition hover:text-gray-900 hover:underline dark:hover:text-white">
                                {{ __('Tonton di YouTube') }}
                            </a>
                        </p>
                    </div>
                </div>
            </section>

            <section id="services" class="rounded-3xl bg-white/30 p-8 shadow-sm ring-1 ring-white/60 backdrop-blur-sm sm:p-10 dark:bg-gray-900/30 dark:ring-white/10">
                <div class="max-w-2xl">
                    <h2 class="text-h2 font-bold">
                        {{ $contents['services_title'] ?? 'What We Do' }}
                    </h2>

                    <p class="mt-4 text-lead text-gray-600 dark:text-gray-300">
                        {{ $contents['services_subtitle'] ?? 'A selection of the services we provide to our clients.' }}
                    </p>
                </div>

                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($services as $service)
                        <article class="rounded-2xl bg-white/45 p-6 shadow-sm ring-1 ring-white/70 backdrop-blur-sm dark:bg-gray-900/35 dark:ring-white/10">
                            <h3 class="text-h3 font-semibold">
                                {{ $service['title'] }}
                            </h3>

                            <p class="mt-3 text-meta text-gray-600 dark:text-gray-300">
                                {{ $service['description'] }}
                            </p>
                        </article>
                    @empty
                        <p class="text-gray-600 dark:text-gray-300">
                            {{ __('Our services will be published here soon.') }}
                        </p>
                    @endforelse
                </div>

                {{-- TODO: ganti ke route() saat halaman detail layanan dibuat --}}
                <div class="mt-10">
                    <a href="#services" class="inline-flex items-center rounded-md bg-gray-900 px-5 py-3 text-meta font-semibold text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 dark:focus:ring-offset-gray-900">
                        {{ __('Selengkapnya') }}
                    </a>
                </div>
            </section>

            <section id="clients" class="rounded-3xl bg-white/30 p-8 shadow-sm ring-1 ring-white/60 backdrop-blur-sm sm:p-10 dark:bg-gray-900/30 dark:ring-white/10">
                <div class="grid gap-10 lg:grid-cols-2">
                    <h2 class="text-h2 font-bold">
                        {{ $contents['clients_title'] ?? 'Our Clients' }}
                    </h2>

                    <div>
                        <p class="text-lead text-gray-600 dark:text-gray-300">
                            {{ $contents['clients_text'] ?? 'Kami dipercaya oleh instansi, UMKM, dan brand yang ingin tampil lebih profesional di dunia digital.' }}
                        </p>

                        {{-- TODO: ganti ke route() saat halaman detail klien dibuat --}}
                        <a href="#clients" class="mt-6 inline-flex items-center rounded-md bg-gray-900 px-5 py-3 text-meta font-semibold text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 dark:focus:ring-offset-gray-900">
                            {{ __('Selengkapnya') }}
                        </a>
                    </div>
                </div>
            </section>

            <section id="projects" class="rounded-3xl bg-white/30 p-8 shadow-sm ring-1 ring-white/60 backdrop-blur-sm sm:p-10 dark:bg-gray-900/30 dark:ring-white/10">
                <div class="grid gap-10 lg:grid-cols-2">
                    <h2 class="text-h2 font-bold">
                        {{ $contents['projects_title'] ?? 'Projects' }}
                    </h2>

                    <div>
                        <p class="text-lead text-gray-600 dark:text-gray-300">
                            {{ $contents['projects_text'] ?? 'Sebagian proyek pilihan yang menunjukkan cara kami membantu klien mengubah ide menjadi hasil yang terukur.' }}
                        </p>

                        {{-- TODO: ganti ke route() saat halaman detail proyek dibuat --}}
                        <a href="#projects" class="mt-6 inline-flex items-center rounded-md bg-gray-900 px-5 py-3 text-meta font-semibold text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 dark:focus:ring-offset-gray-900">
                            {{ __('Selengkapnya') }}
                        </a>
                    </div>
                </div>
            </section>

            <section id="blog" class="rounded-3xl bg-white/30 p-8 shadow-sm ring-1 ring-white/60 backdrop-blur-sm sm:p-10 dark:bg-gray-900/30 dark:ring-white/10">
                <div class="grid gap-10 lg:grid-cols-2">
                    <h2 class="text-h2 font-bold">
                        {{ $contents['blog_title'] ?? 'Blog' }}
                    </h2>

                    <div>
                        <p class="text-lead text-gray-600 dark:text-gray-300">
                            {{ $contents['blog_text'] ?? 'Insight, tips, dan kabar terbaru seputar desain, teknologi, dan pertumbuhan bisnis digital.' }}
                        </p>

                        {{-- TODO: ganti ke route() saat halaman blog dibuat --}}
                        <a href="#blog" class="mt-6 inline-flex items-center rounded-md bg-gray-900 px-5 py-3 text-meta font-semibold text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 dark:focus:ring-offset-gray-900">
                            {{ __('Selengkapnya') }}
                        </a>
                    </div>
                </div>
            </section>

            <section id="about" class="rounded-3xl bg-white/30 p-8 shadow-sm ring-1 ring-white/60 backdrop-blur-sm sm:p-10 dark:bg-gray-900/30 dark:ring-white/10">
                <div class="grid gap-10 lg:grid-cols-2">
                    <h2 class="text-h2 font-bold">
                        {{ $contents['about_title'] ?? 'About Our Company' }}
                    </h2>

                    <div>
                        <p class="text-lead text-gray-600 dark:text-gray-300">
                            {{ $contents['about_text'] ?? 'We are a team of professionals dedicated to helping your business succeed online.' }}
                        </p>

                        {{-- TODO: ganti ke route() saat halaman detail about dibuat --}}
                        <a href="#about" class="mt-6 inline-flex items-center rounded-md bg-gray-900 px-5 py-3 text-meta font-semibold text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 dark:focus:ring-offset-gray-900">
                            {{ __('Selengkapnya') }}
                        </a>
                    </div>
                </div>
            </section>

            <section id="faq" class="rounded-3xl bg-white/30 p-8 shadow-sm ring-1 ring-white/60 backdrop-blur-sm sm:p-10 dark:bg-gray-900/30 dark:ring-white/10">
                <div class="grid gap-10 lg:grid-cols-2">
                    <h2 class="text-h2 font-bold">
                        {{ $contents['faq_title'] ?? 'FAQ' }}
                    </h2>

                    <div>
                        <p class="text-lead text-gray-600 dark:text-gray-300">
                            {{ $contents['faq_text'] ?? 'Jawaban atas pertanyaan yang paling sering kami terima soal proses, waktu pengerjaan, dan biaya.' }}
                        </p>

                        {{-- TODO: ganti ke route() saat halaman FAQ dibuat --}}
                        <a href="#faq" class="mt-6 inline-flex items-center rounded-md bg-gray-900 px-5 py-3 text-meta font-semibold text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 dark:focus:ring-offset-gray-900">
                            {{ __('Selengkapnya') }}
                        </a>
                    </div>
                </div>
            </section>

            <section id="contact" class="rounded-3xl bg-white/35 p-10 text-center shadow-sm ring-1 ring-white/60 backdrop-blur-sm sm:p-14 dark:bg-gray-900/30 dark:ring-white/10">
                <h2 class="text-h2 font-bold">
                    {{ $contents['cta_title'] ?? "Let's Work Together" }}
                </h2>

                <p class="mx-auto mt-4 max-w-2xl text-lead text-gray-600 dark:text-gray-300">
                    {{ $contents['cta_text'] ?? 'Get in touch with our team to discuss your next project.' }}
                </p>

                <div class="mt-8">
                    <a href="mailto:{{ config('mail.from.address') }}" class="inline-flex items-center rounded-md bg-gray-900 px-5 py-3 text-meta font-semibold text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 dark:focus:ring-offset-gray-900">
                        {{ $contents['cta_button_text'] ?? 'Get in Touch' }}
                    </a>
                </div>
            </section>
        </main>

        <footer class="mx-auto w-full max-w-7xl px-4 pb-8 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center justify-between gap-2 rounded-2xl bg-white/25 px-6 py-4 text-meta text-gray-600 shadow-sm ring-1 ring-white/50 backdrop-blur-sm dark:bg-gray-900/25 dark:text-gray-400 dark:ring-white/10 sm:flex-row">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. {{ __('All rights reserved.') }}</p>

                <a href="{{ route('home') }}" class="transition hover:text-gray-900 dark:hover:text-white">
                    {{ __('Back to top') }}
                </a>
            </div>
        </footer>
    </body>
</html>
