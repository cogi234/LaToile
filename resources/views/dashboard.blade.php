<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-3 sm:px-8">
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

            <!-- Tab and Filter Bar -->
            <div class="bg-white w-full lg:max-w-[50%] justify-self-center dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg md:mb-5">
                <div class="flex flex-col sm:flex-row p-2 sm:p-3 justify-self-center text-gray-900 dark:text-gray-100">
                    <a href="javascript:void(0);" 
                        class="text-black dark:text-gray-100 dark:hover:bg-blue-100/20 hover:bg-gray-200 cursor-pointer px-4 py-3 font-bold text-center no-underline flex-grow rounded-lg transition-all duration-300 ease-in-out activeFilter" 
                        id="newest-tab" onclick="applyFilter('newest')">
                        Les plus récents d'abord
                    </a>
                    <a href="javascript:void(0);" 
                        class="text-black dark:text-gray-100 dark:hover:bg-blue-100/20 hover:bg-gray-200 cursor-pointer px-4 py-3 font-bold text-center no-underline flex-grow rounded-lg transition-all duration-300 ease-in-out"
                        id="popular-tab" onclick="applyFilter('popular')">
                        Les plus populaires d'abord
                    </a>
                </div>
            </div>
            <!-- Contenu associé aux onglets -->
            <div class="bg-transparent overflow-hidden">
                <div class="text-gray-900 dark:text-gray-100">
                    <div id="all-content" class="content-section" style="display: block;">
                        <livewire:posts.viewall :key="'viewall-'.now()" />
                    </div>
                    @auth
                    <div id="abonnements-content" class="content-section" style="display: none;">
                        <livewire:posts.view-followed-users :key="'view-followed-users-'.now()" />
                    </div>
                    <div id="tags-content" class="content-section" style="display: none;">
                        <livewire:posts.view-followed-tags :key="'view-followed-tags-'.now()" />
                    </div>
                    @endauth
                </div>
            </div>
            <!-- Post forms -->
            @auth
            <x-post-forms />
            @endauth
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

        .tabsFilter {
            display: flex;
            justify-content: space-around;
            background-color: #ffffff;
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

        .activeFilter {
            background-color: #8a969c3f;
            /* correspond à bg-gray-700 */
            border-bottom: 3px solid #00000027;
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
            
            const lastFilter = localStorage.getItem('lastFilter') || 'newest'; // 'newest' par défaut
            applyFilter(lastFilter);
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

        let filterTimeout;
        function applyFilter(filter) {
            clearTimeout(filterTimeout);

            filterTimeout = setTimeout(() => {
                // Save filter to localStorage
                localStorage.setItem('lastFilter', filter);

                // Reset Active Class
                document.getElementById('newest-tab').classList.remove('activeFilter');
                document.getElementById('popular-tab').classList.remove('activeFilter');

                // Add Active Class to Selected Tab
                document.getElementById(filter + '-tab').classList.add('activeFilter');

                // Envoyer l'event pour reset le contenu des tabs
                const filterEvent = new CustomEvent('set-filter-option', {
                    detail: { option: filter }
                });

                this.dispatchEvent(filterEvent);
                this.dispatchEvent(
                    new Event('reset-post-views')
                );
            }, 200); // 200ms de délai
        }
    </script>
</x-app-layout>