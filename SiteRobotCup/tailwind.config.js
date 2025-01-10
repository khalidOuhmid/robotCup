/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {},
    screens: {
      sm: '640px',
      md: '820px',
      lg: '1024px',
      xl: '1280px',
    },
  },
  plugins: [],
}
