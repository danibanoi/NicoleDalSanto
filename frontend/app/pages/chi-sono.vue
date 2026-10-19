<script setup lang="ts">
useHead({ title: 'Chi sono · Nicole Dal Santo' })
const values = [
  { t: 'Programmi personalizzati', d: 'Ogni percorso parte da te: nessuna scheda standard.' },
  { t: 'Studio privato', d: 'Un ambiente riservato, accogliente e senza giudizio.' },
  { t: 'Supporto continuo', d: 'Ti seguo anche fuori dallo studio, passo dopo passo.' },
  { t: 'Allenamenti efficaci', d: 'Metodo, progressione e tecnica, sempre in sicurezza.' },
]
</script>

<template>
  <div>
    <section class="container section intro">
      <div>
        <span class="eyebrow">Chi sono</span>
        <h1>Ciao, sono <span class="hl">Nicole.</span></h1>
        <p class="lead">Personal trainer certificata e fondatrice di <strong>NDS Personal Trainer</strong>. Il mio approccio è semplice: l'allenamento deve essere costruito sulla persona, non il contrario.</p>
        <p class="lead">Credo nella professionalità, nell'ascolto e in un ambiente in cui ti senta a tuo agio. Non cerco la perfezione: cerco progressi che possano durare nel tempo.</p>
        <div class="stats">
          <div><strong>10+</strong><span>anni di esperienza</span></div>
          <div><strong>1000+</strong><span>persone allenate</span></div>
          <div><strong>1:1</strong><span>attenzione su di te</span></div>
        </div>
      </div>
      <img src="/img/nicole.jpg" alt="Nicole Dal Santo" class="photo">
    </section>

    <section class="values">
      <div class="container grid-2">
        <div v-for="v in values" :key="v.t" class="value">
          <h3>{{ v.t }}</h3>
          <p>{{ v.d }}</p>
        </div>
      </div>
    </section>

    <section class="container section center">
      <h2>Persona prima della prestazione.</h2>
      <p class="lead mx">Che tu voglia dimagrire, tonificare, allenarti in gravidanza o migliorare la postura, troviamo insieme il percorso giusto.</p>
      <NuxtLink to="/contatti" class="btn btn-pink">Prenota la tua consulenza</NuxtLink>
    </section>
  </div>
</template>

<style scoped>
.intro { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 56px; align-items: center; }
.photo { border-radius: 50% 50% var(--radius) var(--radius); border: 3px solid var(--ink); box-shadow: -12px 12px 0 var(--magenta); aspect-ratio: 4/5; object-fit: cover; }
.stats { display: flex; flex-wrap: wrap; gap: 28px; margin-top: 32px; }
.stats strong { display: block; font: 800 2.6rem/1 var(--font-display); color: var(--magenta); }
.stats span { font-size: 0.9rem; font-weight: 700; }
.values { background: var(--ink); color: var(--white); padding: 72px 0; }
.value { padding: 28px; border: 2px solid #3a3036; border-radius: var(--radius); }
.value h3 { color: var(--pink); }
.value p { margin: 0; opacity: .85; }
.center { text-align: center; }
.mx { margin-inline: auto; margin-bottom: 28px; }
@media (max-width: 900px) { .intro { grid-template-columns: 1fr; } }
</style>
