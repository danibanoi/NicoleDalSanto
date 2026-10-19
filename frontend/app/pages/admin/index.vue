<script setup lang="ts">
import type { Order } from '~/types'

definePageMeta({ layout: 'admin', middleware: 'admin' })
useHead({ title: 'Pannello · NDS' })

const api = useApi()
const { data: stats } = await useAsyncData('admin-stats', () => api<Record<string, number>>('/admin/stats'))
const { data: pending, refresh } = await useAsyncData('admin-pending', () => api<Order[]>('/admin/orders', { query: { status: 'pending' } }))

async function activate(o: Order) {
  await api(`/admin/orders/${o.id}`, { method: 'PATCH', body: { status: 'active' } })
  await refresh()
}

const cards = computed(() => [
  { label: 'Richieste da confermare', value: stats.value?.pending_orders, to: '/admin/richieste' },
  { label: 'Account da attivare', value: stats.value?.pending_users, to: '/admin/clienti' },
  { label: 'Clienti attivi', value: stats.value?.active_clients, to: '/admin/clienti' },
  { label: 'Sessioni in programma', value: stats.value?.upcoming_sessions, to: '/admin/calendario' },
  { label: 'Messaggi non letti', value: stats.value?.unread_messages, to: '/admin/messaggi' },
  { label: 'Incassato (pacchetti)', value: euro(stats.value?.revenue_cents ?? 0), to: '/admin/richieste' },
])
</script>

<template>
  <div>
    <h1>Ciao Nicole ✦</h1>
    <p class="muted">Ecco cosa succede oggi.</p>

    <div class="stats">
      <NuxtLink v-for="c in cards" :key="c.label" :to="c.to" class="stat">
        <strong>{{ c.value ?? '—' }}</strong>
        <span>{{ c.label }}</span>
      </NuxtLink>
    </div>

    <section class="card">
      <h3>Richieste in attesa</h3>
      <p v-if="!pending?.length" class="muted">Nessuna richiesta in attesa 🎉</p>
      <div v-else class="table-wrap">
        <table class="table">
          <thead><tr><th>Cliente</th><th>Pacchetto</th><th>Note</th><th /></tr></thead>
          <tbody>
            <tr v-for="o in pending" :key="o.id">
              <td><strong>{{ o.user?.name }}</strong><br><small>{{ o.user?.email }} {{ o.user?.phone }}</small></td>
              <td>{{ o.package.name }}<br><small>{{ euro(o.price_cents) }}</small></td>
              <td><small>{{ o.notes || '—' }}</small></td>
              <td><button class="btn btn-sm btn-pink" @click="activate(o)">Conferma pagato</button></td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>

<style scoped>
.stats { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 14px; margin: 28px 0 36px; }
.stat { padding: 22px; border-radius: 18px; background: var(--white); border: 2px solid var(--ink); text-decoration: none; transition: transform .2s, box-shadow .2s; }
.stat:hover { transform: translateY(-3px); box-shadow: 5px 5px 0 var(--magenta); }
.stat strong { display: block; font: 800 2.2rem/1 var(--font-display); color: var(--magenta); }
.stat span { font-size: 0.9rem; font-weight: 700; }
</style>
