<x-app-layout>
    <!-- Post forms -->
    @auth
    <x-post-forms />
    @endauth

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-post-view :post="$post" />
            
            {{-- admin page --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-3 md:mb-5 mt-14 xl:mt-0">
                <div class="tabs p-6 text-gray-900 dark:text-gray-100">
                    <!-- Les blocs sont maintenant des liens entièrement cliquables -->
                    <a href="javascript:void(0);" class="tab active" id="responses-tab" onclick="showContent('responses')">
                        Réponses
                    </a>
                    @auth
                    <a href="javascript:void(0);" class="tab" id="shares-tab" onclick="showContent('shares')">
                        Partages
                    </a>
                    <a href="javascript:void(0);" class="tab" id="likes-tab" onclick="showContent('likes')">
                        J'aimes
                    </a>
                    @endauth
                </div>
            </div>

            <div class="bg-transparent overflow-hidden">
                <div class="text-gray-900 dark:text-gray-100">
                    <div id="responses-content" class="content-section" style="display: block;">
                        <livewire:posts.viewresponses :post="$post" />
                    </div>
                    @auth
                    <div id="shares-content" class="content-section" style="display: none;">
                        <livewire:posts.viewshares :post="$post" />
                    </div>
                    <div id="likes-content" class="content-section" style="display: none;">
                        <livewire:posts.viewlikes :post="$post" />
                    </div>
                    @endauth
                </div>
            </div>

        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        // Charger l'onglet sélectionné précédemment
        const lastTab = localStorage.getItem('lastTabPost') || 'responses'; // 'allAdmin' par défaut
        showContent(lastTab);
    });

    function showContent(tab) {
        // Enregistrer l'onglet actif dans localStorage
        localStorage.setItem('lastTabPost', tab);

        
        // Envoyer l'event pour reset le contenu des tabs
        this.dispatchEvent(
            new Event('reset-post-views')
        );

        // Cacher tous les contenus
        document.getElementById('responses-content').style.display = 'none';
        document.getElementById('shares-content').style.display = 'none';
        document.getElementById('likes-content').style.display = 'none';

        // Enlever la classe active de tous les onglets
        document.getElementById('responses-tab').classList.remove('active');
        document.getElementById('shares-tab').classList.remove('active');
        document.getElementById('likes-tab').classList.remove('active');

        // Afficher la section sélectionnée et rendre l'onglet actif
        document.getElementById(tab + '-content').style.display = 'block';
        document.getElementById(tab + '-tab').classList.add('active');
    }
    </script>

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

</x-app-layout>