<?php

namespace App\Http\Controllers;

use App\Models\LogNotifikasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogNotifikasiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = LogNotifikasi::where('user_id', $request->user()->id)
            ->where('visible_in_bell', true)
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'user_id' => $n->user_id,
                    'ticket_id' => $n->ticket_id,
                    'actor_user_id' => $n->actor_user_id,
                    'actor_name' => $n->actor_name,
                    'recipient_type' => $n->recipient_type,
                    'action' => $n->action,
                    'title' => $n->title,
                    'message' => $n->message,
                    'status' => $n->status,
                    'visible_in_bell' => $n->visible_in_bell,
                    'read' => $n->read_at !== null,
                    'read_at' => $n->read_at,
                    'time' => $n->created_at,
                    'created_at' => $n->created_at,
                ];
            });

        return response()->json($notifications);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $query = LogNotifikasi::query()
            ->where('user_id', $request->user()->id)
            ->where('visible_in_bell', true)
            ->whereNull('read_at');

        if ($request->filled('ids')) {
            $query->whereIn('id', (array) $request->input('ids'));
        }

        $query->update(['read_at' => now()]);

        return response()->json(['message' => 'Notifikasi telah dibaca.']);
    }
}
