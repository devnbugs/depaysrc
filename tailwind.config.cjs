module.exports = {
    darkMode: 'class',
    content: [
        './app/**/*.php',
        './bootstrap/**/*.php',
        './config/**/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/**/*.php',
        './vendor/livewire/flux/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                // dePay Brand Colors
                'brand': {
                    50: '#faf7f3',
                    100: '#f5ede2',
                    200: '#f0dec0',
                    300: '#e8c89b',
                    400: '#dea758',
                    500: '#c4833f',
                    600: '#a1643e',
                    700: '#7d4c38',
                    800: '#6b4423',
                    900: '#5c3d2e',
                    950: '#3a2519',
                },
            },
        },
    },
    plugins: [],
};
