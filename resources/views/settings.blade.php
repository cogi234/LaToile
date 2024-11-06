<x-app-layout>
    <!-- Post forms -->
    @auth
    <x-post-forms />
    @endauth

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Paramètres
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-full">
                    <!-- Section préférences -->
                    <section>
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Préférences
                            </h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Mettez à jour vos préférences pour l'utilisation du site.
                            </p>
                        </div>
                        <livewire:settings.update-preferences-privateMessage-form />
                        <livewire:settings.update-preferences-notifications-form />
                    </section>
                </div>
            </div>
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-full">
                    <!-- Section apparences -->
                    <section>
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Apparence
                            </h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Modifiez l'apparence lors de l'utilisation du site.
                            </p>
                        </div>
                        <livewire:settings.update-apparences />
                    </section>
                </div>
            </div>
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-full">
                    <!-- Section Profil -->
                    <section>
                        <div class="sm:mb-2 mt-0">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Profil
                            </h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Mettez à jour vos informations de profil.
                            </p>
                        </div>
                        <x-primary-button :href="route('profile')" wire:navigate
                            class="sm:mt-1 mt-5 sm:!ml-auto !ml-0 items-start self-start !bg-slate-500/90 hover:!bg-slate-700/90 dark:!bg-slate-400/50 dark:hover:!bg-slate-500 dark:!text-gray-100 transition duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                            </svg>
            
                            Options de profil
                        </x-primary-button>
                    </section>
                </div>
            </div>
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <x-primary-button :href="route('blockedUsers')" wire:navigate
                            class="sm:mt-1 mt-5 sm:!ml-auto !ml-0 items-start self-start !bg-slate-500/90 hover:!bg-slate-700/90 dark:!bg-slate-400/50 dark:hover:!bg-slate-500 dark:!text-gray-100 transition duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                              </svg>

                            Liste d'utilisateur bloquer
                        </x-primary-button>
                </div>
            </div>
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>