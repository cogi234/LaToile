<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-3 sm:px-8">
            <!-- Post forms -->
            @auth
            <x-post-forms />
            @endauth

            <!-- Ajout des onglets -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-3 md:mb-5 mt-14 xl:mt-0">
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
                        <livewire:posts.view-followed-tags />
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
    </script>
</x-app-layout>