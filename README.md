# NDS Personal Trainer · Nicole Dal Santo

Sito con pacchetti di allenamento, calendario delle date, area clienti e pannello per Nicole.

```
backend/   Laravel 13 – solo API (SQLite, Sanctum token)
frontend/  Nuxt 4 – sito pubblico, area riservata, pannello admin
```

## Avvio in locale

```bash
# API → http://localhost:8000/api
cd backend
php artisan migrate:fresh --seed   # dati demo (pacchetti, date, utenti)
php artisan serve --port=8000

# Sito → http://localhost:3000
cd frontend
npm install
npm run dev
```

L'URL delle API si cambia con `NUXT_PUBLIC_API_BASE` (default `http://localhost:8000/api`).

### Account demo
| Ruolo   | Email                          | Password      |
|---------|--------------------------------|---------------|
| Nicole  | nicole@ndspersonaltrainer.com  | `nicole2026!` |
| Cliente | cliente@example.com            | `password`    |

## Come funziona
1. Chiunque vede **pacchetti**, **programmi** e **calendario**.
2. La cliente si **iscrive** (account "da attivare") e **richiede un pacchetto**.
3. Nicole, dal pannello `/admin`, **conferma il pagamento** → pacchetto e account diventano attivi.
4. La cliente **prenota le date** del suo programma (posti limitati, disdetta fino a 24h prima).

Nicole dal pannello gestisce: richieste, clienti, calendario (anche sessioni ricorrenti), pacchetti e messaggi dal form contatti.

## API principali
| Metodo | Endpoint | |
|---|---|---|
| GET | `/api/packages`, `/api/packages/{slug}` | pubblico |
| GET | `/api/sessions?from=&to=&package=` | pubblico |
| POST | `/api/contact` | pubblico |
| POST | `/api/auth/register`, `/api/auth/login` | |
| GET | `/api/me/dashboard` | cliente |
| POST | `/api/me/orders` | cliente – richiesta pacchetto |
| POST/DELETE | `/api/sessions/{id}/book` | cliente – prenota/disdici |
| * | `/api/admin/...` | solo Nicole |
