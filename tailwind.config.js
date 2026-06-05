import defaultTheme from 'tailwindcss/defaultTheme';

export default {
    darkMode: 'class',
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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Space Grotesk', ...defaultTheme.fontFamily.sans],
            },
            fontWeight: {
                normal: '400',
                medium: '500',
                semibold: '600',
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
                'fade-in-up': 'fadeInUp 0.5s ease-out forwards',
                'blob': 'blob 7s infinite',
                'blob-reverse': 'blob 10s infinite reverse',
            },
            keyframes: {
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-20px)' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(16px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                blob: {
                    '0%': { transform: 'translate(0px, 0px) scale(1)' },
                    '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                    '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                    '100%': { transform: 'translate(0px, 0px) scale(1)' },
                },
            },
            zIndex: {
                '100': '100',
                '110': '110',
                '120': '120',
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
