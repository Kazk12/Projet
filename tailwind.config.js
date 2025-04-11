
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  darkMode: 'class', // Active le dark mode basé sur une classe CSS
  theme: {
    extend: {
      colors: {
        indigo: {
          50: '#f0f5ff',
          100: '#e0eaff',
          400: '#8da2fb',
          500: '#6875f5',
          600: '#4f46e5', // Couleur principale
          700: '#4338ca', // Hover
          800: '#3730a3',
        },
        gray: {
          50: '#f9fafb',
          100: '#f3f4f6',
          200: '#e5e7eb',
          300: '#d1d5db',
          400: '#9ca3af',
          500: '#6b7280',
          600: '#4b5563',
          700: '#374151',
          800: '#1f2937',
          900: '#111827',
        },
        red: {
          50: '#fef2f2',
          100: '#fee2e2',
          600: '#dc2626',
          800: '#991b1b',
        },
        white: '#ffffff',
      },
      fontFamily: {
        sans: ['ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        'sm': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
        'md': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
        'lg': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
        'dark-sm': '0 1px 2px 0 rgba(0, 0, 0, 0.4)',
        'dark-md': '0 4px 6px -1px rgba(0, 0, 0, 0.5), 0 2px 4px -1px rgba(0, 0, 0, 0.4)',
        'dark-lg': '0 10px 15px -3px rgba(0, 0, 0, 0.6), 0 4px 6px -2px rgba(0, 0, 0, 0.5)',
      },
      borderRadius: {
        'md': '0.375rem',
        'lg': '0.5rem',
        'xl': '0.75rem',
        'full': '9999px',
      },
      typography: {
        DEFAULT: {
          css: {
            maxWidth: '65ch',
            color: '#374151',
            lineHeight: '1.5',
          },
        },
        dark: {
          css: {
            color: '#d1d5db',
            a: {
              color: '#8da2fb',
              '&:hover': {
                color: '#6875f5',
              },
            },
            h1: {
              color: '#e0eaff',
            },
            h2: {
              color: '#e0eaff',
            },
            h3: {
              color: '#e0eaff',
            },
            h4: {
              color: '#e0eaff',
            },
            strong: {
              color: '#d1d5db',
            },
            code: {
              color: '#f3f4f6',
            },
          },
        },
      },
    },
  },
  plugins: [],
}