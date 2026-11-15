@props(['post'])

@php
    $imageUrl = $post->featured_image ? asset('storage/'.$post->featured_image) : null;
    $imageAlt = $post->featured_image_alt ?: $post->title;
@endphp

<article {{ $attributes->merge(['class' => 'group flex flex-col overflow-hidden rounded-2xl bg-white/45 shadow-sm ring-1 ring-white/70 backdrop-blur-sm dark:bg-gray-900/35 dark:ring-white/10']) }}>
    <a href="{{ route('blog.show', $post) }}" class="block aspect-[16/9] overflow-hidden bg-gray-100 dark:bg-gray-800">
        @if ($imageUrl)
            <img
                src="{{ $imageUrl }}"
                alt="{{ $imageAlt }}"
                width="1200"
                height="630"
                loading="lazy"
                decoding="async"
                class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
            >
        @else
            <span class="flex h-full w-full items-center justify-center bg-gradient-to-br from-indigo-100 to-white px-4 text-center text-meta font-semibold uppercase tracking-widest text-indigo-500 dark:from-gray-800 dark:to-gray-900 dark:text-indigo-300">
                PRIMA Karunia Kreasi
            </span>
        @endif
    </a>

    <div class="flex flex-1 flex-col p-6">
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-meta text-gray-500 dark:text-gray-400">
            @if ($post->category)
                <a href="{{ route('blog.category', $post->category) }}" class="font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                    {{ $post->category->name }}
                </a>
                <span aria-hidden="true">&middot;</span>
            @endif
            <time datetime="{{ $post->published_at?->toAtomString() }}">{{ $post->published_at?->translatedFormat('d M Y') }}</time>
        </div>

        <h2 class="mt-3 text-h3 font-semibold">
            <a href="{{ route('blog.show', $post) }}" class="transition hover:text-indigo-600 dark:hover:text-indigo-400">
                {{ $post->title }}
            </a>
        </h2>

        <p class="mt-3 line-clamp-3 text-meta text-gray-600 dark:text-gray-300">
            {{ $post->excerpt }}
        </p>

        <p class="mt-auto pt-4 text-meta text-gray-500 dark:text-gray-400">
            {{ $post->reading_time }} {{ __('menit baca') }}
        </p>
    </div>
</article>
