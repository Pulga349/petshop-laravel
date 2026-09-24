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
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                canvas: '#000000',
                surface: '#0d0d0d',
                'surface-raised': '#161616',
                'surface-muted': '#0d0d0d',
                'surface-hover': '#222222',
                line: '#262626',
                'line-focus': '#525252',
                accent: '#8d8d8d',
                primary: '#8d8d8d',
                success: '#10b981',
                warning: '#f59e0b',
                danger: '#ef4444',
            },
            borderRadius: {
                DEFAULT: '0',
                sm: '0',
                md: '0',
                lg: '0',
                xl: '0',
                '2xl': '0',
                '3xl': '0',
                full: '0',
            },
        },
    },

    plugins: [forms],
};
