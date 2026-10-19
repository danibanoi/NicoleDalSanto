<?php

namespace App\Support;

use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Support\Collection;

/** Serializza le sessioni con posti liberi e stato di prenotazione per l'utente corrente. */
class SessionPresenter
{
    public static function collection(Collection $sessions, ?User $user = null): array
    {
        $sessions->loadMissing('package:id,name,slug,color')->loadCount('bookings');

        $booked = $user
            ? $user->bookings()->whereIn('training_session_id', $sessions->pluck('id'))->pluck('training_session_id')->all()
            : [];

        return $sessions->map(fn (TrainingSession $s) => [
            'id' => $s->id,
            'title' => $s->title,
            'description' => $s->description,
            'starts_at' => $s->starts_at->toIso8601String(),
            'ends_at' => $s->ends_at->toIso8601String(),
            'location' => $s->location,
            'capacity' => $s->capacity,
            'spots_left' => max(0, $s->capacity - $s->bookings_count),
            'package' => $s->package?->only(['id', 'name', 'slug', 'color']),
            'is_booked' => in_array($s->id, $booked, true),
            'can_book' => $user ? $user->canBook($s) : false,
        ])->values()->all();
    }
}
