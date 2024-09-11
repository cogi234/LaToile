<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public $avatar;
    public string $name = '';
    public string $email = '';
    public ?string $bio = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->avatar = Auth::user()->avatar ?? 'images/no-avatar.png'; // Valeur par défaut si l'avatar n'existe pas
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->bio = Auth::user()->bio;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        if (!$this->avatar) {
            $this->avatar = 'images/no-avatar.png'; // Photo par défaut
        }

        $validated = $this->validate([
            'avatar' => ['nullable', 'image'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'bio' => ['nullable', 'string']
        ]);

        $user->fill($validated);

        if ($this->avatar && !is_string($this->avatar)) {
            // Si un fichier est téléchargé, sauvegarder l'image
            $user->avatar = $this->avatar->store('profile-photo', 'public');
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profil') }}
        </h2>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <div>
            <!-- Photo de profil -->
            <x-input-label for="avatar" :value="__('Photo de profil')" class="mb-4"/>
            
            <a x-data x-on:click="$refs.fileInput.click()">
                <input type="file" wire:model="avatar" x-ref="fileInput" style="display:none">
                
                @if ($avatar && !is_string($avatar))
                    <img src="{{ $avatar->temporaryUrl() }}" alt="Photo de profil" height="200" width="200" title="{{ $avatar->temporaryUrl() }}">
                @elseif ($avatar == null)
                    <img src="images/no-avatar.png" alt="Photo par défaut" height="200" width="200" title="photo de base">
                @else
                    <img src="{{ $avatar }}" alt="Photo actuelle" height="200" width="200" title="photo actuelle">
                @endif
            </a>
        
            <div wire:loading wire:target="avatar" class="dark:text-gray-100">
                Uploading...
            </div>
            
            @error('avatar')
                <span class="error" class="dark:text-gray-100">{{ $message }}</span>
            @enderror
        </div>
        
        <div>
            <x-input-label for="name" :value="__('Nom')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full"
                required autofocus autocomplete="name" placeholder="Username" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full"
                required autocomplete="username" placeholder="Email" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button wire:click.prevent="sendVerification"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
        <div>
            <x-input-label for="bio" :value="__('Bio')" />
            <textarea wire:model="bio" id="bio" name="bio" 
                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full min-h-[140px]"
                rows="5" placeholder="Bio"></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
