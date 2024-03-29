<?php

namespace App\Http\Controllers;

use App\Note;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class NotesController extends Controller
{
    /**
     * Display a listing of the notes.
     *
     * @param \Illuminate\Http\Request $request
     * @param $user_email
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $user_email)
    {
        $user = User::where('email', $user_email)->first();
        $user_id = $user->id;

        $notes = Note::where('user_id', $user_id)
                        ->orderBy('updated_at', 'DESC')
                        ->get();

        return view('notes.notes', compact('notes', 'user'));
    }

    /**
     * Show the form for creating a new note.
     * 
     * @param $user_email
     * @return \Illuminate\Http\Response
     */
    public function create($user_email)
    {
        $user = User::where('email', $user_email)->first();

        return view('notes.create', compact('user'));
    }

    /**
     * Sotre a newly created note in database.
     * 
     * @param \Illuminate\Http\Request $request
     * @param $user_email
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $user_email)
    {
        $user = User::where('email', $user_email)->first();
        $user_id = $user->id;

        $this->validate($request, [
            'title' => 'required',
            'body'  => 'required'
        ]);

        $note = Note::create([
            'user_id'   => $user_id,
            'title'     => $request->title,
            'slug'      => str_slug($request->title).str_random(10),
            'body'      => $request->body
        ]);

        return redirect('/');
    }

    /**
     * Show the form for editing the specified note.
     * 
     * @param \App\Note $note
     * @return \Illuminate\Http\Response
     */
    public function edit(Note $note)
    {
        $user = User::find($note->user_id);
        
        return view('notes.edit', compact('note', 'user'));
    }

    /**
     * Update the specified note.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Note $note
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Note $note)
    {
        $user = User::find($note->user_id);

        $note->title = $request->title;
        $note->body = $request->body;

        $note->save();

        $notes = Note::where('user_id', $user->id)
                        ->orderBy('updated_at', 'DESC')
                        ->get();

        return view('notes.notes', compact('notes', 'user'));
    }

    /**
     * Destroy the specified note.
     * 
     * @param \App\Note $note
     * @return \Illuminate\Http\Response
     */
    public function destroy(Note $note)
    {
        $user = User::find($note->user_id);
        $note->delete();

        $notes = Note::where('user_id', $user->id)
                        ->orderBy('updated_at', 'DESC')
                        ->get();

        return view('notes.notes', compact('notes', 'user'));
    }

}