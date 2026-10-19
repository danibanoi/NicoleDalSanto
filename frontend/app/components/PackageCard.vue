<script setup lang="ts">
import type { Package } from '~/types'
const props = defineProps<{ pkg: Package }>()
const perSession = computed(() => {
  const total = props.pkg.duration_weeks * props.pkg.sessions_per_week
  return props.pkg.category === 'personal' ? null : `${total} allenamenti · ${props.pkg.duration_weeks} settimane`
})
</script>

<template>
  <NuxtLink :to="`/pacchetti/${pkg.slug}`" class="pkg" :class="[`c-${pkg.color}`, { featured: pkg.is_featured }]">
    <span v-if="pkg.is_featured" class="badge">Il più scelto ★</span>
    <div class="top">
      <span class="pill">{{ categoryLabels[pkg.category] ?? pkg.category }}</span>
      <span class="mode">{{ pkg.mode }}</span>
    </div>
    <h3>{{ pkg.name }}</h3>
    <p class="tag">{{ pkg.tagline }}</p>
    <ul v-if="pkg.features?.length">
      <li v-for="f in pkg.features.slice(0, 3)" :key="f">{{ f }}</li>
    </ul>
    <div class="foot">
      <div>
        <div class="price">{{ euro(pkg.price_cents) }}</div>
        <div class="meta">{{ perSession ?? pkg.level }}</div>
      </div>
      <span class="arrow" aria-hidden="true">→</span>
    </div>
  </NuxtLink>
</template>

<style scoped>
.pkg {
  position: relative; display: flex; flex-direction: column;
  padding: 28px; border-radius: var(--radius); border: 2px solid var(--ink);
  background: var(--white); color: var(--ink); text-decoration: none;
  transition: transform .25s ease, box-shadow .25s ease;
}
.pkg:hover { transform: translateY(-6px) rotate(-0.6deg); box-shadow: 8px 8px 0 var(--ink); }
.c-magenta { background: var(--magenta); color: var(--white); }
.c-magenta .pill { background: var(--white); }
.c-black { background: var(--ink); color: var(--white); }
.c-black .pill { background: var(--pink); color: var(--ink); }
.c-black:hover { box-shadow: 8px 8px 0 var(--magenta); }
.c-pink { background: var(--pink-soft); }
.c-pink .pill { background: var(--white); }
.badge {
  position: absolute; top: -14px; right: 20px;
  background: var(--ink); color: var(--white); border: 2px solid var(--white);
  padding: 6px 14px; border-radius: 999px; font-weight: 700; font-size: 0.8rem; transform: rotate(3deg);
}
.top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; }
.mode { font-size: 0.8rem; font-weight: 700; opacity: .7; }
h3 { font-size: 1.8rem; margin-bottom: 6px; }
.tag { opacity: .85; }
ul { list-style: none; padding: 0; margin: 4px 0 24px; display: grid; gap: 8px; font-size: 0.95rem; }
li { display: flex; gap: 10px; }
li::before { content: '✓'; font-weight: 800; color: var(--magenta); }
.c-magenta li::before { color: var(--ink); }
.foot { margin-top: auto; display: flex; align-items: end; justify-content: space-between; padding-top: 18px; border-top: 2px dashed currentColor; }
.price { font: 800 2.2rem/1 var(--font-display); }
.meta { font-size: 0.85rem; opacity: .75; margin-top: 4px; }
.arrow {
  display: grid; place-items: center; width: 48px; height: 48px; border-radius: 50%;
  background: var(--ink); color: var(--white); font-size: 1.3rem; transition: transform .25s;
}
.c-black .arrow, .c-magenta .arrow { background: var(--white); color: var(--ink); }
.pkg:hover .arrow { transform: rotate(-45deg); }
</style>
