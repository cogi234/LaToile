<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Post forms -->
            @auth
            <x-post-forms />
            @endauth

            <livewire:drafts.delete />
            
            {{-- admin page --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-3 md:mb-5 mt-14 xl:mt-0">
                <div class="tabs p-6 text-gray-900 dark:text-gray-100">
                    <!-- Les blocs sont maintenant des liens entièrement cliquables -->
                    <a href="javascript:void(0);" class="tab active" id="allAdmin-tab" onclick="showContent('allAdmin')">
                        Post a traiter
                    </a>
                    @auth
                    <a href="javascript:void(0);" class="tab" id="postTraiter-tab" onclick="showContent('postTraiter')">
                        post traiter
                    </a>
                    <a href="javascript:void(0);" class="tab" id="UtilisateurBanni-tab" onclick="showContent('UtilisateurBanni')">
                        Utilisateur Banni
                    </a>
                    @endauth
                </div>
            </div>

            <div class="bg-transparent overflow-hidden">
                <div class="text-gray-900 dark:text-gray-100">
                    <div id="allAdmin-content" class="content-section" style="display: block;">
                        <livewire:admin.viewall />
                    </div>
                    @auth
                    <div id="postTraiter-content" class="content-section" style="display: none;">
                        <livewire:admin.viewall-traiter/>
                    </div>
                    <div id="UtilisateurBanni-content" class="content-section" style="display: none;">
                        <livewire:admin.viewall-banned/>
                    </div>
                    @endauth
                </div>
            </div>

        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        // Charger l'onglet sélectionné précédemment
        const lastTabAdmin = localStorage.getItem('lastTabAdmin') || 'allAdmin'; // 'allAdmin' par défaut
        showContent(lastTabAdmin);
    });

    function showContent(tab) {
        // Enregistrer l'onglet actif dans localStorage
        localStorage.setItem('lastTabAdmin', tab);

        
        // Envoyer l'event pour reset le contenu des tabs
        this.dispatchEvent(
            new Event('reset-post-views')
        );

        // Cacher tous les contenus
        document.getElementById('allAdmin-content').style.display = 'none';
        @auth
        document.getElementById('postTraiter-content').style.display = 'none';
        document.getElementById('UtilisateurBanni-content').style.display = 'none';
        @endauth

        // Enlever la classe active de tous les onglets
        document.getElementById('allAdmin-tab').classList.remove('active');
        @auth
        document.getElementById('postTraiter-tab').classList.remove('active');
        document.getElementById('UtilisateurBanni-tab').classList.remove('active');
        @endauth

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