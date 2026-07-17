/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    safelist: [
        // Class dinamis yang tidak terdeteksi static scan Tailwind
        'nav-active', 'nav-item', 'nav-dot', 'nav-group-label',
        'badge', 'badge-green', 'badge-yellow', 'badge-red',
        'badge-blue', 'badge-gray', 'badge-purple',
        'btn-icon', 'btn-xs', 'btn-primary-xs', 'btn-ghost-xs', 'btn-danger-xs',
        'stat-card', 'data-table', 'tbl-row', 'tbl-head',
        'flash-enter', 'fade-in', 'topbar-divider', 'filter-input',
        'hex-pattern', 'hero-overlay', 'float-anim', 'pulse-ring',
        'stat-item', 'doc-card', 'navbar-scrolled', 'nav-active',
        { pattern: /^(bg|text|border)-(primary|gold)-(50|100|200|300|400|500|600|700|800|900|950)$/ },
        { pattern: /^hover:(bg|text|border)-(primary|gold)-(50|100|200|300|400|500|600|700|800|900|950)$/ },
        { pattern: /^badge-(green|yellow|red|blue|gray|purple)$/ },
        { pattern: /^btn-icon(\.(danger|success|warning|blue))?$/ },
        { pattern: /^stat-card(\.(amber|red|blue))?$/ },
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Plus Jakarta Sans', 'sans-serif'],
            },
            colors: {
                primary: {
                    50:  '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#22c55e',
                    600: '#16a34a',
                    700: '#15803d',
                    800: '#166534',
                    900: '#14532d',
                    950: '#052e16',
                },
                gold: {
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                },
                hairline: '#E9E5DA',
            },
            fontSize: {
                '2xs': ['0.65rem', { lineHeight: '1rem' }],
            },
        },
    },
    plugins: [],
};
