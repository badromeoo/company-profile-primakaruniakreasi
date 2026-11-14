<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (auth()->user()?->is_admin)
                <div class="grid gap-4 sm:grid-cols-2">
                    <a href="{{ filament()->getPanel('cms')->getUrl() }}"
                       class="block bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 transition hover:shadow-md">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('CMS') }}</h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Kelola post, kategori, dan konten blog.') }}
                        </p>
                        <span class="mt-4 inline-block text-sm font-medium text-indigo-600 dark:text-indigo-400">
                            {{ __('Buka CMS →') }}
                        </span>
                    </a>

                    <a href="{{ route('admin.home-content.edit') }}" wire:navigate
                       class="block bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 transition hover:shadow-md">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Home Content') }}</h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Ubah teks hero, about, services, dan CTA di beranda.') }}
                        </p>
                        <span class="mt-4 inline-block text-sm font-medium text-indigo-600 dark:text-indigo-400">
                            {{ __('Kelola konten →') }}
                        </span>
                    </a>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        {{ __('Selamat datang di dashboard.') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
