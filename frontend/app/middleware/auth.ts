export default defineNuxtRouteMiddleware((to) => {
  if (!useAuth().loggedIn.value) return navigateTo({ path: '/accedi', query: { redirect: to.fullPath } })
})
