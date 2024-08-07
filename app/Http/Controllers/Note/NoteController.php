<?php

namespace App\Http\Controllers\Note;

use App\Http\Controllers\MainController;
use App\Models\Note\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends MainController
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'target' => 'required',
            'identifier' => 'required',
            'message' => 'required|string',
        ]);

        // Assuming you have a Note model set up to handle note entries
        $note = new Note();
        $note->target = $validated['target'];
        $note->identifier = $validated['identifier'];
        $note->message = $validated['message'];
        $note->author = Auth::user()->id;
        $note->save();

        $noteDetails = [
            'note_id' => $note->id,
            'author' => $note->user->name,
            'message' => $note->message,
            'created_at' => $note->created_at->format('Y-m-d H:i'),

        ];

        // Return a JSON response
        return response()->json([
            'success' => true,
            'message' => 'Note added successfully!',
            'note' => $noteDetails,
            'customer' => $note->target,
            'canDelete' => $request->user()->can('Delete customer notes'),
        ]);
    }

    public function destroy(Request $request)
    {
        $noteId = $request->input('note_id');

        // Assuming you have a Note model with the provided note ID
        $note = Note::find($noteId);
        if ($note && $request->user()->can('Delete customer notes')) {
            $note->delete();
            return response()->json(['success' => true, 'message' => 'Note deleted successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Note not found'], 404);
        }
    }
}
