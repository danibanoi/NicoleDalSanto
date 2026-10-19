<script setup lang="ts">
const { instagram } = useRuntimeConfig().public
</script>

<template>
  <footer class="footer">
    <div class="container">
      <div class="big">ALLENATI. <span class="hl">CAMBIA.</span> SENTITI BENE.</div>
      <div class="cols">
        <div>
          <AppLogo />
          <p class="muted-w">Studio privato a Pescantina (VR).<br>Allenamenti costruiti sulla persona, non il contrario.</p>
        </div>
        <div>
          <h4>Esplora</h4>
          <NuxtLink to="/pacchetti">Pacchetti</NuxtLink>
          <NuxtLink to="/calendario">Calendario</NuxtLink>
          <NuxtLink to="/chi-sono">Chi sono</NuxtLink>
          <NuxtLink to="/contatti">Contatti</NuxtLink>
        </div>
        <div>
          <h4>Seguimi</h4>
          <a :href="instagram" target="_blank" rel="noopener">Instagram · @nds_personaltrainer</a>
          <NuxtLink to="/contatti">Prenota la tua consulenza</NuxtLink>
        </div>
      </div>
      <p class="copy">© {{ new Date().getFullYear() }} NDS Personal Trainer · Nicole Dal Santo</p>
    </div>
  </footer>
</template>

<style scoped>
.footer { background: var(--ink); color: var(--white); padding: 72px 0 28px; margin-top: 40px; }
.big { font: 800 clamp(2rem, 6vw, 4.6rem)/1 var(--font-display); letter-spacing: -0.03em; margin-bottom: 56px; }
.cols { display: grid; gap: 40px; grid-template-columns: 2fr 1fr 1fr; }
.cols :deep(.mark) { background: var(--magenta); }
.cols :deep(.mark .d) { color: var(--ink); }
.cols :deep(.txt small) { color: var(--pink); }
h4 { font-size: 0.8rem; text-transform: uppercase; letter-spacing: .16em; color: var(--pink); margin-bottom: 14px; }
.cols a { display: block; text-decoration: none; opacity: .85; margin-bottom: 8px; }
.cols a:hover { opacity: 1; color: var(--pink); }
.muted-w { opacity: .7; margin-top: 16px; }
.copy { margin: 56px 0 0; padding-top: 20px; border-top: 1px solid #333; font-size: 0.85rem; opacity: .6; }
@media (max-width: 760px) { .cols { grid-template-columns: 1fr; } }
</style>
