import defaultTheme from 'tailwindcss/defaultTheme';

export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'construct-red': 'var(--accent, #CF202E)',
                'construct-black': '#000000',
                'construct-paper': '#F5F5F5',
                accent: 'var(--accent, #CF202E)',
            },
            backgroundColor: {
                accent: 'var(--accent, #CF202E)',
            },
            borderColor: {
                accent: 'var(--accent, #CF202E)',
            },
            textColor: {
                accent: 'var(--accent, #CF202E)',
            },
            animation: {
                'spin-slow': 'spin 20s linear infinite',
                'float': 'float 4s ease-in-out infinite',
            },
            keyframes: {
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-20px)' },
                },
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
