/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'Arial', 'sans-serif'],
      },
      colors: {
        primary: '#97050C',
        secondary: '#f1c40f',
        danger: '#e74c3c',
      },
    },
  },
  darkMode: "class",
  plugins: [],
}