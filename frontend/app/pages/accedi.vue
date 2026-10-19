<script setup lang="ts">
definePageMeta({ middleware: 'guest' })
useHead({ title: 'Accedi · NDS Personal Trainer' })

const { login } = useAuth()
const route = useRoute()
const form = reactive({ email: '', password: '' })
const error = ref('')
const loading = ref(false)

async function submit() {
  loading.value = true
  error.value = ''
  try {
    const u = await login(form.email, form.password)
    const redirect = typeof route.query.redirect === 'string' ? route.query.redirect : null
    await navigateTo(redirect ?? (u.role === 'admin' ? '/admin' : '/area-riservata'))
  } catch (e) {
    error.value = apiErrors(e).message
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <AuthShell title="Bentornata!" subtitle="Accedi per vedere i tuoi pacchetti e prenotare le date.">
    <form @submit.prevent="submit">
      <div v-if="error" class="alert alert-err">{{ error }}</div>
      <div class="field">
        <label for="email">Email</label>
        <input id="email" v-model="form.email" type="email" required autocomplete="email">
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input id="password" v-model="form.password" type="password" required autocomplete="current-password">
      </div>
      <button class="btn btn-pink full" :disabled="loading">{{ loading ? 'Accesso…' : 'Accedi' }}</button>
      <p class="alt">Non hai un account? <NuxtLink :to="{ path: '/registrati', query: route.query }">Iscriviti</NuxtLink></p>
    </form>
  </AuthShell>
</template>
