<script setup lang="ts">
definePageMeta({ layout: 'admin', middleware: 'admin' })
useHead({ title: 'Messaggi · NDS' })

const api = useApi()
const { data: messages, refresh } = await useAsyncData('admin-messages', () => api<any[]>('/admin/messages'))

async function markRead(m: any) {
  await api(`/admin/messages/${m.id}/read`, { method: 'PATCH' })
  await refresh()
}
</script>

<template>
  <div>
    <h1>Messaggi</h1>
    <p v-if="!messages?.length" class="muted">Nessun messaggio ricevuto.</p>
    <article v-for="m in messages" :key="m.id" class="card msg" :class="{ unread: !m.is_read }">
      <div class="top">
        <div>
          <strong>{{ m.name }}</strong> · <a :href="`mailto:${m.email}`">{{ m.email }}</a>
          <template v-if="m.phone"> · <a :href="`tel:${m.phone}`">{{ m.phone }}</a></template>
          <span v-if="m.goal" class="pill">{{ m.goal }}</span>
        </div>
        <small class="muted">{{ fmtDate(m.created_at) }}</small>
      </div>
      <p>{{ m.message }}</p>
      <button v-if="!m.is_read" class="btn btn-sm btn-ghost" @click="markRead(m)">Segna come letto</button>
    </article>
  </div>
</template>

<style scoped>
.msg { margin-top: 16px; padding: 22px; }
.msg.unread { border-color: var(--magenta); box-shadow: 5px 5px 0 var(--pink); }
.top { display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 10px; }
.top a { color: var(--magenta); }
.pill { margin-left: 8px; }
p { white-space: pre-line; }
</style>
