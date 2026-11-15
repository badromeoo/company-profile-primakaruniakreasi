<x-public-layout :title="$category->meta_title ?: $category->name" :description="$category->meta_description ?: $category->description">
    <main class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 py-10 sm:px-6 lg:gap-8 lg:px-8 lg:py-14">
        <nav aria-label="Breadcrumb" class="text-meta text-gray-600 dark:text-gray-300">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="{{ route('home') }}" class="transition hover:text-gray-900 dark:hover:text-white">{{ __('Home') }}</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('blog.index') }}" class="transition hover:text-gray-900 dark:hover:text-white">{{ __('Blog') }}</a></li>
            </ol>
        </nav>

        <section class="rounded-3xl bg-white/30 p-8 shadow-sm ring-1 ring-white/60 backdrop-blur-sm sm:p-10 dark:bg-gray-900/30 dark:ring-white/10">
            <div class="max-w-3xl">
                <p class="text-meta font-semibold uppercase tracking-widest text-indigo-600 dark:text-indigo-400">
                    {{ __('Kategori') }}
                </p>

                <h1 class="mt-3 text-hero font-bold">
                    {{ $category->name }}
                </h1>

                @if ($category->description)
                    <p class="mt-4 text-lead text-gray-600 dark:text-gray-300">
                        {{ $category->description }}
                    </p>
                @endif

                <p class="mt-4 text-meta text-gray-500 dark:text-gray-400">
                    {{ $posts->total() }} {{ __('artikel') }}
                </p>
            </div>
        </section>

        <section class="rounded-3xl bg-white/25 p-6 shadow-sm ring-1 ring-white/60 backdrop-blur-sm sm:p-8 dark:bg-gray-900/25 dark:ring-white/10">
            @if ($posts->isEmpty())
                <div class="py-12 text-center">
                    <h2 class="text-h2 font-bold">{{ __('Belum ada artikel di kategori ini') }}</h2>
                    <p class="mt-3 text-lead text-gray-600 dark:text-gray-300">
                        {{ __('Silakan kembali ke daftar blog untuk membaca artikel lainnya.') }}
                    </p>
                    <a href="{{ route('blog.index') }}" class="mt-6 inline-flex items-center rounded-md bg-gray-900 px-5 py-3 text-meta font-semibold text-white transition hover:bg-gray-700 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200">
                        {{ __('Lihat semua artikel') }}
                    </a>
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
