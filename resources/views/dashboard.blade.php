<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @auth
                <livewire:posts.create />
            @endauth
            <br />

            <!-- Ajout des onglets -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg md:mb-5 sm:mb-3">
                <div class="tabs p-6 text-gray-900 dark:text-gray-100">
                    <!-- Les blocs sont maintenant des liens entièrement cliquables -->
                    <a href="javascript:void(0);" class="tab active" id="suivis-tab" onclick="showContent('suivis')">
                        {{ __('Voir tout') }}
                    </a>
                    <a href="javascript:void(0);" class="tab" id="abonnements-tab" onclick="showContent('abonnements')">
                        {{ __('Abonnements') }}
                    </a>
                    <a href="javascript:void(0);" class="tab" id="tags-tab" onclick="showContent('tags')">
                        {{ __('Par Tags') }}
                    </a>
                </div>
            </div>

            <!-- Contenu associé aux onglets -->
            <div class="bg-transparent overflow-hidden">
                <div class="text-gray-900 dark:text-gray-100">
                    <div id="suivis-content" class="content-section" style="display: block;">
                        <livewire:posts.post />
                    </div>
                    <div id="abonnements-content" class="content-section" style="display: none;">
                        <h2>{{ __('Posts Suivis') }}</h2>
                        <p>{{ __('Voici les posts des utilisateurs que vous suivez.') }}</p>
                    </div>
                    <div id="tags-content" class="content-section" style="display: none;">
                        <h2>{{ __('Posts par Tags') }}</h2>
                        <p>{{ __('Voici les posts filtrés par tags.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .tabs {
            display: flex;
            justify-content: space-around;
            background-color: #2d3748; /* correspond à bg-gray-800 */
            padding: 10px;
        }

        .tab {
            cursor: pointer;
            padding: 10px;
            color: white;
            font-weight: bold;
            text-align: center;
            text-decoration: none; /* Retire la décoration de lien */
            flex-grow: 1; /* Permet aux onglets de prendre toute la largeur de la ligne */
            border-radius: 8px; /* Ajoute une légère courbure aux blocs */
            transition: 0.3s ease;
        }

        .tab:hover {
            background-color: #4a5568; /* correspond à bg-gray-700 */
        }

        .active {
            background-color: #4a5568; /* correspond à bg-gray-700 */
            border-bottom: 3px solid #ff6961; /* correspond à text-blue-400 */
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
        function showContent(tab) {
            // Cacher tous les contenus
            document.getElementById('suivis-content').style.display = 'none';
            document.getElementById('abonnements-content').style.display = 'none';
            document.getElementById('tags-content').style.display = 'none';

            // Enlever la classe active de tous les onglets
            document.getElementById('suivis-tab').classList.remove('active');
            document.getElementById('abonnements-tab').classList.remove('active');
            document.getElementById('tags-tab').classList.remove('active');

            // Afficher la section sélectionnée et rendre l'onglet actif
            document.getElementById(tab + '-content').style.display = 'block';
            document.getElementById(tab + '-tab').classList.add('active');
        }

        // Initialiser l'affichage pour que "Suivis" soit visible par défaut
        document.addEventListener('DOMContentLoaded', (event) => {
            showContent('suivis');
        });
        
    </script>

</x-app-layout>
