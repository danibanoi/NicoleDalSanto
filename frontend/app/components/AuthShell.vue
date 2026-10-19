<script setup lang="ts">
defineProps<{ title: string; subtitle: string }>()
</script>

<template>
  <div class="container shell">
    <div class="side">
      <img src="/img/nicole.jpg" alt="" class="photo">
      <p class="quote">“L'allenamento deve essere costruito sulla persona, non il contrario.”</p>
    </div>
    <div class="card box">
      <h1>{{ title }}</h1>
      <p class="muted">{{ subtitle }}</p>
      <slot />
    </div>
  </div>
</template>

<style scoped>
.shell { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: center; padding: 56px 0 80px; }
.photo { border-radius: var(--radius); border: 3px solid var(--ink); aspect-ratio: 1; object-fit: cover; transform: rotate(-2deg); }
.quote { font: 700 1.4rem/1.3 var(--font-display); margin-top: 28px; color: var(--magenta); }
.box { box-shadow: 10px 10px 0 var(--magenta); }
.box h1 { font-size: 2.8rem; }
.box :deep(.full) { width: 100%; margin-top: 8px; }
.box :deep(.alt) { margin-top: 18px; text-align: center; font-size: 0.95rem; }
.box :deep(.alt a) { color: var(--magenta); font-weight: 700; }
@media (max-width: 900px) { .shell { grid-template-columns: 1fr; } .side { display: none; } }
</style>
