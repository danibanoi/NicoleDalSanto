<script setup lang="ts">
import type { Package } from '~/types'

useHead({ title: 'Pacchetti di allenamento · NDS Personal Trainer' })

const api = useApi()
const { data: packages } = await useAsyncData('packages', () => api<Package[]>('/packages'))

const filter = ref('tutti')
const categories = computed(() => ['tutti', ...new Set((packages.value ?? []).map((p) => p.category))])
const visible = computed(() =>
  (packages.value ?? []).filter((p) => filter.value === 'tutti' || p.category === filter.value),
)
</script>

<template>
  <div class="container section">
    <span class="eyebrow">Pacchetti</span>
    <h1>Il tuo percorso,<br><span class="hl">su misura.</span></h1>
    <p class="lead">Programmi a tema in small group, coaching online o lezioni individuali. Scegli, iscriviti e richiedi il pacchetto: Nicole lo attiva e potrai prenotare le tue date.</p>

    <div class="filters">
      <button v-for="c in categories" :key="c" :class="{ on: filter === c }" @click="filter = c">
        {{ c === 'tutti' ? 'Tutti' : categoryLabels[c] ?? c }}
      </button>
    </div>

    <TransitionGroup name="fade" tag="div" class="grid-3">
      <PackageCard v-for="p in visible" :key="p.id" :pkg="p" />
    </TransitionGroup>

    <div class="how card">
      <h3>Come funziona</h3>
      <ol>
        <li><strong>Iscriviti</strong> al sito con email e password.</li>
        <li><strong>Richiedi</strong> il pacchetto che fa per te.</li>
        <li><strong>Nicole conferma</strong> il pacchetto (pagamento in studio o bonifico).</li>
        <li><strong>Prenota</strong> le tue date dal calendario.</li>
      </ol>
    </div>
  </div>
</template>

<style scoped>
.filters { display: flex; flex-wrap: wrap; gap: 8px; margin: 32px 0 40px; }
.filters button {
  padding: 10px 18px; border-radius: 999px; border: 2px solid var(--ink);
  background: var(--white); font: 700 0.9rem var(--font-body); cursor: pointer; transition: all .15s;
}
.filters button.on, .filters button:hover { background: var(--ink); color: var(--white); }
.how { margin-top: 56px; background: var(--pink-soft); }
.how ol { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; padding-left: 20px; margin: 0; }
</style>
