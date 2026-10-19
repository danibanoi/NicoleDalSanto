<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Package;
use App\Models\TrainingSession;
use App\Support\SessionPresenter;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function packages()
    {
        return Package::where('is_active', true)->orderBy('sort')->get();
    }

    public function package(string $slug)
    {
        $package = Package::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return [
            'package' => $package,
            'sessions' => SessionPresenter::collection(
                TrainingSession::where('package_id', $package->id)
                    ->where('starts_at', '>=', now())
                    ->orderBy('starts_at')->limit(12)->get(),
                auth('sanctum')->user(),
            ),
        ];
    }

    /** Calendario pubblico: ?from=YYYY-MM-DD&to=YYYY-MM-DD&package=slug */
    public function sessions(Request $request)
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'package' => ['nullable', 'string'],
        ]);

        $from = $request->date('from') ?? now()->startOfDay();
        $to = $request->date('to') ?? $from->copy()->addWeeks(4);

        $query = TrainingSession::whereBetween('starts_at', [$from, $to->endOfDay()])->orderBy('starts_at');

        if ($slug = $request->string('package')->toString()) {
            $query->whereHas('package', fn ($q) => $q->where('slug', $slug));
        }

        return SessionPresenter::collection($query->get(), auth('sanctum')->user());
    }

    public function contact(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:30'],
            'goal' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        ContactMessage::create($data);

        return response()->json(['message' => 'Messaggio inviato! Nicole ti risponderà a breve.'], 201);
    }
}
