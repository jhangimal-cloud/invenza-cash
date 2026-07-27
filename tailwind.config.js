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
                    50: '#f4f2ff',
                    100: '#eae5ff',
                    200: '#d7cdff',
                    300: '#b8a4ff',
                    400: '#9670ff',
                    500: '#7c4dff',
                    600: '#6c2fef',
                    700: '#5b22cc',
                    800: '#4a1ca6',
                    900: '#3d1c85',
                    950: '#250f57',
                },
            },
        },
    },

    plugins: [forms],
};
