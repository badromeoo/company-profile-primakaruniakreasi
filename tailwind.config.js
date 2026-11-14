import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/livewire/livewire/src/Features/SupportPagination/views/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            /**
             * Design tokens tipografi (dipakai halaman publik).
             * Setiap token membawa line-height (dan letter-spacing untuk heading)
             * sehingga view cukup memakai satu class ukuran.
             */
            fontSize: {
                hero: ['clamp(2.25rem, 1.5rem + 2.5vw, 3rem)', { lineHeight: '1.1', letterSpacing: '-0.02em' }],
                h2: ['1.875rem', { lineHeight: '1.2', letterSpacing: '-0.02em' }],
                h3: ['1.125rem', { lineHeight: '1.4' }],
                lead: ['1.125rem', { lineHeight: '1.75' }],
                body: ['1rem', { lineHeight: '1.6' }],
                meta: ['0.875rem', { lineHeight: '1.6' }],
            },
        },
    },

    plugins: [forms],
};
