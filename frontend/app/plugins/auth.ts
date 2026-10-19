export default defineNuxtPlugin(async () => {
  const { user, fetchMe } = useAuth()
  if (!user.value) await fetchMe()
})
