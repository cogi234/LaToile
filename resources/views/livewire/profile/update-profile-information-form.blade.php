<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Locked;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

new class extends Component
{
    use WithFileUploads;

    #[Validate('nullable')]
    #[Validate('image', message: 'Ca doit etre une image.')]
    public $avatar = null;
    #[Validate('required', message: 'Le nom ne peut pas etre vide.')]
    #[Validate('max:255', message: 'Le nom est trop long.')]
    public string $name = '';
    #[Validate('required', message: "L'adresse courriel ne peut pas etre vide.")]
    #[Validate('max:255', message: "L'adresse courriel est trop longue.")]
    #[Validate('lowercase', message: "L'adresse courriel doit etre en minuscule.")]
    #[Validate('email', message: 'Ca doit etre une adresse courriel valide.')]
    public string $email = '';
    #[Validate('nullable')]
    public ?string $bio = '';

    #[Locked]
    public string $avatarPath = "";

    public function rules()
    {
        return [
            'name' => [
                Rule::unique('users')->ignore(Auth::user()->id)
            ],
            'email' => [
                Rule::unique('users')->ignore(Auth::user()->id)
            ]
        ];
    }

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        if (Auth::user()->avatar != null && Auth::user()->avatar != ''){
            $this->avatarPath = Auth::user()->avatar;
        }
        
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
        $validated = $this->validate();
        
        $user->fill($validated);

        if ($this->avatar != null) {
            // Si un fichier est téléchargé, sauvegarder l'image
            //$user->avatar = $this->avatar->store('profile-photo', 'public');
            //Essayer de compresser l'image
            $image = Image::read($this->avatar)->cover(200, 200, 'center')->toJpeg();
            $user->avatar = 'profile-photo/' . Str::random(40) . '.jpg';
            $image->save('storage/' . $user->avatar);
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
            Informations de profil
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Mettez à jour les informations de profil et l'adresse e-mail de votre compte.
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <div>
            <!-- Photo de profil -->
            <x-input-label for="avatar" :value="__('Photo de profil')" class="mb-4"/>
            
            <a x-data x-on:click="$refs.fileInput.click()">
                <input type="file" wire:model="avatar" x-ref="fileInput" style="display:none">
                
                @if ($avatar != null && !is_string($avatar))
                    <img src="{{ $avatar->temporaryUrl() }}" alt="Photo de profil" height="200" width="200" title="{{ $avatar->temporaryUrl() }}">
                @elseif ($avatarPath != null && $avatarPath != '')
                    <img src="{{ $avatarPath }}" alt="Photo actuelle" height="200" width="200" title="photo actuelle">
                @else
                    <img src="images/no-avatar.png" alt="Photo par défaut" height="200" width="200" title="photo de base">
                @endif
            </a>
        
            <div wire:loading wire:target="avatar" class="dark:text-gray-100">
                Uploading...
            </div>
            
            @error('avatar')
                <span class="error text-red-600">{{ $message }}</span>
            @enderror
        </div>
        
        <div>
            <x-input-label for="name" :value="__('Nom')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Courriel')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full"
                required autocomplete="email" placeholder="Courriel" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
            @if (auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2  text-green-600 dark:text-green-400">
                        Votre adresse email est vérifiée.
                    </p>
                </div>
            @endif

            @if (auth()->check() && !auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        Votre adresse email n'est pas vérifiée.

                        <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Cliquez ici pour renvoyer le courriel de vérification.
                        </button>
                    </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                        Un nouveau lien de vérification a été envoyé à votre adresse courriel
                    </p>
                @endif
                </div>
            @endif
        </div>
        <div>
            <x-input-label for="bio" :value="__('Bio')" />
            <textarea wire:model="bio" id="bio" name="bio" type="text"
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
