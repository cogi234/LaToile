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
use Illuminate\Support\Facades\Config;

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

    #[Validate('nullable')]
    #[Validate('image', message: 'Ca doit etre une image.')]
    public $background = null;

    #[Locked]
    public string $avatarPath = "";
    #[Locked]
    public string $backgroundPath = ""; // Add this line to declare the background path variable

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

    public function mount(): void
    {
        if (Auth::user()->avatar != null && Auth::user()->avatar != '') {
            $this->avatarPath = Auth::user()->getAvatar();
        }

        if (Auth::user()->profile_background != null && Auth::user()->profile_background != '') {
            $this->backgroundPath = Auth::user()->profile_background;
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
            //Essayer de compresser l'image
            $size = Config::get('image.avatar_size');
            $image = Image::read($this->avatar)->cover($size, $size, 'center')->toJpeg();
            $user->avatar = 'profile-photo/' . Str::random(40) . '.jpg';
            $image->save('storage/' . $user->avatar);
        }
        if ($this->background) {
            $size = Config::get('image.background_size'); // Define an appropriate size
            $image = Image::read($this->background)->scaleDown($size, $size)->toJpeg();
            $backgroundFileName = Str::random(40) . '.jpg';  // Generate a random filename for the background image
            $backgroundPath = 'profile-background/' . $backgroundFileName;  // Define the path for the background image
            $image->save('storage/' . $backgroundPath);  // Save the image in the storage folder
            $user->profile_background = $backgroundPath;  // Store the path in the database
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

    <form wire:submit.prevent="updateProfileInformation" class="mt-6 space-y-6">
        <!-- Photo de profil -->
        <div>
            <x-input-label for="avatar" value="Photo de profil" class="mb-4"/>
            <a x-data x-on:click="$refs.avatarInput.click()">
                <input type="file" wire:model="avatar" x-ref="avatarInput" style="display:none">
                @if ($avatar && !is_string($avatar))
                    <img src="{{ $avatar->temporaryUrl() }}" alt="Photo de profil" height="200" width="200">
                @elseif ($avatarPath)
                    <img src="{{ $avatarPath }}" alt="Photo actuelle" height="200" width="200">
                @else
                    <img src="{{ 'images/no-avatar.png' }}" alt="Photo par défaut" height="200" width="200">
                @endif
            </a>
            <div wire:loading wire:target="avatar" class="dark:text-gray-100">Chargement...</div>
            @error('avatar') <span class="error text-red-600">{{ $message }}</span> @enderror
        </div>

        <!-- Photo de fond de profil -->
        <div>
            <x-input-label for="background" value="Photo de fond de profil" class="mb-4"/>
            <a x-data x-on:click="$refs.backgroundInput.click()">
                <input type="file" wire:model="background" x-ref="backgroundInput" style="display:none">
                @if ($background && !is_string($background))
                    <img src="{{ $background->temporaryUrl() }}" alt="Photo de fond" height="200" width="300">
                @elseif ($backgroundPath)
                    <img src="{{ $backgroundPath }}" alt="Fond actuel" height="200" width="300">
                @else
                    <img src="{{ 'images/no-background.png' }}" alt="Fond par défaut" height="200" width="300">
                @endif
            </a>
            <div wire:loading wire:target="background" class="dark:text-gray-100">Chargement...</div>
            @error('background') <span class="error text-red-600">{{ $message }}</span> @enderror
        </div>

        <!-- Nom -->
        <div>
            <x-input-label for="name" value="Nom" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Courriel -->
        <div>
            <x-input-label for="email" value="Courriel" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="email" placeholder="Courriel" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
            
            @if (auth()->user()->hasVerifiedEmail())
                <p class="text-sm mt-2 text-green-600 dark:text-green-400">
                    Votre adresse courriel est vérifiée.
                </p>
            @else
                <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                    Votre adresse email n'est pas vérifiée.
                    <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                        Cliquez ici pour renvoyer le courriel de vérification.
                    </button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                        Un nouveau lien de vérification a été envoyé à votre adresse courriel.
                    </p>
                @endif
            @endif
        </div>

        <!-- Bio -->
        <div>
            <x-input-label for="bio" value="Bio" />
            <textarea wire:model="bio" id="bio" name="bio" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full min-h-[140px]" rows="5" placeholder="Bio"></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <!-- Save Button -->
        <div class="flex items-center gap-4">
            <x-primary-button>Sauvegarder</x-primary-button>
            <x-action-message class="me-3" on="profile-updated">Sauvegardé.</x-action-message>
        </div>
    </form>
</section>
