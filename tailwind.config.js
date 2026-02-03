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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                heading: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#fff5f5',
                    100: '#ffe3e3',
                    200: '#ffc9c9',
                    300: '#ffa1a1',
                    400: '#ff6b6b',
                    500: '#eb4242',
                    600: '#cb2d2d',
                    700: '#a82020',
                    800: '#8a1c1c',
                    900: '#731b1b',
                    950: '#3f0a0a',
                },
                secondary: {
                    50: '#f4f7f9',
                    100: '#e7ecf2',
                    200: '#cfdceb',
                    300: '#abbddb',
                    400: '#819cc4',
                    500: '#617fa8',
                    600: '#4a6488',
                    700: '#3d516d',
                    800: '#274256',
                    900: '#263a4d',
                    950: '#1a2633',
                },
            },
        },
    },

    plugins: [forms],
};
