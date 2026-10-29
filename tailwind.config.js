/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './index.php',
    './app/**/*.php',
    './views/**/*.php',
    './assets/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        ink: '#0A0A0B',
        charcoal: '#0F0F11',
        panel: '#141416',
        ivory: '#F3EEE4',
        muted: '#8E887D',
        gold: '#C6A664',
        line: '#262420',
      },
      fontFamily: {
        display: ['"Cormorant Garamond"', 'Georgia', 'Times New Roman', 'serif'],
        sans: ['Inter', 'system-ui', '-apple-system', 'Segoe UI', 'sans-serif'],
      },
      letterSpacing: {
        widest2: '0.28em',
      },
      boxShadow: {
        glow: '0 20px 70px -25px rgba(198, 166, 100, 0.45)',
        panel: '0 30px 80px -40px rgba(0, 0, 0, 0.9)',
      },
      keyframes: {
        'fade-up': {
          '0%': { opacity: '0', transform: 'translateY(14px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        'fade-in': {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        'scale-in': {
          '0%': { opacity: '0', transform: 'scale(.97) translateY(8px)' },
          '100%': { opacity: '1', transform: 'scale(1) translateY(0)' },
        },
        'grow-x': {
          '0%': { transform: 'scaleX(0)' },
          '100%': { transform: 'scaleX(1)' },
        },
      },
      animation: {
        'fade-up': 'fade-up .7s cubic-bezier(.22,1,.36,1) both',
        'fade-in': 'fade-in .5s ease both',
        'scale-in': 'scale-in .35s cubic-bezier(.22,1,.36,1) both',
        'grow-x': 'grow-x .9s cubic-bezier(.22,1,.36,1) both',
      },
      transitionTimingFunction: {
        lux: 'cubic-bezier(.22,1,.36,1)',
      },
    },
  },
  plugins: [],
};
