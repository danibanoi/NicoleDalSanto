<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return User::where('role', 'client')
            ->when($request->string('status')->toString(), fn ($q, $s) => $q->where('status', $s))
            ->when($request->string('q')->toString(), fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")))
            ->withCount(['bookings', 'orders' => fn ($q) => $q->where('status', 'active')])
            ->latest()->get(['id', 'name', 'email', 'phone', 'status', 'created_at']);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'active', 'disabled'])],
        ]);

        $user->update($data);

        return $user->only(['id', 'name', 'email', 'phone', 'status']);
    }
}
