<script setup lang="ts">
const { logout } = useAuth()
const links = [
  { to: '/admin', label: 'Panoramica', icon: '✦' },
  { to: '/admin/richieste', label: 'Richieste pacchetti', icon: '🧾' },
  { to: '/admin/clienti', label: 'Clienti', icon: '👯‍♀️' },
  { to: '/admin/calendario', label: 'Calendario', icon: '📅' },
  { to: '/admin/pacchetti', label: 'Pacchetti', icon: '📦' },
  { to: '/admin/messaggi', label: 'Messaggi', icon: '💌' },
]
</script>

<template>
  <div class="admin">
    <aside class="side">
      <AppLogo />
      <nav>
        <NuxtLink v-for="l in links" :key="l.to" :to="l.to" :class="{ exact: l.to === '/admin' }">
          <span>{{ l.icon }}</span>{{ l.label }}
        </NuxtLink>
      </nav>
      <div class="bottom">
        <NuxtLink to="/" class="small">← Vai al sito</NuxtLink>
        <button class="btn btn-sm btn-white" @click="logout">Esci</button>
      </div>
    </aside>
    <main class="content"><slot /></main>
  </div>
</template>

<style scoped>
.admin { display: grid; grid-template-columns: 260px 1fr; min-height: 100vh; }
.side { background: var(--ink); color: var(--white); padding: 24px 16px; display: flex; flex-direction: column; gap: 28px; position: sticky; top: 0; height: 100vh; }
.side :deep(.mark) { background: var(--magenta); }
.side :deep(.mark .d) { color: var(--ink); }
.side :deep(.txt small) { color: var(--pink); }
nav { display: grid; gap: 4px; }
nav a { display: flex; gap: 12px; padding: 12px 14px; border-radius: 12px; text-decoration: none; font-weight: 700; opacity: .8; }
nav a:hover { background: #2a2228; opacity: 1; }
nav a.router-link-exact-active, nav a:not(.exact).router-link-active { background: var(--magenta); opacity: 1; }
.bottom { margin-top: auto; display: grid; gap: 12px; }
.small { font-size: 0.9rem; opacity: .7; text-decoration: none; }
.content { padding: 40px clamp(16px, 4vw, 56px); min-width: 0; }
.content :deep(h1) { font-size: 2.6rem; }
@media (max-width: 900px) {
  .admin { grid-template-columns: 1fr; }
  .side { position: static; height: auto; }
  nav { grid-template-columns: repeat(2, 1fr); }
}
</style>
