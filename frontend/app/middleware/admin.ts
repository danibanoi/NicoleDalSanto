export default defineNuxtRouteMiddleware(() => {
  const { loggedIn, isAdmin } = useAuth()
  if (!loggedIn.value) return navigateTo('/accedi')
  if (!isAdmin.value) return navigateTo('/area-riservata')
})
