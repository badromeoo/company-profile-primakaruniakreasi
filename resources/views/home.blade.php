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
    <body class="bg-white font-sans text-gray-900 antialiased dark:bg-gray-900 dark:text-gray-100">
        <header class="border-b border-gray-100 dark:border-gray-800">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-semibold">
                    <x-application-logo class="h-8 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    <span>{{ config('app.name', 'Laravel') }}</span>
                </a>

                <nav class="flex items-center gap-4 text-sm font-medium">
                    <a href="#about" class="hidden text-gray-600 transition hover:text-gray-900 dark:text-gray-400 dark:hover:text-white sm:block">
                        {{ __('About') }}
                    </a>
                    <a href="#services" class="hidden text-gray-600 transition hover:text-gray-900 dark:text-gray-400 dark:hover:text-white sm:block">
                        {{ __('Services') }}
                    </a>

                    @auth
                        <a href="{{ url('/dashboard') }}" class="rounded-md border border-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-md border border-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                            {{ __('Log in') }}
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            <section class="border-b border-gray-100 dark:border-gray-800">
                <div class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8 lg:py-32">
                    <div class="max-w-3xl">
                        <p class="text-sm font-semibold uppercase tracking-widest text-indigo-600 dark:text-indigo-400">
                            {{ __('Company Profile') }}
                        </p>

                        <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl">
                            {{ $contents['hero_title'] ?? 'Welcome to Our Company' }}
                        </h1>

                        <p class="mt-6 text-lg leading-relaxed text-gray-600 dark:text-gray-400">
                            {{ $contents['hero_subtitle'] ?? 'We build digital experiences that help your business grow.' }}
                        </p>

                        <div class="mt-8">
                            <a href="#services" class="inline-flex items-center rounded-md bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 dark:focus:ring-offset-gray-900">
                                {{ $contents['hero_button_text'] ?? 'Learn More' }}
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <section id="about" class="border-b border-gray-100 dark:border-gray-800">
                <div class="mx-auto grid max-w-7xl gap-10 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:px-8">
                    <h2 class="text-3xl font-bold tracking-tight">
                        {{ $contents['about_title'] ?? 'About Our Company' }}
                    </h2>

                    <p class="text-lg leading-relaxed text-gray-600 dark:text-gray-400">
                        {{ $contents['about_text'] ?? 'We are a team of professionals dedicated to helping your business succeed online.' }}
                    </p>
                </div>
            </section>

            <section id="services" class="border-b border-gray-100 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
                <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
                    <div class="max-w-2xl">
                        <h2 class="text-3xl font-bold tracking-tight">
                            {{ $contents['services_title'] ?? 'What We Do' }}
                        </h2>

                        <p class="mt-4 text-lg leading-relaxed text-gray-600 dark:text-gray-400">
                            {{ $contents['services_subtitle'] ?? 'A selection of the services we provide to our clients.' }}
                        </p>
                    </div>

                    <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse ($services as $service)
                            <article class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-100 dark:bg-gray-900 dark:ring-gray-800">
                                <h3 class="text-lg font-semibold">
                                    {{ $service['title'] }}
                                </h3>

                                <p class="mt-3 text-sm leading-relaxed text-gray-600 dark:text-gray-400">
                                    {{ $service['description'] }}
                                </p>
                            </article>
                        @empty
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ __('Our services will be published here soon.') }}
                            </p>
                        @endforelse
                    </div>
                </div>
            </section>

            <section id="contact" class="border-b border-gray-100 dark:border-gray-800">
                <div class="mx-auto max-w-4xl px-4 py-20 text-center sm:px-6 lg:px-8">
                    <h2 class="text-3xl font-bold tracking-tight">
                        {{ $contents['cta_title'] ?? "Let's Work Together" }}
                    </h2>

                    <p class="mx-auto mt-4 max-w-2xl text-lg leading-relaxed text-gray-600 dark:text-gray-400">
                        {{ $contents['cta_text'] ?? 'Get in touch with our team to discuss your next project.' }}
                    </p>

                    <div class="mt-8">
                        <a href="mailto:{{ config('mail.from.address') }}" class="inline-flex items-center rounded-md bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 dark:focus:ring-offset-gray-900">
                            {{ $contents['cta_button_text'] ?? 'Get in Touch' }}
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <footer>
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 py-8 text-sm text-gray-500 dark:text-gray-400 sm:flex-row sm:px-6 lg:px-8">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. {{ __('All rights reserved.') }}</p>

                <a href="{{ route('home') }}" class="transition hover:text-gray-900 dark:hover:text-white">
                    {{ __('Back to top') }}
                </a>
            </div>
        </footer>
    </body>
</html>
