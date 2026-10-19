const TZ = 'Europe/Rome'

export const euro = (cents: number) =>
  new Intl.NumberFormat('it-IT', { style: 'currency', currency: 'EUR', maximumFractionDigits: cents % 100 ? 2 : 0 }).format(cents / 100)

export const fmtDay = (iso: string) =>
  new Intl.DateTimeFormat('it-IT', { weekday: 'long', day: 'numeric', month: 'long', timeZone: TZ }).format(new Date(iso))

export const fmtShortDay = (iso: string) =>
  new Intl.DateTimeFormat('it-IT', { weekday: 'short', day: 'numeric', month: 'short', timeZone: TZ }).format(new Date(iso))

export const fmtTime = (iso: string) =>
  new Intl.DateTimeFormat('it-IT', { hour: '2-digit', minute: '2-digit', timeZone: TZ }).format(new Date(iso))

export const fmtDate = (iso: string | null) =>
  iso ? new Intl.DateTimeFormat('it-IT', { day: '2-digit', month: '2-digit', year: 'numeric', timeZone: TZ }).format(new Date(iso)) : '—'

/** Chiave YYYY-MM-DD nel fuso di Roma, per raggruppare le sessioni per giorno. */
export const dayKey = (iso: string) =>
  new Intl.DateTimeFormat('en-CA', { timeZone: TZ }).format(new Date(iso))

export const categoryLabels: Record<string, string> = {
  glutei: 'Glutei',
  completo: 'Completo',
  tonificazione: 'Tonificazione',
  gravidanza: 'Gravidanza',
  online: 'Online',
  personal: 'Personal 1:1',
}

export const orderStatus: Record<string, { label: string; cls: string }> = {
  pending: { label: 'In attesa', cls: 'pill-warn' },
  active: { label: 'Attivo', cls: 'pill-ok' },
  completed: { label: 'Concluso', cls: 'pill-off' },
  cancelled: { label: 'Annullato', cls: 'pill-off' },
}

export const userStatus: Record<string, { label: string; cls: string }> = {
  pending: { label: 'Da attivare', cls: 'pill-warn' },
  active: { label: 'Attiva', cls: 'pill-ok' },
  disabled: { label: 'Disattivata', cls: 'pill-off' },
}

/** ISO → valore per <input type="datetime-local"> nel fuso di Roma. */
export const toLocalInput = (iso: string) =>
  new Intl.DateTimeFormat('sv-SE', { timeZone: TZ, year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' })
    .format(new Date(iso)).replace(' ', 'T')
