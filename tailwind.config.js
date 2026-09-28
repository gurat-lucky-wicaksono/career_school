/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './app/**/*.php',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        heading: ['"Sora"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      fontSize: {
        display: ['36px', { lineHeight: '44px', fontWeight: '700' }],
        h1: ['28px', { lineHeight: '36px', fontWeight: '700' }],
        h2: ['22px', { lineHeight: '30px', fontWeight: '600' }],
        h3: ['18px', { lineHeight: '26px', fontWeight: '600' }],
        'body-lg': ['16px', { lineHeight: '24px', fontWeight: '400' }],
        body: ['14px', { lineHeight: '20px', fontWeight: '400' }],
        caption: ['12px', { lineHeight: '16px', fontWeight: '500' }],
        btn: ['14px', { lineHeight: '20px', fontWeight: '600' }],
      },
      borderRadius: {
        card: '16px',
        input: '10px',
        btn: '10px',
        modal: '20px',
      },
      colors: {
        // Primitives from docs/design.md
        neutral: {
          50: '#F7F8F3',
          100: '#EDEEE8',
          300: '#C4C5BE',
          400: '#A0A0A0',
          600: '#6B6B6B',
          700: '#4A4A4A',
          800: '#333333',
          900: '#1E1E1E',
          950: '#121212',
        },
        lime: {
          100: '#F0FBD4',
          500: '#C2F970',
          700: '#7DA82E',
          900: '#5A7A2B',
        },
        forest: {
          100: '#D1EAE0',
          300: '#3FA07D',
          500: '#166E4F',
          900: '#083D2D',
        },
        success: {
          100: '#DCFCE7',
          700: '#15803D',
          900: '#14532D',
        },
        danger: {
          100: '#FEE2E2',
          700: '#B91C1C',
          900: '#7F1D1D',
        },
        warning: {
          100: '#FEF3C7',
          700: '#B45309',
          900: '#78350F',
        },
        info: {
          100: '#DBEAFE',
          700: '#1D4ED8',
          900: '#1E3A8A',
        },

        // Semantic tokens (dynamic light/dark)
        surface: {
          bg: 'var(--color-surface-bg)',
          card: 'var(--color-surface-card)',
          elevated: 'var(--color-surface-elevated)',
        },
        text: {
          primary: 'var(--color-text-primary)',
          secondary: 'var(--color-text-secondary)',
          brand: 'var(--color-text-brand)',
          'on-brand-primary': 'var(--color-text-on-brand-primary)',
          'on-brand-secondary': 'var(--color-text-on-brand-secondary)',
        },
        border: {
          default: 'var(--color-border-default)',
          subtle: 'var(--color-border-subtle)',
        },
        brand: {
          primary: 'var(--color-brand-primary)',
          secondary: 'var(--color-brand-secondary)',
        },
        state: {
          'success-bg': 'var(--color-state-success-bg)',
          'success-text': 'var(--color-state-success-text)',
          'danger-bg': 'var(--color-state-danger-bg)',
          'danger-text': 'var(--color-state-danger-text)',
          'warning-bg': 'var(--color-state-warning-bg)',
          'warning-text': 'var(--color-state-warning-text)',
          'info-bg': 'var(--color-state-info-bg)',
          'info-text': 'var(--color-state-info-text)',
        },
      },
    },
  },
  plugins: [],
};
