<script setup lang="ts">
import type { Session } from '~/types'

const props = defineProps<{ sessions: Session[]; compact?: boolean }>()
const emit = defineEmits<{ changed: [] }>()

const { loggedIn, user } = useAuth()
const api = useApi()
const busy = ref<number | null>(null)
const feedback = ref<{ id: number; ok: boolean; text: string } | null>(null)

const days = computed(() => {
  const map = new Map<string, Session[]>()
  for (const s of props.sessions) {
    const k = dayKey(s.starts_at)
    map.set(k, [...(map.get(k) ?? []), s])
  }
  return [...map.entries()].map(([key, items]) => ({ key, label: fmtDay(items[0]!.starts_at), items }))
})

async function toggle(s: Session) {
  busy.value = s.id
  feedback.value = null
  try {
    await api(`/sessions/${s.id}/book`, { method: s.is_booked ? 'DELETE' : 'POST' })
    feedback.value = { id: s.id, ok: true, text: s.is_booked ? 'Prenotazione annullata.' : 'Prenotata! Ci vediamo lì 💪' }
    emit('changed')
  } catch (e) {
    feedback.value = { id: s.id, ok: false, text: apiErrors(e).message }
  } finally {
    busy.value = null
  }
}
</script>

<template>
  <div class="days">
    <p v-if="!sessions.length" class="empty">Nessuna data in programma in questo periodo.</p>

    <section v-for="d in days" :key="d.key" class="day">
      <h4>{{ d.label }}</h4>
      <article v-for="s in d.items" :key="s.id" class="row" :class="{ booked: s.is_booked }">
        <div class="time">
          <strong>{{ fmtTime(s.starts_at) }}</strong>
          <span>{{ fmtTime(s.ends_at) }}</span>
        </div>
        <div class="info">
          <div class="title">
            {{ s.title }}
            <NuxtLink v-if="s.package && !compact" :to="`/pacchetti/${s.package.slug}`" class="pill">{{ s.package.name }}</NuxtLink>
          </div>
          <div class="meta">
            📍 {{ s.location }} ·
            <span :class="{ low: s.spots_left <= 2 }">{{ s.spots_left ? `${s.spots_left} posti liberi` : 'Completo' }}</span>
          </div>
          <div v-if="feedback?.id === s.id" class="fb" :class="feedback.ok ? 'ok' : 'err'">{{ feedback.text }}</div>
        </div>
        <div class="act">
          <NuxtLink v-if="!loggedIn" :to="{ path: '/accedi', query: { redirect: $route.fullPath } }" class="btn btn-sm btn-ghost">Accedi per prenotare</NuxtLink>
          <button v-else-if="s.is_booked" class="btn btn-sm btn-ghost" :disabled="busy === s.id" @click="toggle(s)">✓ Prenotata · Disdici</button>
          <button v-else-if="s.can_book" class="btn btn-sm btn-pink" :disabled="busy === s.id || !s.spots_left" @click="toggle(s)">Prenota</button>
          <span v-else-if="user?.status === 'pending'" class="hint">In attesa di attivazione</span>
          <NuxtLink v-else-if="s.package" :to="`/pacchetti/${s.package.slug}`" class="hint link">Richiedi il pacchetto →</NuxtLink>
        </div>
      </article>
    </section>
  </div>
</template>

<style scoped>
.days { display: grid; gap: 28px; }
.empty { padding: 40px; text-align: center; background: var(--white); border-radius: var(--radius); border: 2px dashed var(--line); }
h4 { font-size: 1.05rem; text-transform: capitalize; margin-bottom: 10px; }
.row {
  display: grid; grid-template-columns: 74px 1fr auto; gap: 18px; align-items: center;
  background: var(--white); border: 2px solid var(--ink); border-radius: 18px;
  padding: 14px 18px; margin-bottom: 10px;
  transition: transform .2s;
}
.row:hover { transform: translateX(4px); }
.row.booked { background: var(--pink-soft); border-color: var(--magenta); }
.time { display: grid; text-align: center; border-right: 2px dashed var(--line); padding-right: 12px; }
.time strong { font: 800 1.3rem/1 var(--font-display); }
.time span { font-size: 0.8rem; color: var(--ink-soft); }
.title { font-weight: 700; display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
.title .pill { text-decoration: none; }
.meta { font-size: 0.88rem; color: var(--ink-soft); }
.low { color: var(--magenta); font-weight: 700; }
.fb { font-size: 0.85rem; font-weight: 700; margin-top: 4px; }
.fb.ok { color: var(--ok); } .fb.err { color: var(--magenta-dark); }
.hint { font-size: 0.85rem; font-weight: 700; color: var(--ink-soft); }
.hint.link { color: var(--magenta); text-decoration: none; }
@media (max-width: 640px) {
  .row { grid-template-columns: 64px 1fr; }
  .act { grid-column: 1 / -1; }
  .act .btn { width: 100%; }
}
</style>
