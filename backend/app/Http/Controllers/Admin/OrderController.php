<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return Order::with(['user:id,name,email,phone,status', 'package:id,name,slug,duration_weeks'])
            ->when($request->string('status')->toString(), fn ($q, $s) => $q->where('status', $s))
            ->latest()->get();
    }

    /** Nicole può assegnare direttamente un pacchetto a un cliente (es. pagato in studio). */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'package_id' => ['required', 'exists:packages,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $package = Package::findOrFail($data['package_id']);

        $order = Order::create([
            ...$data,
            'price_cents' => $package->price_cents,
            'status' => 'active',
            'starts_on' => today(),
            'ends_on' => today()->addWeeks($package->duration_weeks),
        ]);
        $order->user->update(['status' => 'active']);

        return response()->json($order->load('user', 'package'), 201);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(Order::STATUSES)],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Attivando un ordine si attiva anche il cliente e si imposta il periodo.
        if ($data['status'] === 'active' && $order->status !== 'active') {
            $data['starts_on'] ??= today();
            $data['ends_on'] ??= today()->addWeeks($order->package->duration_weeks);
            $order->user->update(['status' => 'active']);
        }

        $order->update($data);

        return $order->load(['user:id,name,email,phone,status', 'package:id,name,slug,duration_weeks']);
    }
}
