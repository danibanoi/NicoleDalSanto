<script setup lang="ts">
import type { Package, Session } from '~/types'

const api = useApi()
const { data: packages } = await useAsyncData('packages', () => api<Package[]>('/packages'))
const { data: sessions, refresh } = await useAsyncData('home-sessions', () => api<Session[]>('/sessions'))

const programs = computed(() => (packages.value ?? []).filter((p) => p.category !== 'personal').slice(0, 3))
const nextSessions = computed(() => (sessions.value ?? []).slice(0, 5))

const steps = [
  { n: '01', title: 'Analisi', text: 'Partiamo da obiettivi, abitudini, eventuali problematiche e stile di vita.' },
  { n: '02', title: 'Programmazione', text: 'Costruisco un allenamento su misura per te. Niente schede standard.' },
  { n: '03', title: 'Evoluzione', text: 'Monitoriamo i progressi e adattiamo il percorso nel tempo.' },
]
const services = [
  { icon: '🍑', title: 'Glutei', text: 'Forza e forma per la catena posteriore.' },
  { icon: '🔥', title: 'Dimagrimento', text: 'Meno massa grassa, più salute.' },
  { icon: '💪', title: 'Tonificazione', text: 'Forza, definizione e benessere.' },
  { icon: '🤰', title: 'Gravidanza', text: 'Allenarsi in sicurezza, prima e dopo.' },
  { icon: '🧘‍♀️', title: 'Postura', text: 'Mobilità, equilibrio, movimento quotidiano.' },
  { icon: '📱', title: 'Online', text: 'Allenati ovunque con il mio supporto.' },
]
</script>

<template>
  <div>
    <!-- HERO -->
    <section class="hero">
      <div class="container hero-grid">
        <div class="hero-text">
          <span class="eyebrow">Personal trainer · Pescantina (VR)</span>
          <h1>Allenati.<br><span class="hl">Cambia.</span><br><span class="hl-under">Sentiti bene.</span></h1>
          <p class="lead">Il percorso per ritrovare forza, forma e sicurezza nel tuo corpo. Programmi costruiti sulla persona, non il contrario.</p>
          <div class="cta">
            <NuxtLink to="/pacchetti" class="btn btn-pink">Scopri i pacchetti</NuxtLink>
            <NuxtLink to="/calendario" class="btn btn-ghost">Vedi le date</NuxtLink>
          </div>
        </div>

        <div class="hero-photo">
          <div class="blob" />
          <img src="/img/nicole.jpg" alt="Nicole Dal Santo, personal trainer, a braccia conserte su sfondo rosa" width="1254" height="1254">
          <div class="sticker s1"><strong>10+</strong> anni di<br>esperienza</div>
          <div class="sticker s2"><strong>1000+</strong> persone<br>allenate</div>
          <div class="sticker s3">Studio privato ✦</div>
        </div>
      </div>
    </section>

    <MarqueeStrip :items="['Glutei', 'Total body', 'Tonificazione', 'Mamma fit', 'Online coaching', 'Postura', 'Personal 1:1']" />

    <!-- METODO -->
    <section class="section">
      <div class="container">
        <span class="eyebrow">Il metodo</span>
        <h2>Progressi che <span class="hl">durano</span> nel tempo.</h2>
        <div class="steps">
          <article v-for="s in steps" :key="s.n" class="step">
            <span class="num">{{ s.n }}</span>
            <h3>{{ s.title }}</h3>
            <p class="muted">{{ s.text }}</p>
          </article>
        </div>
      </div>
    </section>

    <!-- PACCHETTI -->
    <section class="section pkgs">
      <div class="container">
        <div class="head">
          <div>
            <span class="eyebrow">Pacchetti di allenamento</span>
            <h2>Scegli il tuo<br>percorso.</h2>
          </div>
          <NuxtLink to="/pacchetti" class="btn">Tutti i pacchetti →</NuxtLink>
        </div>
        <div class="grid-3">
          <PackageCard v-for="p in programs" :key="p.id" :pkg="p" />
        </div>
      </div>
    </section>

    <!-- SERVIZI -->
    <section class="section">
      <div class="container">
        <span class="eyebrow">Su cosa lavoriamo</span>
        <h2>Un obiettivo, <span class="hl">il tuo.</span></h2>
        <div class="services">
          <div v-for="s in services" :key="s.title" class="service">
            <span class="ico">{{ s.icon }}</span>
            <div><strong>{{ s.title }}</strong><p class="muted">{{ s.text }}</p></div>
          </div>
        </div>
      </div>
    </section>

    <!-- CALENDARIO PREVIEW -->
    <section class="section cal">
      <div class="container cal-grid">
        <div>
          <span class="eyebrow">Prossime date</span>
          <h2>Trova il tuo<br>orario.</h2>
          <p class="lead">Small group da massimo 6 persone in studio e live online. Una volta iscritta e con il pacchetto attivo, prenoti le date direttamente da qui.</p>
          <NuxtLink to="/calendario" class="btn btn-pink">Calendario completo</NuxtLink>
        </div>
        <SessionList :sessions="nextSessions" @changed="refresh" />
      </div>
    </section>

    <!-- CHI SONO -->
    <section class="section">
      <div class="container about">
        <img src="/img/nicole.jpg" alt="" class="about-img" loading="lazy">
        <div>
          <span class="eyebrow">Ciao, sono Nicole</span>
          <h2>Persona prima della prestazione.</h2>
          <p class="lead">Sono una personal trainer certificata e fondatrice di NDS Personal Trainer. Credo nella professionalità, nell'ascolto e in un ambiente che ti faccia sentire a tuo agio.</p>
          <p class="lead">Non cerco la perfezione: <strong>cerco progressi che possano durare nel tempo!</strong></p>
          <NuxtLink to="/chi-sono" class="btn">La mia storia</NuxtLink>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="container">
      <div class="final">
        <h2>Pronta a iniziare?</h2>
        <p>Prenota una consulenza gratuita: parliamo dei tuoi obiettivi e scegliamo insieme il percorso giusto.</p>
        <div class="cta">
          <NuxtLink to="/contatti" class="btn btn-white">Prenota la consulenza</NuxtLink>
          <NuxtLink to="/registrati" class="btn">Iscriviti al sito</NuxtLink>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
