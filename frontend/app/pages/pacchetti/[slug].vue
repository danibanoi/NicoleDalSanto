<script setup lang="ts">
import type { Package, Session } from '~/types'

const route = useRoute()
const api = useApi()
const { loggedIn, user } = useAuth()

const { data, refresh, error } = await useAsyncData(`pkg-${route.params.slug}`, () =>
  api<{ package: Package; sessions: Session[] }>(`/packages/${route.params.slug}`),
)
if (error.value) throw createError({ statusCode: 404, statusMessage: 'Pacchetto non trovato', fatal: true })

const pkg = computed(() => data.value!.package)
useHead({ title: () => `${pkg.value.name} · NDS Personal Trainer` })

const notes = ref('')
const sending = ref(false)
const msg = ref<{ ok: boolean; text: string } | null>(null)

async function requestPackage() {
  sending.value = true
  msg.value = null
  try {
    await api('/me/orders', { method: 'POST', body: { package_id: pkg.value.id, notes: notes.value } })
    msg.value = { ok: true, text: 'Richiesta inviata! Nicole ti contatterà per confermare e attivare il pacchetto.' }
  } catch (e) {
    msg.value = { ok: false, text: apiErrors(e).message }
  } finally {
    sending.value = false
  }
}
</script>

<template>
  <div v-if="pkg">
    <section class="hero" :class="`c-${pkg.color}`">
      <div class="container">
        <NuxtLink to="/pacchetti" class="back">← Tutti i pacchetti</NuxtLink>
        <span class="pill">{{ categoryLabels[pkg.category] ?? pkg.category }}</span>
        <h1>{{ pkg.name }}</h1>
        <p class="lead">{{ pkg.tagline }}</p>
        <div class="facts">
          <div><strong>{{ pkg.duration_weeks }}</strong> settimane</div>
          <div><strong>{{ pkg.sessions_per_week }}×</strong> a settimana</div>
          <div><strong>{{ pkg.mode }}</strong> modalità</div>
          <div><strong>{{ pkg.level }}</strong> livello</div>
        </div>
      </div>
    </section>

    <div class="container body">
      <div class="main">
        <h2>Il programma</h2>
        <p class="lead">{{ pkg.description }}</p>

        <ol v-if="pkg.program?.length" class="timeline">
          <li v-for="(p, i) in pkg.program" :key="i">
            <span class="dot">{{ i + 1 }}</span>
            <div><h3>{{ p.title }}</h3><p class="muted">{{ p.text }}</p></div>
          </li>
        </ol>

        <h2 class="mt">Prossime date</h2>
        <SessionList v-if="data!.sessions.length" :sessions="data!.sessions" compact @changed="refresh" />
        <p v-else class="muted">Le date di questo pacchetto si concordano direttamente con Nicole.</p>
      </div>

      <aside class="buy card">
        <div class="price">{{ euro(pkg.price_cents) }}</div>
        <ul v-if="pkg.features?.length">
          <li v-for="f in pkg.features" :key="f">{{ f }}</li>
        </ul>

        <div v-if="msg" class="alert" :class="msg.ok ? 'alert-ok' : 'alert-err'">{{ msg.text }}</div>

        <template v-if="!loggedIn">
          <NuxtLink :to="{ path: '/registrati', query: { redirect: route.fullPath } }" class="btn btn-pink full">Iscriviti e richiedi</NuxtLink>
          <NuxtLink :to="{ path: '/accedi', query: { redirect: route.fullPath } }" class="btn btn-ghost full">Ho già un account</NuxtLink>
        </template>
        <template v-else-if="user?.role !== 'admin' && !msg?.ok">
          <div class="field">
            <label for="notes">Note per Nicole (facoltativo)</label>
            <textarea id="notes" v-model="notes" rows="3" placeholder="Obiettivi, disponibilità, infortuni…" />
          </div>
          <button class="btn btn-pink full" :disabled="sending" @click="requestPackage">
            {{ sending ? 'Invio…' : 'Richiedi questo pacchetto' }}
          </button>
        </template>
        <p class="small muted">Nessun pagamento online: Nicole conferma la richiesta e il pagamento avviene in studio o tramite bonifico.</p>
      </aside>
    </div>
  </div>
</template>

<style scoped>
.hero { padding: 56px 0 64px; background: var(--pink); border-bottom: 3px solid var(--ink); }
.hero.c-magenta { background: var(--magenta); color: var(--white); }
.hero.c-black { background: var(--ink); color: var(--white); }
.hero.c-black .pill { background: var(--pink); color: var(--ink); }
.hero .lead { color: inherit; opacity: .9; }
.back { display: inline-block; margin-bottom: 24px; font-weight: 700; text-decoration: none; opacity: .8; }
.hero .pill { background: var(--white); color: var(--magenta-dark); margin-bottom: 16px; }
.facts { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 28px; }
.facts div { padding: 12px 18px; border: 2px solid currentColor; border-radius: 16px; font-size: 0.85rem; }
.facts strong { display: block; font: 800 1.3rem/1.1 var(--font-display); }

.body { display: grid; grid-template-columns: 1fr 380px; gap: 48px; padding: 64px 0; align-items: start; }
.mt { margin-top: 56px; }
.timeline { list-style: none; padding: 0; margin: 32px 0 0; display: grid; gap: 4px; }
.timeline li { display: flex; gap: 20px; position: relative; padding-bottom: 20px; }
.timeline li:not(:last-child)::before { content: ''; position: absolute; left: 21px; top: 46px; bottom: 0; width: 3px; background: var(--line); }
.dot { flex: none; display: grid; place-items: center; width: 44px; height: 44px; border-radius: 50%; background: var(--magenta); color: var(--white); font: 800 1.1rem var(--font-display); }
.timeline h3 { margin: 8px 0 4px; }

.buy { position: sticky; top: 100px; box-shadow: 8px 8px 0 var(--magenta); }
.price { font: 800 3rem/1 var(--font-display); margin-bottom: 16px; }
.buy ul { list-style: none; padding: 0; margin: 0 0 24px; display: grid; gap: 10px; }
.buy li { display: flex; gap: 10px; } .buy li::before { content: '✓'; color: var(--magenta); font-weight: 800; }
.full { width: 100%; margin-bottom: 10px; }
.small { font-size: 0.82rem; margin: 12px 0 0; }
@media (max-width: 900px) { .body { grid-template-columns: 1fr; } .buy { position: static; } }
</style>
