<script setup lang="ts">
import type { Package } from '~/types'

definePageMeta({ layout: 'admin', middleware: 'admin' })
useHead({ title: 'Pacchetti · NDS' })

const api = useApi()
const { data: packages, refresh } = await useAsyncData('admin-packages', () => api<(Package & { orders_count: number })[]>('/admin/packages'))

const empty = () => ({
  id: null as number | null, name: '', category: 'glutei', tagline: '', description: '', price: 0,
  duration_weeks: 8, sessions_per_week: 2, level: 'Tutti i livelli', mode: 'Studio', color: 'pink',
  is_featured: false, is_active: true, features: '', program: '', sort: 0,
})
const form = reactive(empty())
const editing = ref(false)
const msg = ref<{ ok: boolean; text: string } | null>(null)

function open(p?: Package) {
  Object.assign(form, empty())
  if (p) Object.assign(form, {
    ...p, price: p.price_cents / 100,
    features: (p.features ?? []).join('\n'),
    program: (p.program ?? []).map((x) => `${x.title} | ${x.text}`).join('\n'),
  })
  msg.value = null
  editing.value = true
}

async function save() {
  const body = {
    ...form,
    price_cents: Math.round(Number(form.price) * 100),
    features: form.features.split('\n').map((s) => s.trim()).filter(Boolean),
    program: form.program.split('\n').filter((l) => l.includes('|')).map((l) => {
      const [title, ...rest] = l.split('|')
      return { title: title!.trim(), text: rest.join('|').trim() }
    }),
  }
  try {
    if (form.id) await api(`/admin/packages/${form.id}`, { method: 'PATCH', body })
    else await api('/admin/packages', { method: 'POST', body })
    editing.value = false
    await refresh()
  } catch (e) {
    msg.value = { ok: false, text: apiErrors(e).message }
  }
}

async function remove(p: Package) {
  if (!confirm(`Eliminare "${p.name}"? Verranno eliminate anche le relative richieste.`)) return
  await api(`/admin/packages/${p.id}`, { method: 'DELETE' })
  await refresh()
}
</script>

<template>
  <div>
    <div class="head">
      <h1>Pacchetti</h1>
      <button class="btn btn-pink" @click="open()">+ Nuovo pacchetto</button>
    </div>

    <form v-if="editing" class="card form" @submit.prevent="save">
      <h3>{{ form.id ? `Modifica: ${form.name}` : 'Nuovo pacchetto' }}</h3>
      <div v-if="msg" class="alert alert-err">{{ msg.text }}</div>
      <div class="grid">
        <div class="field"><label>Nome</label><input v-model="form.name" required></div>
        <div class="field">
          <label>Categoria</label>
          <select v-model="form.category">
            <option v-for="(l, k) in categoryLabels" :key="k" :value="k">{{ l }}</option>
          </select>
        </div>
        <div class="field"><label>Prezzo (€)</label><input v-model.number="form.price" type="number" min="0" step="0.01" required></div>
        <div class="field"><label>Durata (settimane)</label><input v-model.number="form.duration_weeks" type="number" min="1" required></div>
        <div class="field"><label>Allenamenti / settimana</label><input v-model.number="form.sessions_per_week" type="number" min="1" required></div>
        <div class="field"><label>Livello</label><input v-model="form.level" required></div>
        <div class="field"><label>Modalità</label><input v-model="form.mode" required></div>
        <div class="field">
          <label>Colore card</label>
          <select v-model="form.color"><option value="pink">Rosa</option><option value="magenta">Magenta</option><option value="black">Nero</option></select>
        </div>
        <div class="field"><label>Ordine</label><input v-model.number="form.sort" type="number"></div>
      </div>
      <div class="field"><label>Slogan</label><input v-model="form.tagline"></div>
      <div class="field"><label>Descrizione</label><textarea v-model="form.description" rows="3" /></div>
      <div class="grid2">
        <div class="field"><label>Cosa include (uno per riga)</label><textarea v-model="form.features" rows="5" /></div>
        <div class="field"><label>Programma (una fase per riga: Titolo | descrizione)</label><textarea v-model="form.program" rows="5" /></div>
      </div>
      <div class="checks">
        <label><input v-model="form.is_featured" type="checkbox"> In evidenza</label>
        <label><input v-model="form.is_active" type="checkbox"> Visibile sul sito</label>
      </div>
      <div class="acts">
        <button class="btn btn-pink">Salva</button>
        <button type="button" class="btn btn-ghost" @click="editing = false">Annulla</button>
      </div>
    </form>

    <div class="card table-wrap">
      <table class="table">
        <thead><tr><th>Nome</th><th>Categoria</th><th>Prezzo</th><th>Durata</th><th>Attivi</th><th>Stato</th><th /></tr></thead>
        <tbody>
          <tr v-for="p in packages" :key="p.id">
            <td><strong>{{ p.name }}</strong><span v-if="p.is_featured"> ★</span></td>
            <td>{{ categoryLabels[p.category] ?? p.category }}</td>
            <td>{{ euro(p.price_cents) }}</td>
            <td>{{ p.duration_weeks }} sett. · {{ p.sessions_per_week }}×</td>
            <td>{{ p.orders_count }}</td>
            <td><span class="pill" :class="p.is_active ? 'pill-ok' : 'pill-off'">{{ p.is_active ? 'Visibile' : 'Nascosto' }}</span></td>
            <td class="acts">
              <button class="btn btn-sm btn-ghost" @click="open(p)">Modifica</button>
              <button class="btn btn-sm btn-ghost" @click="remove(p)">Elimina</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
.head { display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; margin-bottom: 24px; }
.head h1 { margin: 0; }
.form { margin-bottom: 32px; box-shadow: 8px 8px 0 var(--pink); }
.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0 16px; }
.grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.checks { display: flex; gap: 24px; margin-bottom: 20px; font-weight: 700; }
.acts { display: flex; gap: 6px; flex-wrap: wrap; }
@media (max-width: 800px) { .grid2 { grid-template-columns: 1fr; } }
</style>
