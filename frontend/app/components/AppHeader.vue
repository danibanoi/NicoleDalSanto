<script setup lang="ts">
const { loggedIn, isAdmin, user, logout } = useAuth()
const open = ref(false)
const route = useRoute()
watch(() => route.fullPath, () => (open.value = false))

const links = [
  { to: '/pacchetti', label: 'Pacchetti' },
  { to: '/calendario', label: 'Calendario' },
  { to: '/chi-sono', label: 'Chi sono' },
  { to: '/contatti', label: 'Contatti' },
]
</script>

<template>
  <header class="header">
    <div class="container bar">
      <AppLogo />

      <nav :class="{ open }">
        <NuxtLink v-for="l in links" :key="l.to" :to="l.to" class="nav-link">{{ l.label }}</NuxtLink>
        <template v-if="loggedIn">
          <NuxtLink :to="isAdmin ? '/admin' : '/area-riservata'" class="btn btn-sm btn-pink">
            {{ isAdmin ? 'Pannello' : `Ciao ${user?.name.split(' ')[0]}` }}
          </NuxtLink>
          <button class="btn btn-sm btn-ghost" @click="logout">Esci</button>
        </template>
        <template v-else>
          <NuxtLink to="/accedi" class="nav-link">Accedi</NuxtLink>
          <NuxtLink to="/registrati" class="btn btn-sm btn-pink">Iscriviti</NuxtLink>
        </template>
      </nav>

      <button class="burger" :aria-expanded="open" aria-label="Menu" @click="open = !open">
        <span /><span />
      </button>
    </div>
  </header>
</template>

<style scoped>
.header {
  position: sticky; top: 0; z-index: 50;
  background: color-mix(in srgb, var(--pink-pale) 85%, transparent);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--line);
}
.bar { display: flex; align-items: center; justify-content: space-between; height: 76px; }
nav { display: flex; align-items: center; gap: 24px; }
.nav-link { text-decoration: none; font-weight: 700; font-size: 0.95rem; position: relative; }
.nav-link::after {
  content: ''; position: absolute; left: 0; right: 0; bottom: -4px; height: 3px; border-radius: 3px;
  background: var(--magenta); transform: scaleX(0); transition: transform .2s;
}
.nav-link:hover::after, .nav-link.router-link-active::after { transform: scaleX(1); }
.burger { display: none; background: none; border: 0; width: 44px; height: 44px; cursor: pointer; }
.burger span { display: block; height: 3px; margin: 7px 8px; background: var(--ink); border-radius: 3px; transition: transform .2s; }
.burger[aria-expanded='true'] span:first-child { transform: translateY(5px) rotate(45deg); }
.burger[aria-expanded='true'] span:last-child { transform: translateY(-5px) rotate(-45deg); }

@media (max-width: 900px) {
  .burger { display: block; }
  nav {
    position: absolute; top: 76px; left: 0; right: 0;
    flex-direction: column; align-items: stretch; gap: 6px; padding: 16px;
    background: var(--pink-pale); border-bottom: 2px solid var(--ink);
    display: none;
  }
  nav.open { display: flex; }
  .nav-link { padding: 12px 4px; font-size: 1.15rem; }
}
</style>
