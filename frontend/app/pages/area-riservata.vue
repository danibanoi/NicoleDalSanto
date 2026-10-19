<script setup lang="ts">
import type { Order, Session } from '~/types'

definePageMeta({ middleware: 'auth' })
useHead({ title: 'La mia area · NDS Personal Trainer' })

const { user, isAdmin } = useAuth()
if (isAdmin.value) await navigateTo('/admin')

const api = useApi()
const { data, refresh } = await useAsyncData('me-dashboard', () =>
  api<{ orders: Order[]; bookings: Session[] }>('/me/dashboard'),
)

const activeOrders = computed(() => (data.value?.orders ?? []).filter((o) => o.status === 'active'))
</script>

<template>
  <div class="container section">
    <span class="eyebrow">La mia area</span>
    <h1>Ciao {{ user?.name.split(' ')[0] }} 👋</h1>

    <div v-if="user?.status === 'pending'" class="alert alert-err">
      <strong>Account in attesa di attivazione.</strong> Richiedi un pacchetto: Nicole ti contatterà, confermerà il pagamento e attiverà il tuo account.
    </div>
    <div v-else-if="!activeOrders.length" class="alert alert-info">
      Non hai ancora un pacchetto attivo. <NuxtLink to="/pacchetti" class="lnk">Scegli il tuo percorso →</NuxtLink>
    </div>

    <div class="grid">
      <section>
        <div class="h">
          <h2>Le mie prenotazioni</h2>
          <NuxtLink to="/calendario" class="btn btn-sm btn-pink">+ Prenota</NuxtLink>
        </div>
        <SessionList :sessions="data?.bookings ?? []" @changed="refresh" />
      </section>

      <aside>
        <h2>I miei pacchetti</h2>
        <p v-if="!data?.orders.length" class="muted">Nessun pacchetto richiesto.</p>
        <article v-for="o in data?.orders" :key="o.id" class="order card">
          <div class="row">
            <NuxtLink :to="`/pacchetti/${o.package.slug}`" class="name">{{ o.package.name }}</NuxtLink>
            <span class="pill" :class="orderStatus[o.status]?.cls">{{ orderStatus[o.status]?.label }}</span>
          </div>
          <p class="muted small">
            {{ euro(o.price_cents) }}
            <template v-if="o.starts_on"> · dal {{ fmtDate(o.starts_on) }} al {{ fmtDate(o.ends_on) }}</template>
            <template v-else> · richiesto il {{ fmtDate(o.created_at) }}</template>
          </p>
        </article>
        <NuxtLink to="/pacchetti" class="btn btn-ghost btn-sm">Vedi tutti i pacchetti</NuxtLink>
      </aside>
    </div>
  </div>
</template>

<style scoped>
.grid { display: grid; grid-template-columns: 1.4fr 1fr; gap: 48px; margin-top: 32px; align-items: start; }
.h { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 16px; }
h2 { font-size: 1.8rem; margin: 0 0 16px; }
.h h2 { margin: 0; }
.order { padding: 20px; margin-bottom: 12px; }
.row { display: flex; justify-content: space-between; gap: 10px; align-items: center; }
.name { font: 700 1.15rem var(--font-display); text-decoration: none; }
.small { font-size: 0.88rem; margin: 6px 0 0; }
.lnk { color: var(--pink); font-weight: 700; }
@media (max-width: 900px) { .grid { grid-template-columns: 1fr; } }
</style>
