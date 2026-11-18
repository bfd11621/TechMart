/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        irack: {
          navy: '#042562',
          'navy-dark': '#021840',
          'navy-light': '#0a3a91',
          blue: '#1865C0',
          'blue-light': '#2f7fda',
          'blue-dark': '#104b91',
          red: '#E20913',
          'red-hover': '#be0710',
          bg: '#F4F6FA',
          white: '#FFFFFF',
          dark: '#172033',
          secondary: '#64748B',
          border: '#E2E8F0',
        },
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', '-apple-system', 'Segoe UI', 'Roboto', 'sans-serif'],
      },
      boxShadow: {
        card: '0 1px 3px 0 rgba(0, 0, 0, 0.07), 0 1px 2px -1px rgba(0, 0, 0, 0.05)',
        'card-hover': '0 10px 15px -3px rgba(4, 37, 98, 0.08), 0 4px 6px -4px rgba(4, 37, 98, 0.05)',
      },
    },
  },
  plugins: [],
};
