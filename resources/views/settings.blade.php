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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>