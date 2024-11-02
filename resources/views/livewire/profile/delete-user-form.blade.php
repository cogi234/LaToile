<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use App\Models\Post;
use App\Models\Ban;
use App\Models\Draft;
use App\Models\GroupMessage;
use App\Models\QueuedPost;
use App\Models\Report;
use App\Models\ReportMessage;
use App\Models\PrivateMessage;
use App\Models\User;
use App\Models\Tag;
use App\Models\Warning;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        //tap(Auth::user(), $logout(...))->delete();
        $user = Auth::user();

        // Supprimer toutes les relations avec l'utilisateurs
        $posts = Post::where("user_id", $user->id)->get();
        foreach ($posts as $post) {
            DB::table('post_has_tags')->where('post_id', $post->id)->delete();
        }
        Ban::where("user_id", $user->id)->delete();
        Draft::where("user_id", $user->id)->delete();
        QueuedPost::where("user_id", $user->id)->delete();
        Report::where("user_id", $user->id)->delete();
        Warning::where("user_id", $user->id)->delete();
        PrivateMessage::where("sender_id", $user->id)->delete();
        PrivateMessage::where("receiver_id", $user->id)->delete();
        GroupMessage::where("user_id", $user->id)->delete();
        DB::table('likes')->where("user_id", $user->id)->delete();
        DB::table('group_memberships')->where("user_id", $user->id)->delete();
        DB::table('followed_tags')->where('user_id', $user->id)->delete();
        DB::table('blocked_tags')->where('user_id', $user->id)->delete();
        DB::table('followed_users')->where('user', $user->id)->delete();
        DB::table('blocked_users')->where('user', $user->id)->delete();
        DB::table('notifications')->where('notifiable_id', $user->id)->delete();
        Post::where("user_id", $user->id)->delete();

        Auth::logout();

        $user->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Supprimer le compte') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées. Il ne sera plus possible d\'y avoir accès.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Supprimer le compte') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6">

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Êtes-vous sûr de vouloir supprimer votre compte?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées. Veuillez saisir votre mot de passe pour confirmer que vous souhaitez supprimer définitivement votre compte.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    wire:model="password"
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Mot de passe') }}"
                />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Annuler') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Supprimer le compte') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
