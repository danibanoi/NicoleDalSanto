export interface User {
  id: number
  name: string
  email: string
  phone: string | null
  role: 'client' | 'admin'
  status: 'pending' | 'active' | 'disabled'
}

export function useAuth() {
  const user = useState<User | null>('auth:user', () => null)
  const token = useToken()
  const api = useApi()

  async function fetchMe() {
    if (!token.value) return (user.value = null)
    try {
      user.value = (await api<{ user: User }>('/auth/me')).user
    } catch {
      token.value = null
      user.value = null
    }
    return user.value
  }

  async function login(email: string, password: string) {
    const res = await api<{ token: string; user: User }>('/auth/login', { method: 'POST', body: { email, password } })
    token.value = res.token
    user.value = res.user
    return res.user
  }

  async function register(body: Record<string, string>) {
    const res = await api<{ token: string; user: User }>('/auth/register', { method: 'POST', body })
    token.value = res.token
    user.value = res.user
    return res.user
  }

  async function logout() {
    try { await api('/auth/logout', { method: 'POST' }) } catch {}
    token.value = null
    user.value = null
    await navigateTo('/')
  }

  return {
    user,
    loggedIn: computed(() => !!user.value),
    isAdmin: computed(() => user.value?.role === 'admin'),
    fetchMe, login, register, logout,
  }
}
