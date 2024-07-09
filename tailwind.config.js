/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.tsx",
    "./assets/**/*.js",
    "./assets/**/*.jsx",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('daisyui'),
  ],
}

