<?php

namespace App\Http\Livewire\Posts;

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\Post;

new class extends Component {
    public $postId;
    public $confirmingDeletion = false;

    public function mount($id)
    {
        $this->postId = $id;
    }

    public function confirmDeletion()
    {
        $this->confirmingDeletion = true;
    }

    public function deletePost()
    {
        $post = Post::find($this->postId);
        if ($post) {
            $post->delete();
            session()->flash('message', 'Le post a été supprimé avec succès.');
        }
        $this->confirmingDeletion = false;
    }
}; ?>

<div class="@if ($enabled)
fixed
@else
hidden
@endif inset-0 bg-gray-200/75 dark:bg-gray-900/75 flex items-center justify-center">
    <div class="relative bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
        <div class="mb-4">
            <h2 class="text-lg font-semibold">Confirmation de suppression</h2>
            <p>Êtes-vous certain de vouloir supprimer ce post? Il sera supprimé définitivement.</p>
        </div>
        <div class="flex justify-end space-x-4">
            <button wire:click="deletePost"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Supprimer</button>
            <button wire:click="$set('confirmingDeletion', false)"
                class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Annuler</button>
        </div>
    </div>
</div>
@else
<button wire:click="confirmDeletion"
    class="like-button flex items-center text-gray-600 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-500 mr-4">
    <!-- SVG pour l'icône de suppression -->
</button>
@endif