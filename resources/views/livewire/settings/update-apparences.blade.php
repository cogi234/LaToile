<form class="mt-6 space-y-6">
    <div>
        <span class="text-gray-900 dark:text-gray-100">Quel apparence souhaites-tu avoir sur le site?</span><br>
        <span class="mt-1 text-sm text-gray-600 dark:text-gray-400">Mode clair / Mode sombre</span>
        <div class="flex flex-row mt-2">
            <label for="toggleSwitch" title="Mode clair">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="dark:text-gray-100 size-5 mr-1" title="Mode clair">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                </svg>      
            </label>        
            <input type="checkbox" class="toggle" id="toggleSwitch">
            <label class="toggle" for="toggleSwitch">Toggle</label>            
            <label for="toggleSwitch" title="Mode sombre">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="dark:text-gray-100 size-5 ml-2" title="Mode sombre">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                </svg>    
            </label>            
            <x-action-message class="me-3 ml-2" on="preferences-updated">
                Sauvegardé.
            </x-action-message>
        </div>
        <div class="flex items-center mt-4">
            <input type="checkbox" id="systemPreference" class="mr-2" />
            <label for="systemPreference" class="text-gray-900 dark:text-gray-100">Utiliser la préférence système</label>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const themeToggle = document.getElementById('toggleSwitch');
        const systemPreferenceToggle = document.getElementById('systemPreference');

        // Vérifie si une préférence de thème existe dans localStorage ou respecte le système
        const currentTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

        document.documentElement.classList.toggle('dark', currentTheme === 'dark');
        themeToggle.checked = currentTheme === 'dark'; // Coche le toggle si le mode sombre est actif

        // Écoute les changements sur le toggle de mode
        themeToggle.addEventListener('change', function () {
            if (this.checked) {
                // Activer le mode sombre
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                // Désactiver le mode sombre
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }

            // Désactiver le toggle de préférence système si un thème est choisi
            systemPreferenceToggle.checked = false;
            localStorage.setItem('systemPreference', "false");
        });

        // Vérifiez et définissez l'état initial du toggle de préférence système
        systemPreference = localStorage.getItem('systemPreference') === "true";
        systemPreferenceToggle.checked = systemPreference;

        // Si la préférence système est activée, mettez à jour l'état initial
        if (systemPreference) {
            const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
            document.documentElement.classList.toggle('dark', prefersDarkScheme.matches);
        }

        // Écoute les changements sur le toggle de préférence système
        systemPreferenceToggle.addEventListener('change', function () {
            if (this.checked) {
                // Réinitialiser le thème à la préférence système
                localStorage.removeItem('theme');
                localStorage.setItem('systemPreference', "true"); // Sauvegarder la préférence dans localStorage
                const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
                document.documentElement.classList.toggle('dark', prefersDarkScheme.matches);
                themeToggle.checked = prefersDarkScheme.matches; // Ajuster l'état du toggle
            } else {
                // Si l'utilisateur désactive la préférence système, réafficher le thème choisi
                const savedTheme = localStorage.getItem('theme');
                document.documentElement.classList.toggle('dark', savedTheme === 'dark');
                localStorage.setItem('systemPreference', "false"); // Sauvegarder la préférence dans localStorage
                themeToggle.checked = savedTheme === 'dark'; // Ajuster l'état du toggle
            }
        });
    });
</script>
