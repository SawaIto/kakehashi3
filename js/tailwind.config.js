const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ['./**/*.blade.php'],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [],

    module.exports = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['"Noto Sans JP"', 'sans-serif'],
            },
          },
        },
};
