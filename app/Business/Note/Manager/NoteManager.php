<?php

namespace App\Business\Note\Manager;

use App\Models\Note\Note;
use Illuminate\Database\Eloquent\Collection;

class NoteManager
{
    public function getNotesByIdentifierAndTarget(string $identifier, string $target): Collection
    {
        return Note::where('identifier', $identifier)
            ->where('target', $target)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function deleteNote(int $noteId): void
    {
        $note = Note::find($noteId);

        if($note) {
            $note->delete();
        }
    }
}
