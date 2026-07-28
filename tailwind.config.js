import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#eefcf6',
                    100: '#d5f7e7',
                    200: '#adecd1',
                    300: '#79dab8',
                    400: '#44bf99',
                    500: '#22a17e',
                    600: '#158066',
                    700: '#136654',
                    800: '#135144',
                    900: '#124339',
                    950: '#062820',
                },
                ink: {
                    50: '#eef1f6',
                    100: '#d7dee9',
                    200: '#b1bfd4',
                    300: '#8195b6',
                    400: '#576f96',
                    500: '#3c5279',
                    600: '#2c3e60',
                    700: '#22314e',
                    800: '#182440',
                    900: '#101a30',
                    950: '#080e1c',
                },
            },
        },
    },

    plugins: [forms],
};
