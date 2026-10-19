<script setup lang="ts">
defineProps<{ items: string[] }>()
</script>

<template>
  <div class="marquee" aria-hidden="true">
    <div class="track">
      <span v-for="n in 2" :key="n" class="group">
        <span v-for="it in items" :key="it" class="item">{{ it }} <i>✦</i></span>
      </span>
    </div>
  </div>
</template>

<style scoped>
.marquee { background: var(--magenta); color: var(--white); overflow: hidden; transform: rotate(-1.5deg); margin: 0 -10px; border-block: 3px solid var(--ink); }
.track { display: flex; width: max-content; animation: scroll 28s linear infinite; }
.group { display: flex; }
.item { font: 800 1.5rem/1 var(--font-display); text-transform: uppercase; padding: 18px 0 18px 28px; white-space: nowrap; }
i { font-style: normal; color: var(--ink); margin-left: 28px; }
@keyframes scroll { to { transform: translateX(-50%); } }
</style>
