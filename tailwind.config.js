/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#4f46e5',
        secondary: '#7c3aed',
        accent: '#059669',
        destructive: '#dc2626',
        'neon-pink': '#db2777',
        'neon-blue': '#0ea5e9',
        'neon-violet': '#4f46e5',
        'neon-green': '#059669',
        'status-active': '#059669',
        'status-warning': '#f59e0b',
        'status-danger': '#dc2626',
      }
    },
  },
  plugins: [],
}
