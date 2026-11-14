<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Home Page Content') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.home-content.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                @if (session('status') === 'home-content-updated')
                    <div class="rounded-md bg-green-50 p-4 text-sm font-medium text-green-700 dark:bg-green-900/40 dark:text-green-300">
                        {{ __('Home page content saved successfully.') }}
                    </div>
                @endif

                <x-input-error :messages="$errors->get('contents')" />

                @foreach ($contents as $group => $sections)
                    <section class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                        <div class="p-6">
                            <header class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ __(Str::headline($group)) }} {{ __('Section') }}
                                </h3>

                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('Manage the text shown in this section of the public home page.') }}
                                </p>
                            </header>

                            <div class="space-y-6">
                                @foreach ($sections as $section)
                                    @php
                                        $field = 'contents.'.$section->section_key;
                                        $inputName = 'contents['.$section->section_key.']';
                                        $isLongText = Str::contains($section->section_key, ['text', 'subtitle', 'description']);
                                    @endphp

                                    <div>
                                        <x-input-label :for="$section->section_key" :value="Str::headline($section->section_key)" />

                                        @if ($isLongText)
                                            <textarea id="{{ $section->section_key }}" name="{{ $inputName }}" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">{{ old($field, $section->content_value) }}</textarea>
                                        @else
                                            <x-text-input id="{{ $section->section_key }}" :name="$inputName" type="text" class="mt-1 block w-full" :value="old($field, $section->content_value)" />
                                        @endif

                                        <x-input-error class="mt-2" :messages="$errors->get($field)" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endforeach

                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
