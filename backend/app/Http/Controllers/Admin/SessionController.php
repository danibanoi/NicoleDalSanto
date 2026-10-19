<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrainingSession;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->date('from') ?? now()->startOfWeek();
        $to = $request->date('to') ?? $from->copy()->addWeeks(6);

        return TrainingSession::with(['package:id,name,slug,color', 'bookings.user:id,name,email,phone'])
            ->whereBetween('starts_at', [$from, $to->endOfDay()])
            ->orderBy('starts_at')->get();
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $request->validate(['repeat_weeks' => ['nullable', 'integer', 'min:0', 'max:26']]);
        $repeat = (int) $request->input('repeat_weeks', 0);

        // Crea la sessione e, opzionalmente, le stesse nelle settimane successive.
        $created = collect(range(0, $repeat))->map(fn ($w) => TrainingSession::create([
            ...$data,
            'starts_at' => $request->date('starts_at')->addWeeks($w),
            'ends_at' => $request->date('ends_at')->addWeeks($w),
        ]));

        return response()->json($created, 201);
    }

    public function update(Request $request, TrainingSession $session)
    {
        $session->update($this->validated($request));

        return $session->load('package:id,name,slug,color');
    }

    public function destroy(TrainingSession $session)
    {
        $session->delete();

        return response()->noContent();
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'package_id' => ['nullable', 'exists:packages,id'],
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'location' => ['required', 'string', 'max:120'],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);
    }
}
