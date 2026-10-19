export default defineNuxtRouteMiddleware(() => {
  const { loggedIn, isAdmin } = useAuth()
  if (loggedIn.value) return navigateTo(isAdmin.value ? '/admin' : '/area-riservata')
})
