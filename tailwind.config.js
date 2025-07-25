// tailwind.config.js
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
  ],
  safelist: [
    // Tipos de íconos
    'fas', 'far', 'fal', 'fab',
    'fa-list-alt',
    'fa-check-circle',
    'fa-shopping-cart',
    'fa-hand-paper',
    'fa-coins',
    'fa-filter',
    'fa-chart-line',
    'fa-dollar-sign',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#2563eb',     // Azul corporativo principal (Tailwind blue-600)
          light: '#3b82f6',      // Azul más claro (blue-500)
          dark: '#1e40af',       // Azul más oscuro (blue-800)
        },
        secondary: {
          DEFAULT: '#64748b',    // Gris azulado elegante (slate-500)
          light: '#94a3b8',      // Gris más claro (slate-400)
          dark: '#334155',       // Gris más oscuro (slate-800)
        },
        accent: {
          DEFAULT: '#fb923c',    // Naranja moderno para detalles/acento (orange-400)
          dark: '#ea580c',       // Naranja fuerte para hover o alertas
        },
      },
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  plugins: [forms],
};
