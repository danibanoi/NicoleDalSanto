<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Package;
use App\Models\TrainingSession;
use App\Support\SessionPresenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        return [
            'orders' => $user->orders()->with('package:id,name,slug,color,duration_weeks')->latest()->get(),
            'bookings' => SessionPresenter::collection(
                TrainingSession::whereIn('id', $user->bookings()->pluck('training_session_id'))
                    ->where('starts_at', '>=', now()->startOfDay())
                    ->orderBy('starts_at')->get(),
                $user,
            ),
        ];
    }

    /** Richiesta di acquisto: Nicole la conferma dal pannello dopo il pagamento. */
    public function requestPackage(Request $request)
    {
        $data = $request->validate([
            'package_id' => ['required', 'exists:packages,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $package = Package::where('is_active', true)->findOrFail($data['package_id']);

        $exists = $request->user()->orders()
            ->where('package_id', $package->id)
            ->whereIn('status', ['pending', 'active'])->exists();

        if ($exists) {
            throw ValidationException::withMessages(['package_id' => 'Hai già una richiesta aperta per questo pacchetto.']);
        }

        $order = $request->user()->orders()->create([
            'package_id' => $package->id,
            'price_cents' => $package->price_cents,
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json($order->load('package:id,name,slug'), 201);
    }

    public function book(Request $request, TrainingSession $session)
    {
        $user = $request->user();

        if ($session->starts_at->isPast()) {
            throw ValidationException::withMessages(['session' => 'Questa sessione è già passata.']);
        }

        if (! $user->canBook($session)) {
            throw ValidationException::withMessages(['session' => $user->isActive()
                ? 'Ti serve un pacchetto attivo per prenotare questa sessione.'
                : 'Il tuo account è in attesa di attivazione da parte di Nicole.']);
        }

        DB::transaction(function () use ($user, $session) {
            $count = Booking::where('training_session_id', $session->id)->lockForUpdate()->count();

            if ($count >= $session->capacity) {
                throw ValidationException::withMessages(['session' => 'Posti esauriti, scegli un altro orario.']);
            }

            Booking::firstOrCreate(['user_id' => $user->id, 'training_session_id' => $session->id]);
        });

        return response()->json(['message' => 'Prenotazione confermata!'], 201);
    }

    public function cancel(Request $request, TrainingSession $session)
    {
        if ($session->starts_at->lt(now()->addHours(24))) {
            throw ValidationException::withMessages(['session' => 'Puoi disdire fino a 24 ore prima. Scrivi a Nicole.']);
        }

        $request->user()->bookings()->where('training_session_id', $session->id)->delete();

        return response()->noContent();
    }
}
