<x-app-layout>
    @if (trim($query) !== '')
        <div class="py-6">
            <div class="max-w-5xl mx-auto px-3 sm:px-8">
                <!-- Ajout des onglets -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-3 md:mb-5">
                    <div class="tabs p-6 text-gray-900 dark:text-gray-100">
                        <!-- Les blocs sont maintenant des liens entièrement cliquables -->
                        <a href="javascript:void(0);" class="tab active" id="posts-tab" onclick="showContent('posts')">
                            Posts 
                        </a>
                        @auth
                        <a href="javascript:void(0);" class="tab" id="users-tab" onclick="showContent('users')">
                            Utilisateurs
                        </a>
                        <a href="javascript:void(0);" class="tab" id="tags-tab" onclick="showContent('tags')">
                            Tags
                        </a>
                        @endauth
                    </div>
                </div>

                <!-- Contenu associé aux onglets -->
                <div class="bg-transparent overflow-hidden">
                    <div class="text-gray-900 dark:text-gray-100">
                        <div id="posts-content" class="content-section" style="display: block;">
                            <livewire:search.view-searched-posts query="{{$query}}"/>
                        </div>
                        <div id="users-content" class="content-section" style="display: none;">
                            <livewire:search.view-searched-users query="{{$query}}"/>
                        </div>
                        <div id="tags-content" class="content-section" style="display: none;">
                            <livewire:search.view-searched-tag query="{{$query}}"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
            <div class="py-12">
                <div class="max-w-lg mx-auto text-center bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg p-8">
                    <!-- Icône d'erreur -->
                    <div class="mb-6">
                        <svg class="w-16 h-16 text-red-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-.01-6a1 1 0 011-1h.01a1 1 0 011 1v2a1 1 0 01-1 1h-.01a1 1 0 01-1-1v-2z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8-3.582 8-8 8z" />
                        </svg>
                    </div>
            
                    <!-- Message d'erreur -->
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Recherche introuvable</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-8">
                        Nous n'avons trouvé aucun résultat correspondant à votre recherche. Veuillez vérifier votre requête et réessayer.
                    </p>
            
                    <!-- Boutons d'action -->
                    <div class="flex justify-center space-x-4">
                        <a href="{{ url()->previous() }}" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">
                            Retour
                        </a>
                        <a href="{{ route('dashboard') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-900 font-semibold py-2 px-4 rounded-lg transition duration-300">
                            Accueil
                        </a>
                    </div>
                </div>
            </div>
        @endif
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

        #dropdownMenu::-webkit-scrollbar {
            width: 8px;
        }

        #dropdownMenu::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }
    </style>

    <script>
        function toggleDropdown() {
        var menu = document.getElementById("dropdownMenu");
        menu.classList.toggle("hidden");
    }

    window.onclick = function(event) {
        if (!event.target.closest('button')) {
            var dropdown = document.getElementById("dropdownMenu");
            if (!dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
            }
        }
    }


        window.onload = function(){
            document.getElementById('searchBar').value = "{{$query}}";
        };

        function showContent(tab) {
            //Envoyer l'event pour reset le contenu des tabs
            this.dispatchEvent(
                new Event('reset-post-views')
            );

            // Cacher tous les contenus
            document.getElementById('posts-content').style.display = 'none';
            @auth
            document.getElementById('users-content').style.display = 'none';
            document.getElementById('tags-content').style.display = 'none';
            @endauth

            // Enlever la classe active de tous les onglets
            document.getElementById('posts-tab').classList.remove('active');
            @auth
            document.getElementById('users-tab').classList.remove('active');
            document.getElementById('tags-tab').classList.remove('active');
            @endauth

            // Afficher la section sélectionnée et rendre l'onglet actif
            document.getElementById(tab + '-content').style.display = 'block';
            document.getElementById(tab + '-tab').classList.add('active');
        }
    </script>

</x-app-layout>