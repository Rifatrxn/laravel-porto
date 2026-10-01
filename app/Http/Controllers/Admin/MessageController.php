<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        return view('admin.messages.index', [
            'rows' => Message::latest()->paginate(15),
        ]);
    }

    public function show(Message $message)
    {
        if (! $message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('admin.messages.show', ['row' => $message]);
    }

    public function update(Request $request, Message $message)
    {
        $message->update(['is_read' => $request->boolean('is_read')]);

        return back()->with('success', 'Status pesan diperbarui.');
    }

    public function destroy(Message $message)
    {
        $message->delete();

        return back()->with('success', 'Pesan berhasil dihapus.');
    }
}
