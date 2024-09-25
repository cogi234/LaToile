<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-3 sm:px-8">
            <!-- Post Creation -->
            @auth
            <div class="fixed xl:hidden bg-white dark:bg-gray-800 left-0 top-16 p-[2.1em] w-[100%]">
            </div>
            <button type="button" class="fixed left-[50%] translate-x-[-50%] top-[72px] xl:left-5 xl:bottom-5 xl:top-auto xl:translate-x-0 block mx-auto mb-3 md:mb-5 h-12 items-center px-4 py-2 uppercase tracking-widest
                    border border-transparent rounded-md font-semibold text-xs bg-gray-800 dark:bg-gray-200 text-white
                    dark:text-gray-800 hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white
                    active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500
                    focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                onclick="showPostEditor()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-3 inline-block">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>

                Publier un post
            </button>
            <livewire:posts.create />
            @endauth

            <!-- Ajout des onglets -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-3 md:mb-5">
                <div class="tabs p-6 text-gray-900 dark:text-gray-100">
                    <!-- Les blocs sont maintenant des liens entièrement cliquables -->
                    <a href="javascript:void(0);" class="tab active" id="all-tab" onclick="showContent('all')">
                        Tous les posts
                    </a>
                    @auth
                    <a href="javascript:void(0);" class="tab" id="abonnements-tab" onclick="showContent('abonnements')">
                        Par Utilisateurs suivis
                    </a>
                    <a href="javascript:void(0);" class="tab" id="tags-tab" onclick="showContent('tags')">
                        Par Tags suivis
                    </a>
                    @endauth
                </div>
            </div>

            <!-- Contenu associé aux onglets -->
            <div class="bg-transparent overflow-hidden">
                <div class="text-gray-900 dark:text-gray-100">
                    <div id="all-content" class="content-section" style="display: block;">
                        <livewire:posts.viewall />
                    </div>
                    @auth
                    <div id="abonnements-content" class="content-section" style="display: none;">
                        <livewire:posts.view-followed-users />
                    </div>
                    <div id="tags-content" class="content-section" style="display: none;">
                        <h2>Posts par Tags</h2>
                        <p>Voici les posts filtrés par tags.</p>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <style>
        .tabs {
            display: flex;
            justify-content: space-around;
            background-color: #2d3748;
            /* correspond à bg-gray-800 */
            padding: 10px;
        }

        .tab {
            cursor: pointer;
            padding: 10px;
            color: white;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            /* Retire la décoration de lien */
            flex-grow: 1;
            /* Permet aux onglets de prendre toute la largeur de la ligne */
            border-radius: 8px;
            /* Ajoute une légère courbure aux blocs */
            transition: 0.3s ease;
        }

        .tab:hover {
            background-color: #4a5568;
            /* correspond à bg-gray-700 */
        }

        .active {
            background-color: #4a5568;
            /* correspond à bg-gray-700 */
            border-bottom: 3px solid #ff6961;
            /* correspond à text-blue-400 */
            transition: 0s;
        }

        .content-section {
            display: none;
        }

        .content-section.active {
            display: block;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Charger l'onglet sélectionné précédemment
            const lastTab = localStorage.getItem('lastTab') || 'all'; // 'all' par défaut
            showContent(lastTab);
        });

        function showContent(tab) {
            // Enregistrer l'onglet actif dans localStorage
            localStorage.setItem('lastTab', tab);

            // Envoyer l'event pour reset le contenu des tabs
            this.dispatchEvent(
                new Event('reset-post-views')
            );

            // Cacher tous les contenus
            document.getElementById('all-content').style.display = 'none';
            @auth
            document.getElementById('abonnements-content').style.display = 'none';
            document.getElementById('tags-content').style.display = 'none';
            @endauth

            // Enlever la classe active de tous les onglets
            document.getElementById('all-tab').classList.remove('active');
            @auth
            document.getElementById('abonnements-tab').classList.remove('active');
            document.getElementById('tags-tab').classList.remove('active');
            @endauth

            // Afficher la section sélectionnée et rendre l'onglet actif
            document.getElementById(tab + '-content').style.display = 'block';
            document.getElementById(tab + '-tab').classList.add('active');
        }

        function openEditPopup() {
            document.getElementById('editPostModal').classList.remove('hidden');
        }

        // Fonction pour fermer le modal
        function closeEditPopup() {
            document.getElementById('editPostModal').classList.add('hidden');
        }
    </script>
    <x-script-showPostEditor />
</x-app-layout>