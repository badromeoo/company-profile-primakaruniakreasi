<x-public-layout :seo="seo()->for($seoData)">

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

                        <a href="{{ route('blog.index') }}" class="mt-6 inline-flex items-center rounded-md bg-gray-900 px-5 py-3 text-meta font-semibold text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 dark:focus:ring-offset-gray-900">
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
</x-public-layout>
