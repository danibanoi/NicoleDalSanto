<script setup lang="ts">
useHead({ title: 'Contatti · NDS Personal Trainer' })
const { instagram } = useRuntimeConfig().public
const api = useApi()

const form = reactive({ name: '', email: '', phone: '', goal: '', message: '' })
const errors = ref<Record<string, string>>({})
const status = ref<{ ok: boolean; text: string } | null>(null)
const sending = ref(false)
const goals = ['Dimagrimento', 'Tonificazione', 'Glutei', 'Gravidanza / post-parto', 'Postura e benessere', 'Online coaching', 'Altro']

async function submit() {
  sending.value = true
  errors.value = {}
  status.value = null
  try {
    const res = await api<{ message: string }>('/contact', { method: 'POST', body: form })
    status.value = { ok: true, text: res.message }
    Object.assign(form, { name: '', email: '', phone: '', goal: '', message: '' })
  } catch (e) {
    const { message, fields } = apiErrors(e)
    errors.value = fields
    status.value = { ok: false, text: message }
  } finally {
    sending.value = false
  }
}
</script>

<template>
  <div class="container section wrap">
    <div>
      <span class="eyebrow">Contatti</span>
      <h1>Parliamone.</h1>
      <p class="lead">Prenota la tua consulenza: raccontami i tuoi obiettivi e costruiamo insieme il percorso giusto per te.</p>
      <div class="info">
        <div><span>📍</span><div><strong>Studio privato</strong><br>Pescantina (VR)</div></div>
        <div><span>📸</span><div><strong>Instagram</strong><br><a :href="instagram" target="_blank" rel="noopener">@nds_personaltrainer</a></div></div>
      </div>
    </div>

    <form class="card form" @submit.prevent="submit">
      <div v-if="status" class="alert" :class="status.ok ? 'alert-ok' : 'alert-err'">{{ status.text }}</div>
      <div class="two">
        <div class="field">
          <label for="name">Nome *</label>
          <input id="name" v-model="form.name" required autocomplete="name">
          <span v-if="errors.name" class="error">{{ errors.name }}</span>
        </div>
        <div class="field">
          <label for="phone">Telefono</label>
          <input id="phone" v-model="form.phone" type="tel" autocomplete="tel">
        </div>
      </div>
      <div class="field">
        <label for="email">Email *</label>
        <input id="email" v-model="form.email" type="email" required autocomplete="email">
        <span v-if="errors.email" class="error">{{ errors.email }}</span>
      </div>
      <div class="field">
        <label for="goal">Il tuo obiettivo</label>
        <select id="goal" v-model="form.goal">
          <option value="">Scegli…</option>
          <option v-for="g in goals" :key="g">{{ g }}</option>
        </select>
      </div>
      <div class="field">
        <label for="message">Messaggio *</label>
        <textarea id="message" v-model="form.message" rows="5" required />
        <span v-if="errors.message" class="error">{{ errors.message }}</span>
      </div>
      <button class="btn btn-pink" :disabled="sending">{{ sending ? 'Invio…' : 'Invia messaggio' }}</button>
    </form>
  </div>
</template>

<style scoped>
.wrap { display: grid; grid-template-columns: 1fr 1.1fr; gap: 56px; align-items: start; }
.info { display: grid; gap: 16px; margin-top: 32px; }
.info > div { display: flex; gap: 16px; align-items: center; padding: 18px; background: var(--white); border-radius: 18px; border: 1px solid var(--line); }
.info span { font-size: 1.6rem; }
.info a { color: var(--magenta); font-weight: 700; }
.form { box-shadow: 10px 10px 0 var(--pink); }
.two { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media (max-width: 900px) { .wrap, .two { grid-template-columns: 1fr; } }
</style>
