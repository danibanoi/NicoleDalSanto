<script setup lang="ts">
import type { Order, Package } from '~/types'

definePageMeta({ layout: 'admin', middleware: 'admin' })
useHead({ title: 'Richieste · NDS' })

const api = useApi()
const status = ref('')
const { data: orders, refresh } = await useAsyncData('admin-orders',
  () => api<Order[]>('/admin/orders', { query: { status: status.value || undefined } }), { watch: [status] })
const { data: packages } = await useAsyncData('admin-packages', () => api<Package[]>('/admin/packages'))
const { data: clients } = await useAsyncData('admin-users', () => api<any[]>('/admin/users'))

async function setStatus(o: Order, s: string) {
  await api(`/admin/orders/${o.id}`, { method: 'PATCH', body: { status: s } })
  await refresh()
}

// Assegna un pacchetto direttamente (es. pagato in studio)
const assign = reactive({ user_id: '', package_id: '', notes: '' })
const assignMsg = ref('')
async function submitAssign() {
  try {
    await api('/admin/orders', { method: 'POST', body: assign })
    assignMsg.value = 'Pacchetto assegnato e attivato ✓'
    Object.assign(assign, { user_id: '', package_id: '', notes: '' })
    await refresh()
  } catch (e) {
    assignMsg.value = apiErrors(e).message
  }
}
</script>

<template>
  <div>
    <h1>Richieste pacchetti</h1>

    <form class="card assign" @submit.prevent="submitAssign">
      <h3>Assegna un pacchetto</h3>
      <div class="row">
        <select v-model="assign.user_id" class="input" required>
          <option value="">Cliente…</option>
          <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }} ({{ c.email }})</option>
        </select>
        <select v-model="assign.package_id" class="input" required>
          <option value="">Pacchetto…</option>
          <option v-for="p in packages" :key="p.id" :value="p.id">{{ p.name }} · {{ euro(p.price_cents) }}</option>
        </select>
        <input v-model="assign.notes" class="input" placeholder="Note (facoltative)">
        <button class="btn btn-pink">Assegna</button>
      </div>
      <p v-if="assignMsg" class="msg">{{ assignMsg }}</p>
    </form>

    <div class="filters">
      <select v-model="status" class="input" style="width:auto">
        <option value="">Tutti gli stati</option>
        <option v-for="(s, k) in orderStatus" :key="k" :value="k">{{ s.label }}</option>
      </select>
    </div>

    <div class="card table-wrap">
      <table class="table">
        <thead><tr><th>Data</th><th>Cliente</th><th>Pacchetto</th><th>Periodo</th><th>Stato</th><th>Azioni</th></tr></thead>
        <tbody>
          <tr v-for="o in orders" :key="o.id">
            <td>{{ fmtDate(o.created_at) }}</td>
            <td><strong>{{ o.user?.name }}</strong><br><small>{{ o.user?.email }}</small><br><small v-if="o.notes" class="muted">“{{ o.notes }}”</small></td>
            <td>{{ o.package.name }}<br><small>{{ euro(o.price_cents) }}</small></td>
            <td><small>{{ fmtDate(o.starts_on) }} → {{ fmtDate(o.ends_on) }}</small></td>
            <td><span class="pill" :class="orderStatus[o.status]?.cls">{{ orderStatus[o.status]?.label }}</span></td>
            <td class="acts">
              <button v-if="o.status === 'pending'" class="btn btn-sm btn-pink" @click="setStatus(o, 'active')">Attiva</button>
              <button v-if="o.status === 'active'" class="btn btn-sm btn-ghost" @click="setStatus(o, 'completed')">Concludi</button>
              <button v-if="['pending', 'active'].includes(o.status)" class="btn btn-sm btn-ghost" @click="setStatus(o, 'cancelled')">Annulla</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
.assign { margin: 24px 0; }
.row { display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 10px; }
.msg { margin: 10px 0 0; font-weight: 700; color: var(--magenta); }
.filters { margin-bottom: 16px; }
.acts { display: flex; gap: 6px; flex-wrap: wrap; }
@media (max-width: 1100px) { .row { grid-template-columns: 1fr; } }
</style>
