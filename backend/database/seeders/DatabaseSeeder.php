<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Package;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Nicole Dal Santo',
            'email' => 'nicole@ndspersonaltrainer.com',
            'password' => 'nicole2026!',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $packages = collect($this->packages())->map(fn ($p, $i) => Package::create([...$p, 'sort' => $i]))->keyBy('slug');

        $this->seedSessions($packages);

        // Cliente demo con un pacchetto attivo
        $client = User::create([
            'name' => 'Giulia Demo',
            'email' => 'cliente@example.com',
            'phone' => '333 000 0000',
            'password' => 'password',
            'status' => 'active',
        ]);
        Order::create([
            'user_id' => $client->id,
            'package_id' => $packages['glute-lab']->id,
            'price_cents' => $packages['glute-lab']->price_cents,
            'status' => 'active',
            'starts_on' => today(),
            'ends_on' => today()->addWeeks(8),
        ]);
    }

    private function seedSessions($packages): void
    {
        // [slug, giorno ISO (1=lun), ora, minuti durata, titolo, luogo, posti]
        $weekly = [
            ['glute-lab', 1, '18:30', 60, 'Glute Lab', 'Studio Pescantina', 6],
            ['glute-lab', 3, '18:30', 60, 'Glute Lab', 'Studio Pescantina', 6],
            ['total-body', 2, '19:00', 60, 'Total Body', 'Studio Pescantina', 6],
            ['total-body', 4, '19:00', 60, 'Total Body', 'Studio Pescantina', 6],
            ['total-body', 6, '09:30', 60, 'Total Body Weekend', 'Studio Pescantina', 6],
            ['tonifica', 6, '11:00', 50, 'Tonifica & Definisci', 'Studio Pescantina', 5],
            ['mamma-fit', 5, '10:00', 50, 'Mamma Fit', 'Studio Pescantina', 4],
            ['online-coaching', 3, '20:30', 45, 'Live Online Workout', 'Online · Zoom', 20],
        ];

        $monday = now()->startOfWeek();

        foreach (range(0, 5) as $week) {
            foreach ($weekly as [$slug, $day, $time, $minutes, $title, $location, $capacity]) {
                [$h, $m] = explode(':', $time);
                $start = $monday->copy()->addWeeks($week)->addDays($day - 1)->setTime((int) $h, (int) $m);

                if ($start->isPast()) {
                    continue;
                }

                TrainingSession::create([
                    'package_id' => $packages[$slug]->id,
                    'title' => $title,
                    'starts_at' => $start,
                    'ends_at' => $start->copy()->addMinutes($minutes),
                    'location' => $location,
                    'capacity' => $capacity,
                ]);
            }
        }
    }

    private function packages(): array
    {
        return [
            [
                'name' => 'Glute Lab',
                'slug' => 'glute-lab',
                'category' => 'glutei',
                'tagline' => 'Glutei forti, tonici e funzionali.',
                'description' => "Un programma di 8 settimane dedicato a glutei e catena posteriore. Lavoro progressivo con carichi, elastici e attivazione, per costruire forma e forza senza trascurare postura e schiena.",
                'price_cents' => 29000,
                'duration_weeks' => 8,
                'sessions_per_week' => 2,
                'level' => 'Principiante → Intermedio',
                'mode' => 'Studio',
                'features' => ['16 allenamenti in small group (max 6)', 'Valutazione iniziale e finale', 'Scheda extra da fare a casa', 'Supporto su WhatsApp'],
                'program' => [
                    ['title' => 'Settimane 1–2 · Attivazione', 'text' => 'Tecnica di hip thrust, squat e stacco. Attivazione del gluteo medio e mobilità dell\'anca.'],
                    ['title' => 'Settimane 3–5 · Costruzione', 'text' => 'Aumento progressivo dei carichi, lavoro unilaterale e tempo sotto tensione.'],
                    ['title' => 'Settimane 6–8 · Intensità', 'text' => 'Superserie, metabolic finisher e test finale per misurare i progressi.'],
                ],
                'color' => 'pink',
                'is_featured' => false,
            ],
            [
                'name' => 'Total Body',
                'slug' => 'total-body',
                'category' => 'completo',
                'tagline' => 'Il percorso completo: forza, forma e sicurezza.',
                'description' => "Il programma completo di 12 settimane per lavorare su tutto il corpo. Forza, tonicità, resistenza e postura: costruito su di te dopo un'analisi iniziale di obiettivi, abitudini e stile di vita.",
                'price_cents' => 59000,
                'duration_weeks' => 12,
                'sessions_per_week' => 3,
                'level' => 'Tutti i livelli',
                'mode' => 'Studio + Online',
                'features' => ['36 allenamenti in studio', 'Analisi posturale e composizione corporea', 'Programma personalizzato aggiornato ogni 4 settimane', 'Consigli sulle abitudini alimentari', 'Supporto continuo su WhatsApp'],
                'program' => [
                    ['title' => 'Fase 1 · Analisi', 'text' => 'Obiettivi, abitudini, eventuali problematiche e stile di vita. Test di partenza.'],
                    ['title' => 'Fase 2 · Programmazione', 'text' => 'Allenamento costruito su di te: forza, condizionamento e mobilità.'],
                    ['title' => 'Fase 3 · Evoluzione', 'text' => 'Monitoraggio dei progressi e adattamento del piano nel tempo.'],
                ],
                'color' => 'magenta',
                'is_featured' => true,
            ],
            [
                'name' => 'Tonifica & Definisci',
                'slug' => 'tonifica',
                'category' => 'tonificazione',
                'tagline' => 'Più definizione, più energia.',
                'description' => 'Circuiti di forza e condizionamento per ridurre la massa grassa e migliorare la composizione corporea, mantenendo il focus sulla salute.',
                'price_cents' => 32000,
                'duration_weeks' => 8,
                'sessions_per_week' => 2,
                'level' => 'Intermedio',
                'mode' => 'Studio',
                'features' => ['16 allenamenti in small group', 'Misurazioni ogni 4 settimane', 'Workout bonus per il weekend'],
                'program' => [
                    ['title' => 'Settimane 1–4', 'text' => 'Circuiti full body e basi di forza.'],
                    ['title' => 'Settimane 5–8', 'text' => 'Intervalli ad alta intensità e progressione dei carichi.'],
                ],
                'color' => 'black',
                'is_featured' => false,
            ],
            [
                'name' => 'Mamma Fit',
                'slug' => 'mamma-fit',
                'category' => 'gravidanza',
                'tagline' => 'Allenarsi in sicurezza, prima e dopo il parto.',
                'description' => 'Percorso pensato per la gravidanza e il post-parto: mobilità, pavimento pelvico, respirazione e forza, sempre in sicurezza.',
                'price_cents' => 28000,
                'duration_weeks' => 8,
                'sessions_per_week' => 1,
                'level' => 'Tutti i livelli',
                'mode' => 'Studio',
                'features' => ['8 sessioni in piccolo gruppo (max 4)', 'Lavoro su respirazione e core', 'Esercizi da fare a casa'],
                'program' => [
                    ['title' => 'Mobilità & respiro', 'text' => 'Consapevolezza del core e del pavimento pelvico.'],
                    ['title' => 'Forza dolce', 'text' => 'Esercizi a basso impatto per mantenere tono ed energia.'],
                ],
                'color' => 'pink',
                'is_featured' => false,
            ],
            [
                'name' => 'Online Coaching',
                'slug' => 'online-coaching',
                'category' => 'online',
                'tagline' => 'Allenati ovunque, con Nicole al tuo fianco.',
                'description' => 'Programma mensile personalizzato da seguire a casa o in palestra, con video degli esercizi, check settimanale e live workout del mercoledì.',
                'price_cents' => 7900,
                'duration_weeks' => 4,
                'sessions_per_week' => 3,
                'level' => 'Tutti i livelli',
                'mode' => 'Online',
                'features' => ['Scheda personalizzata mensile', 'Video tutorial degli esercizi', 'Check settimanale', 'Live workout ogni mercoledì'],
                'program' => [
                    ['title' => 'Ogni mese', 'text' => 'Nuova scheda in base ai progressi e ai tuoi feedback.'],
                ],
                'color' => 'black',
                'is_featured' => false,
            ],
            [
                'name' => 'Personal Training · 5 lezioni',
                'slug' => 'pt-5',
                'category' => 'personal',
                'tagline' => 'Lezioni 1:1 in studio privato.',
                'description' => 'Allenamento individuale costruito sui tuoi obiettivi, in studio privato a Pescantina. Orari concordati direttamente con Nicole.',
                'price_cents' => 21000,
                'duration_weeks' => 8,
                'sessions_per_week' => 1,
                'level' => 'Tutti i livelli',
                'mode' => 'Studio',
                'features' => ['5 lezioni individuali', '€42 a lezione', 'Valido 8 settimane'],
                'program' => null,
                'color' => 'pink',
                'is_featured' => false,
            ],
            [
                'name' => 'Personal Training · 10 lezioni',
                'slug' => 'pt-10',
                'category' => 'personal',
                'tagline' => 'Il pacchetto più conveniente.',
                'description' => 'Dieci lezioni individuali per costruire un percorso solido e continuativo, con programma su misura.',
                'price_cents' => 35000,
                'duration_weeks' => 12,
                'sessions_per_week' => 1,
                'level' => 'Tutti i livelli',
                'mode' => 'Studio',
                'features' => ['10 lezioni individuali', '€35 a lezione', 'Valido 12 settimane'],
                'program' => null,
                'color' => 'magenta',
                'is_featured' => false,
            ],
        ];
    }
}
