/** @type {import('tailwindcss').Config} */
module.exports = {
  prefix: 'tw-',
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.jsx',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      typography: (theme) => ({
        DEFAULT: {
          css: {
            '.alphabet_counter u::before': {
              counterIncrement: 'uwu',
              content: '"(" counter(uwu, upper-alpha) ")"',
              display: 'inline-block',
              position: 'relative',
              paddingBottom: '15px',
              textDecoration: 'none',
            },
          },
        },
      }),
      fontFamily: {
        sans: ['Poppins', 'sans-serif']
      },
      colors: {
        primary1: '#64398B',
        primary2: '#2DA34D',
        primary3: '#2B7FD4',
        secondary1: '#CD424B',
        secondary2: '#F9B233',
        secondary3: 'hsla(356, 58%, 53%, 0.3)',
        secondary4: 'hsla(38, 94%, 59%, 0.3)',
        secondary5: 'hsla(136, 57%, 41%, 0.3)',
        secondary6: 'hsla(271, 42%, 38%, 0.3)',
        secondary7: 'hsla(210, 66%, 50%, 0.3)',
        secondary7_2: 'hsla(210, 66%, 50%, 0.1)',
        neutral1: '#212529',
        neutral2: '#F8F9FA',
        neutral3: '#ACACAD',
        neutral4: '#979797',
        neutral5: '#E8EDF2',
        neutral6: '#031139'
      },
      width: {
        appbar: '240px'
      }
    },
  },
  safelist: [
    'tw-text-primary1',
    'tw-text-primary2',
    'tw-text-primary3',
    'tw-bg-primary1',
    'tw-bg-primary2',
    'tw-bg-primary3',
    'tw-bg-secondary5',
    'tw-bg-secondary6',
    'tw-bg-secondary7',
    'tw-border-primary1',
    'tw-border-primary2',
    'tw-border-primary3',
    'tw-border-secondary5',
    'tw-border-secondary6',
    'tw-border-secondary7',
    'tw-from-primary1',
    'tw-from-primary2',
    'tw-from-primary3',
    'tw-to-secondary5',
    'tw-to-secondary6',
    'tw-to-secondary7',
    'tw-bg-red-500',
    'tw-bg-green-500',
  ],
  plugins: [],
};