import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            // --- ADICIONE ESTE BLOCO DE CORES ---
            colors: {
                'primary': {
                    light: '#36a9e9', // Tom mais claro para hover
                    DEFAULT: '#0494E3', // A cor principal do seu logo
                    dark: '#037ab8',  // Tom mais escuro para active/focus
                },
                'dark': {
                    DEFAULT: '#3a3a3a', // Cinza escuro para texto principal
                    light: '#6b7280', // Cinza mais claro (equivale a gray-500)
                }
            },
            // --- FIM DO BLOCO DE CORES ---
        },
    },

    plugins: [forms],
};
