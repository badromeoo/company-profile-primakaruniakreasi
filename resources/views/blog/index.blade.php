<x-public-layout :title="$title" :description="$description" :seo="seo()->for($seoData)">
    <main class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 py-10 sm:px-6 lg:gap-8 lg:px-8 lg:py-14">
        <section class="rounded-3xl bg-white/30 p-8 shadow-sm ring-1 ring-white/60 backdrop-blur-sm sm:p-10 dark:bg-gray-900/30 dark:ring-white/10">
            <div class="max-w-3xl">
                <p class="text-meta font-semibold uppercase tracking-widest text-indigo-600 dark:text-indigo-400">
                    {{ __('Blog') }}
                </p>

                <h1 class="mt-3 text-hero font-bold">
                    {{ $title }}
                </h1>

                <p class="mt-4 text-lead text-gray-600 dark:text-gray-300">
                    {{ $description }}
                </p>
            </div>

            @if ($categories->isNotEmpty())
                <div class="mt-8 flex flex-wrap gap-2">
                    @foreach ($categories as $category)
                        <a
                            href="{{ route('blog.category', $category) }}"
                            class="inline-flex items-center gap-2 rounded-full bg-white/60 px-4 py-2 text-meta font-medium text-gray-700 ring-1 ring-white/70 transition hover:bg-white/80 hover:text-gray-900 dark:bg-gray-900/40 dark:text-gray-300 dark:ring-white/10 dark:hover:bg-gray-900/60 dark:hover:text-white"
                        >
                            {{ $category->name }}
                            <span class="text-meta text-gray-500 dark:text-gray-400">{{ $category->posts_count }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="rounded-3xl bg-white/25 p-6 shadow-sm ring-1 ring-white/60 backdrop-blur-sm sm:p-8 dark:bg-gray-900/25 dark:ring-white/10">
            @if ($posts->isEmpty())
                <div class="py-12 text-center">
                    <h2 class="text-h2 font-bold">{{ __('Belum ada artikel') }}</h2>
                    <p class="mt-3 text-lead text-gray-600 dark:text-gray-300">
                        {{ __('Artikel baru akan tampil di sini setelah dipublikasikan.') }}
                    </p>
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $post)
                        <x-post-card :post="$post" />
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
            @endif
        </section>
    </main>
</x-public-layout>
