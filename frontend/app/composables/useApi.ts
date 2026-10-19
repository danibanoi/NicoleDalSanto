type FetchOptions = NonNullable<Parameters<typeof $fetch>[1]>

export const useToken = () =>
  useCookie<string | null>('nds_token', { maxAge: 60 * 60 * 24 * 30, sameSite: 'lax' })

/** $fetch verso le API Laravel, con token Sanctum se presente. */
export function useApi() {
  const { apiBase } = useRuntimeConfig().public
  const token = useToken()

  return <T = any>(url: string, opts: FetchOptions = {}) =>
    $fetch<T>(url, {
      baseURL: apiBase,
      ...opts,
      headers: {
        Accept: 'application/json',
        ...(token.value ? { Authorization: `Bearer ${token.value}` } : {}),
        ...(opts.headers as Record<string, string> | undefined),
      },
    } as any) as Promise<T>
}

/** Estrae i messaggi di validazione Laravel ({ errors: { campo: [..] } }). */
export function apiErrors(e: any): { message: string; fields: Record<string, string> } {
  const data = e?.data ?? {}
  const fields: Record<string, string> = {}
  for (const [k, v] of Object.entries(data.errors ?? {})) fields[k] = (v as string[])[0]
  const first = Object.values(fields)[0]
  return { message: first || data.message || 'Qualcosa è andato storto, riprova.', fields }
}
