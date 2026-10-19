<script setup lang="ts">
import type { Package, Session } from '~/types'

useHead({ title: 'Calendario · NDS Personal Trainer' })

const api = useApi()
const { loggedIn, user } = useAuth()

const weekOffset = ref(0)
const pkg = ref('')

const range = computed(() => {
  const now = new Date()
  const monday = new Date(now)
  monday.setDate(now.getDate() - ((now.getDay() + 6) % 7) + weekOffset.value * 7)
  const sunday = new Date(monday)
  sunday.setDate(monday.getDate() + 13)
  const iso = (d: Date) => dayKey(d.toISOString())
  return { from: iso(weekOffset.value === 0 ? now : monday), to: iso(sunday), monday, sunday }
})

const { data: packages } = await useAsyncData('packages', () => api<Package[]>('/packages'))
const { data: sessions, refresh, pending } = await useAsyncData(
  'calendar',
  () => api<Session[]>('/sessions', { query: { from: range.value.from, to: range.value.to, package: pkg.value || undefined } }),
  { watch: [range, pkg] },
)

const label = computed(() => {
  const f = new Intl.DateTimeFormat('it-IT', { day: 'numeric', month: 'short' })
  return `${f.format(range.value.monday)} – ${f.format(range.value.sunday)}`
})
const withDates = computed(() => (packages.value ?? []).filter((p) => p.category !== 'personal'))
</script>

<template>
  <div class="container section">
    <span class="eyebrow">Calendario</span>
    <h1>Programmi <span class="hl">&amp;</span> date.</h1>
    <p class="lead">Guarda tutti gli allenamenti in programma. Quando sei iscritta e il tuo pacchetto è attivo, prenoti il tuo posto con un click.</p>

    <div v-if="!loggedIn" class="alert alert-info">
      Per prenotare devi avere un account. <NuxtLink to="/registrati">Iscriviti</NuxtLink> o <NuxtLink to="/accedi">accedi</NuxtLink>.
    </div>
    <div v-else-if="user?.status === 'pending'" class="alert alert-err">
      Il tuo account è in attesa di attivazione: Nicole lo attiverà a breve.
    </div>

    <div class="toolbar">
      <div class="weeks">
        <button class="btn btn-sm btn-ghost" :disabled="weekOffset === 0" @click="weekOffset -= 2">←</button>
        <strong>{{ label }}</strong>
        <button class="btn btn-sm btn-ghost" :disabled="weekOffset >= 8" @click="weekOffset += 2">→</button>
      </div>
      <select v-model="pkg" class="input sel" aria-label="Filtra per programma">
        <option value="">Tutti i programmi</option>
        <option v-for="p in withDates" :key="p.id" :value="p.slug">{{ p.name }}</option>
      </select>
    </div>

    <div :class="{ loading: pending }">
      <SessionList :sessions="sessions ?? []" @changed="refresh" />
    </div>
  </div>
</template>

<style scoped>
.alert a { color: var(--pink); font-weight: 700; }
.toolbar { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 16px; align-items: center; margin: 32px 0; }
.weeks { display: flex; align-items: center; gap: 14px; }
.weeks strong { font: 800 1.2rem var(--font-display); min-width: 150px; text-align: center; }
.sel { width: auto; min-width: 240px; }
.loading { opacity: .5; transition: opacity .2s; }
</style>
