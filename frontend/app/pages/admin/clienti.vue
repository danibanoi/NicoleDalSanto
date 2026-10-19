<script setup lang="ts">
definePageMeta({ layout: 'admin', middleware: 'admin' })
useHead({ title: 'Clienti · NDS' })

const api = useApi()
const q = ref('')
const status = ref('')
const { data: users, refresh } = await useAsyncData('admin-users-list',
  () => api<any[]>('/admin/users', { query: { q: q.value || undefined, status: status.value || undefined } }),
  { watch: [status] })

async function setStatus(u: any, s: string) {
  await api(`/admin/users/${u.id}`, { method: 'PATCH', body: { status: s } })
  await refresh()
}
</script>

<template>
  <div>
    <h1>Clienti</h1>
    <form class="filters" @submit.prevent="refresh()">
      <input v-model="q" class="input" placeholder="Cerca nome o email…">
      <select v-model="status" class="input">
        <option value="">Tutti</option>
        <option v-for="(s, k) in userStatus" :key="k" :value="k">{{ s.label }}</option>
      </select>
      <button class="btn btn-sm">Cerca</button>
    </form>

    <div class="card table-wrap">
      <table class="table">
        <thead><tr><th>Nome</th><th>Contatti</th><th>Iscritta il</th><th>Pacchetti attivi</th><th>Prenotazioni</th><th>Stato</th><th /></tr></thead>
        <tbody>
          <tr v-for="u in users" :key="u.id">
            <td><strong>{{ u.name }}</strong></td>
            <td><small>{{ u.email }}<br>{{ u.phone }}</small></td>
            <td>{{ fmtDate(u.created_at) }}</td>
            <td>{{ u.orders_count }}</td>
            <td>{{ u.bookings_count }}</td>
            <td><span class="pill" :class="userStatus[u.status]?.cls">{{ userStatus[u.status]?.label }}</span></td>
            <td class="acts">
              <button v-if="u.status !== 'active'" class="btn btn-sm btn-pink" @click="setStatus(u, 'active')">Attiva</button>
              <button v-if="u.status !== 'disabled'" class="btn btn-sm btn-ghost" @click="setStatus(u, 'disabled')">Disattiva</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="!users?.length" class="muted">Nessun cliente trovato.</p>
    </div>
  </div>
</template>

<style scoped>
.filters { display: flex; gap: 10px; margin: 24px 0; flex-wrap: wrap; }
.filters .input { width: auto; min-width: 200px; }
.acts { display: flex; gap: 6px; }
</style>
