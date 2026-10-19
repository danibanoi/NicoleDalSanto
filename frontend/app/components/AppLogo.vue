<template>
  <NuxtLink to="/" class="logo" aria-label="NDS Personal Trainer – home">
    <span class="mark">N<span class="d">D</span>S</span>
    <span class="txt">Nicole Dal Santo<small>Personal Trainer</small></span>
  </NuxtLink>
</template>

<style scoped>
.logo { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; }
.mark {
  white-space: nowrap;
  display: flex; align-items: center; justify-content: center;
  width: 46px; height: 46px; border-radius: 50%;
  background: var(--ink); color: var(--white);
  font: 800 0.95rem/1 var(--font-display); letter-spacing: -0.04em;
}
.d { color: var(--magenta); }
.txt { display: grid; font: 800 1.05rem/1.05 var(--font-display); }
.txt small { font: 700 0.65rem/1.4 var(--font-body); letter-spacing: .2em; text-transform: uppercase; color: var(--magenta); }
</style>