/* Hero */
.hero { padding: 48px 0 80px; overflow: hidden; }
.hero-grid { display: grid; grid-template-columns: 1.05fr 1fr; gap: 40px; align-items: center; }
.hero h1 { margin-bottom: 24px; }
.cta { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 28px; }

.hero-photo { position: relative; justify-self: center; width: min(100%, 520px); }
.hero-photo img {
  position: relative; z-index: 1;
  border-radius: 260px 260px var(--radius) var(--radius);
  border: 3px solid var(--ink);
  aspect-ratio: 4 / 5; object-fit: cover; object-position: center 20%;
  box-shadow: 12px 12px 0 var(--magenta);
}
.blob {
  position: absolute; inset: -30px -40px auto auto; width: 70%; aspect-ratio: 1;
  background: var(--pink); border-radius: 50%; filter: blur(2px);
  animation: float 8s ease-in-out infinite;
}
.sticker {
  position: absolute; z-index: 2;
  background: var(--white); border: 2px solid var(--ink); border-radius: 18px;
  padding: 10px 16px; font-weight: 700; font-size: 0.85rem; line-height: 1.2;
  box-shadow: 4px 4px 0 var(--ink);
  animation: bob 5s ease-in-out infinite;
}
.sticker strong { display: block; font: 800 1.8rem/1 var(--font-display); color: var(--magenta); }
.s1 { left: -36px; top: 26%; transform: rotate(-6deg); }
.s2 { right: -28px; bottom: 18%; transform: rotate(5deg); animation-delay: -2s; }
.s3 { left: 12%; bottom: -18px; background: var(--ink); color: var(--white); animation-delay: -3.5s; }
@keyframes bob { 50% { translate: 0 -8px; } }
@keyframes float { 50% { translate: -16px 14px; } }

/* Metodo */
.steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 40px; }
.step { padding: 32px; border-radius: var(--radius); background: var(--white); border: 2px solid var(--ink); }
.step:nth-child(2) { background: var(--pink-soft); transform: translateY(20px); }
.num { font: 800 3.4rem/1 var(--font-display); color: var(--magenta); display: block; margin-bottom: 16px; }

/* Pacchetti */
.pkgs { background: var(--ink); color: var(--white); border-radius: 48px; margin: 0 12px; }
.pkgs .eyebrow { color: var(--pink); }
.head { display: flex; justify-content: space-between; align-items: end; gap: 20px; margin-bottom: 40px; flex-wrap: wrap; }
.head .btn { --bg: var(--white); --fg: var(--ink); }
.pkgs :deep(.pkg.c-black) { border-color: var(--white); background: #221b20; }

/* Servizi */
.services { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-top: 36px; }
.service { display: flex; gap: 16px; align-items: start; padding: 22px; border-radius: 18px; background: var(--white); border: 1px solid var(--line); transition: background .2s, transform .2s; }
.service:hover { background: var(--pink-soft); transform: rotate(-1deg); }
.service p { margin: 2px 0 0; font-size: 0.95rem; }
.ico { font-size: 1.8rem; display: grid; place-items: center; width: 54px; height: 54px; flex: none; background: var(--pink-soft); border-radius: 14px; }

/* Calendario */
.cal { background: var(--pink); }
.cal .eyebrow { color: var(--ink); }
.cal .lead { color: var(--ink); }
.cal-grid { display: grid; grid-template-columns: 1fr 1.2fr; gap: 48px; align-items: start; }

/* About */
.about { display: grid; grid-template-columns: 0.8fr 1fr; gap: 56px; align-items: center; }
.about-img { border-radius: var(--radius); border: 3px solid var(--ink); transform: rotate(-2deg); aspect-ratio: 1; object-fit: cover; }

/* CTA finale */
.final {
  background: var(--magenta); color: var(--white); text-align: center;
  border-radius: 40px; padding: clamp(48px, 8vw, 96px) 24px; border: 3px solid var(--ink);
  box-shadow: 12px 12px 0 var(--ink);
}
.final p { font-size: 1.2rem; max-width: 52ch; margin-inline: auto; }
.final .cta { justify-content: center; }

@media (max-width: 900px) {
  .hero-grid, .cal-grid, .about { grid-template-columns: 1fr; }
  .hero-photo { margin-top: 20px; width: min(100% - 40px, 440px); }
  .s1 { left: -16px; } .s2 { right: -14px; }
  .steps, .services { grid-template-columns: 1fr; }
  .step:nth-child(2) { transform: none; }
  .pkgs { margin: 0; border-radius: 32px; }
}
</style>
