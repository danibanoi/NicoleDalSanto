<script setup lang="ts">
definePageMeta({ middleware: 'guest' })
useHead({ title: 'Iscriviti · NDS Personal Trainer' })

const { register } = useAuth()
const route = useRoute()
const form = reactive({ name: '', email: '', phone: '', password: '', password_confirmation: '' })
const errors = ref<Record<string, string>>({})
const loading = ref(false)

async function submit() {
  loading.value = true
  errors.value = {}
  try {
    await register(form)
    const redirect = typeof route.query.redirect === 'string' ? route.query.redirect : null
    await navigateTo(redirect ?? '/area-riservata')
  } catch (e) {
    errors.value = apiErrors(e).fields
    if (!Object.keys(errors.value).length) errors.value = { name: apiErrors(e).message }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <AuthShell title="Iscriviti" subtitle="Crea il tuo account: Nicole lo attiverà e potrai prenotare i tuoi allenamenti.">
    <form @submit.prevent="submit">
      <div class="field">
        <label for="name">Nome e cognome</label>
        <input id="name" v-model="form.name" required autocomplete="name">
        <span v-if="errors.name" class="error">{{ errors.name }}</span>
      </div>
      <div class="field">
        <label for="email">Email</label>
        <input id="email" v-model="form.email" type="email" required autocomplete="email">
        <span v-if="errors.email" class="error">{{ errors.email }}</span>
      </div>
      <div class="field">
        <label for="phone">Telefono</label>
        <input id="phone" v-model="form.phone" type="tel" autocomplete="tel">
      </div>
      <div class="field">
        <label for="password">Password (min. 8 caratteri)</label>
        <input id="password" v-model="form.password" type="password" required minlength="8" autocomplete="new-password">
        <span v-if="errors.password" class="error">{{ errors.password }}</span>
      </div>
      <div class="field">
        <label for="password2">Conferma password</label>
        <input id="password2" v-model="form.password_confirmation" type="password" required autocomplete="new-password">
      </div>
      <button class="btn btn-pink full" :disabled="loading">{{ loading ? 'Creazione…' : 'Crea account' }}</button>
      <p class="alt">Hai già un account? <NuxtLink :to="{ path: '/accedi', query: route.query }">Accedi</NuxtLink></p>
    </form>
  </AuthShell>
</template>
