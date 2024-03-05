<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function  createComment()
    {
        $comments = Comment::all();
        return view('showProduct', compact('$comments'));
    }

    public function storeComment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'evaluation' => 'required|string',
            'comment' => 'required|string',
            'email' => 'required|string',
            'user_id' => 'required|exists:user,id',

        ]);

        // Créer le produit
        $comments = new  Comment();
        $comments->name = $request->name;
        $comments->evaluation = $request->evaluation;
        $comments->comment = $request->comment;
        $comments->user_id = $request->user_id;
        $comments->save();

        return redirect()->route('showProduct.create')->with('success', 'commentaire ajouté avec succès.');
    }
}
