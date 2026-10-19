<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class MessageController extends Controller
{
    public function index()
    {
        return ContactMessage::latest()->get();
    }

    public function markRead(ContactMessage $message)
    {
        $message->update(['is_read' => true]);

        return $message;
    }
}
