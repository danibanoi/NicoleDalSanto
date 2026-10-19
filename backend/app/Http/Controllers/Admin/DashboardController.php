<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\TrainingSession;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return [
            'pending_users' => User::where('role', 'client')->where('status', 'pending')->count(),
            'active_clients' => User::where('role', 'client')->where('status', 'active')->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'unread_messages' => ContactMessage::where('is_read', false)->count(),
            'upcoming_sessions' => TrainingSession::where('starts_at', '>=', now())->count(),
            'revenue_cents' => (int) Order::whereIn('status', ['active', 'completed'])->sum('price_cents'),
        ];
    }
}
