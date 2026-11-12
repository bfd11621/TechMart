/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
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
          success: '#16A34A',
          warning: '#F59E0B',
          error: '#DC2626',
        },
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
        mono: ['JetBrains Mono', 'Fira Code', 'monospace'],
      },
      boxShadow: {
        'card': '0 1px 3px 0 rgba(0, 0, 0, 0.07), 0 1px 2px -1px rgba(0, 0, 0, 0.05)',
        'card-hover': '0 10px 15px -3px rgba(4, 37, 98, 0.08), 0 4px 6px -4px rgba(4, 37, 98, 0.05)',
        'elevated': '0 20px 25px -5px rgba(4, 37, 98, 0.1), 0 8px 10px -6px rgba(4, 37, 98, 0.05)',
      },
    },
  },
  plugins: [],
}
