<script setup lang="ts">
import type { Package } from '~/types'

definePageMeta({ layout: 'admin', middleware: 'admin' })
useHead({ title: 'Calendario · NDS' })

const api = useApi()
const { data: packages } = await useAsyncData('admin-packages', () => api<Package[]>('/admin/packages'))
const { data: sessions, refresh } = await useAsyncData('admin-sessions', () => api<any[]>('/admin/sessions'))

const empty = () => ({ id: null as number | null, title: '', package_id: '' as number | '', description: '', starts_at: '', ends_at: '', location: 'Studio Pescantina', capacity: 6, repeat_weeks: 0 })
const form = reactive(empty())
const msg = ref<{ ok: boolean; text: string } | null>(null)

// Scegliendo un pacchetto, il titolo si precompila
watch(() => form.package_id, (id) => {
  const p = packages.value?.find((x) => x.id === id)
  if (p && !form.title) form.title = p.name
})
// Durata di default 1 ora
watch(() => form.starts_at, (v) => {
  if (v && !form.ends_at) {
    const d = new Date(v); d.setHours(d.getHours() + 1)
    form.ends_at = `${v.slice(0, 11)}${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}`
  }
})

async function save() {
  msg.value = null
  const body = { ...form, package_id: form.package_id || null }
  try {
    if (form.id) await api(`/admin/sessions/${form.id}`, { method: 'PATCH', body })
    else await api('/admin/sessions', { method: 'POST', body })
    msg.value = { ok: true, text: form.id ? 'Sessione aggiornata ✓' : 'Sessione creata ✓' }
    Object.assign(form, empty())
    await refresh()
  } catch (e) {
    msg.value = { ok: false, text: apiErrors(e).message }
  }
}

function edit(s: any) {
  Object.assign(form, {
    id: s.id, title: s.title, package_id: s.package_id ?? '', description: s.description ?? '',
    starts_at: toLocalInput(s.starts_at), ends_at: toLocalInput(s.ends_at),
    location: s.location, capacity: s.capacity, repeat_weeks: 0,
  })
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

async function remove(s: any) {
  if (!confirm(`Eliminare "${s.title}" del ${fmtShortDay(s.starts_at)}? Le prenotazioni verranno cancellate.`)) return
  await api(`/admin/sessions/${s.id}`, { method: 'DELETE' })
  await refresh()
}

const days = computed(() => {
  const map = new Map<string, any[]>()
  for (const s of sessions.value ?? []) map.set(dayKey(s.starts_at), [...(map.get(dayKey(s.starts_at)) ?? []), s])
  return [...map.values()]
})
</script>

<template>
  <div>
    <h1>Calendario</h1>

    <form class="card form" @submit.prevent="save">
      <h3>{{ form.id ? 'Modifica sessione' : 'Nuova sessione' }}</h3>
      <div v-if="msg" class="alert" :class="msg.ok ? 'alert-ok' : 'alert-err'">{{ msg.text }}</div>
      <div class="grid">
        <div class="field">
          <label>Programma</label>
          <select v-model="form.package_id">
            <option value="">Aperta a tutti i clienti attivi</option>
            <option v-for="p in packages" :key="p.id" :value="p.id">{{ p.name }}</option>
          </select>
        </div>
        <div class="field"><label>Titolo</label><input v-model="form.title" required></div>
        <div class="field"><label>Inizio</label><input v-model="form.starts_at" type="datetime-local" required></div>
        <div class="field"><label>Fine</label><input v-model="form.ends_at" type="datetime-local" required></div>
        <div class="field"><label>Luogo</label><input v-model="form.location" required></div>
        <div class="field"><label>Posti</label><input v-model.number="form.capacity" type="number" min="1" required></div>
        <div v-if="!form.id" class="field">
          <label>Ripeti per altre settimane</label>
          <input v-model.number="form.repeat_weeks" type="number" min="0" max="26">
        </div>
      </div>
      <div class="acts">
        <button class="btn btn-pink">{{ form.id ? 'Salva modifiche' : 'Crea sessione' }}</button>
        <button v-if="form.id" type="button" class="btn btn-ghost" @click="Object.assign(form, empty())">Annulla</button>
      </div>
    </form>

    <section v-for="group in days" :key="group[0].id" class="day">
      <h4>{{ fmtDay(group[0].starts_at) }}</h4>
      <div v-for="s in group" :key="s.id" class="card sess">
        <div class="info">
          <strong>{{ fmtTime(s.starts_at) }}–{{ fmtTime(s.ends_at) }} · {{ s.title }}</strong>
          <small class="muted">{{ s.location }} · {{ s.bookings.length }}/{{ s.capacity }} posti</small>
          <div v-if="s.bookings.length" class="people">
            <span v-for="b in s.bookings" :key="b.id" class="pill">{{ b.user.name }}</span>
          </div>
        </div>
        <div class="acts">
          <button class="btn btn-sm btn-ghost" @click="edit(s)">Modifica</button>
          <button class="btn btn-sm btn-ghost" @click="remove(s)">Elimina</button>
        </div>
      </div>
    </section>
    <p v-if="!days.length" class="muted">Nessuna sessione nelle prossime 6 settimane.</p>
  </div>
</template>

<style scoped>
.form { margin: 24px 0 40px; }
.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 0 16px; }
.acts { display: flex; gap: 8px; flex-wrap: wrap; }
.day { margin-bottom: 24px; }
h4 { text-transform: capitalize; margin-bottom: 8px; }
.sess { display: flex; justify-content: space-between; gap: 16px; padding: 16px 20px; margin-bottom: 8px; align-items: center; flex-wrap: wrap; }
.info { display: grid; gap: 4px; }
.people { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px; }
</style>
