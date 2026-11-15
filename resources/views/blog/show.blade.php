<x-public-layout
    :title="$post->meta_title ?: $post->title"
    :description="$post->meta_description ?: $post->excerpt"
    :noindex="$isPreview || $post->noindex"
>
    <main class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 py-10 sm:px-6 lg:gap-8 lg:px-8 lg:py-14">
        <nav aria-label="Breadcrumb" class="text-meta text-gray-600 dark:text-gray-300">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="{{ route('home') }}" class="transition hover:text-gray-900 dark:hover:text-white">{{ __('Home') }}</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('blog.index') }}" class="transition hover:text-gray-900 dark:hover:text-white">{{ __('Blog') }}</a></li>
                @if ($post->category)
                    <li aria-hidden="true">/</li>
                    <li>
                        <a href="{{ route('blog.category', $post->category) }}" class="transition hover:text-gray-900 dark:hover:text-white">
                            {{ $post->category->name }}
                        </a>
                    </li>
                @endif
            </ol>
        </nav>

        @if ($isPreview)
            <div class="rounded-2xl bg-amber-50/90 px-5 py-4 text-meta text-amber-900 ring-1 ring-amber-200 dark:bg-amber-900/30 dark:text-amber-100 dark:ring-amber-800" role="status">
                {{ __('Mode preview — status') }}: <strong>{{ $post->status->value }}</strong>.
                {{ __('Halaman ini belum tentu tersedia untuk publik.') }}
            </div>
        @endif

        <article class="rounded-3xl bg-white/30 p-8 shadow-sm ring-1 ring-white/60 backdrop-blur-sm sm:p-10 dark:bg-gray-900/30 dark:ring-white/10">
            <header class="max-w-3xl">
                @if ($post->category)
                    <a href="{{ route('blog.category', $post->category) }}" class="text-meta font-semibold uppercase tracking-widest text-indigo-600 dark:text-indigo-400">
                        {{ $post->category->name }}
                    </a>
                @endif

                <h1 class="mt-3 text-hero font-bold">
                    {{ $post->title }}
                </h1>

                <p class="mt-4 text-lead text-gray-600 dark:text-gray-300">
                    {{ $post->excerpt }}
                </p>

                <div class="mt-6 flex flex-wrap items-center gap-x-4 gap-y-2 text-meta text-gray-500 dark:text-gray-400">
                    @if ($post->author)
                        <span>{{ __('Oleh') }} {{ $post->author->name }}</span>
                        <span aria-hidden="true">&middot;</span>
                    @endif
                    <time datetime="{{ $post->published_at?->toAtomString() }}">
                        {{ $post->published_at?->translatedFormat('d F Y') }}
                    </time>
                    <span aria-hidden="true">&middot;</span>
                    <span>{{ $post->reading_time }} {{ __('menit baca') }}</span>
                </div>
            </header>

            @if ($post->featured_image)
                <figure class="mt-8 overflow-hidden rounded-2xl ring-1 ring-white/60 dark:ring-white/10">
                    <img
                        src="{{ asset('storage/'.$post->featured_image) }}"
                        alt="{{ $post->featured_image_alt ?: $post->title }}"
                        width="1200"
                        height="630"
                        loading="lazy"
                        decoding="async"
                        class="w-full object-cover"
                    >
                </figure>
            @endif

            <div class="mt-8 max-w-none text-body text-gray-700 dark:text-gray-300 [&_a]:font-semibold [&_a]:text-indigo-600 [&_a]:underline [&_a]:underline-offset-2 dark:[&_a]:text-indigo-400 [&_blockquote]:mt-6 [&_blockquote]:border-l-4 [&_blockquote]:border-indigo-200 [&_blockquote]:pl-4 [&_blockquote]:italic [&_h2]:mt-10 [&_h2]:text-h2 [&_h2]:font-bold [&_h2]:text-gray-900 dark:[&_h2]:text-white [&_h3]:mt-8 [&_h3]:text-h3 [&_h3]:font-semibold [&_h3]:text-gray-900 dark:[&_h3]:text-white [&_img]:mt-6 [&_img]:rounded-2xl [&_li]:mt-2 [&_ol]:mt-4 [&_ol]:list-decimal [&_ol]:pl-6 [&_p]:mt-4 [&_strong]:font-semibold [&_ul]:mt-4 [&_ul]:list-disc [&_ul]:pl-6">
                {!! $post->content !!}
            </div>
        </article>

        @if ($related->isNotEmpty())
            <section class="rounded-3xl bg-white/30 p-8 shadow-sm ring-1 ring-white/60 backdrop-blur-sm sm:p-10 dark:bg-gray-900/30 dark:ring-white/10">
                <h2 class="text-h2 font-bold">{{ __('Artikel terkait') }}</h2>

                <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $relatedPost)
                        <x-post-card :post="$relatedPost" />
                    @endforeach
                </div>
            </section>
        @endif

        <section class="rounded-3xl bg-white/35 p-10 text-center shadow-sm ring-1 ring-white/60 backdrop-blur-sm sm:p-14 dark:bg-gray-900/30 dark:ring-white/10">
            <h2 class="text-h2 font-bold">{{ __('Punya proyek yang ingin dikembangkan?') }}</h2>

            <p class="mx-auto mt-4 max-w-2xl text-lead text-gray-600 dark:text-gray-300">
                {{ __('Diskusikan kebutuhan digital Anda bersama tim kami.') }}
            </p>

            <div class="mt-8">
                <a href="mailto:{{ config('mail.from.address') }}" class="inline-flex items-center rounded-md bg-gray-900 px-5 py-3 text-meta font-semibold text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 dark:focus:ring-offset-gray-900">
                    {{ __('Hubungi Kami') }}
                </a>
            </div>
        </section>
    </main>
</x-public-layout>

