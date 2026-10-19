export default defineNuxtConfig({
  compatibilityDate: '2026-10-01',
  devtools: { enabled: false },
  css: ['~/assets/css/main.css'],
  runtimeConfig: {
    public: {
      // Laravel API (php artisan serve). Sovrascrivibile con NUXT_PUBLIC_API_BASE
      apiBase: 'http://localhost:8000/api',
      instagram: 'https://www.instagram.com/nds_personaltrainer',
      whatsapp: '',
    },
  },
  app: {
    head: {
      htmlAttrs: { lang: 'it' },
      title: 'Nicole Dal Santo · Personal Trainer a Pescantina',
      meta: [
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'description', content: 'Programmi di allenamento personalizzati, pacchetti glutei, total body e online coaching. Studio privato a Pescantina (VR).' },
        { name: 'theme-color', content: '#e6007e' },
      ],
      link: [
        { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
        { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
        { rel: 'stylesheet', href: 'https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,700&display=swap' },
      ],
    },
  },
})
