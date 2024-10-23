<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;


class PostController extends Controller
{
    public function show(int $id)
    {
        $post = Post::findOrFail($id);
        return view('post.show', [
            'post' => Post::findOrFail($id)
        ]);
    }

    public function deletePost(int $id)
    {
        // Récupérer le post avec les relations nécessaires
        $post = Post::with(['previous', 'original_post', 'likes', 'tags', 'direct_shares'])->findOrFail($id);

        // Supprimer les relations associées
        $post->direct_shares()->delete(); // Supprime les posts qui répondent directement
        $post->likes()->detach(); // Détache tous les likes
        $post->tags()->detach(); // Détache tous les tags

        // Supprimer les commentaires associés

        // Supprimer le post
        $post->delete();

        return redirect()->route('dashboard');
    }

    public function updatePost(Request $request, $id)
    {
        $request->validate([
            'newContent' => 'required|string',
        ]);

        // Trouver le post par son ID
        $post = Post::findOrFail($id);
        
        $newContent = [
            [
                'type' => 'text',
                'content' => $request->input('newContent'),
            ]
        ];

        // Mettre à jour le contenu du post au format JSON
        $post->content = $newContent;
        $post->save();

        return redirect()->route('dashboard');
    }
}
