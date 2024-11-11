<x-app-layout>
    <div class="py-6">
        <x-follow-view :user="$user" :viewFollowers="$viewFollowers"/>
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

        #dropdownMenu::-webkit-scrollbar {
            width: 8px;
        }

        #dropdownMenu::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }

        .content-section {
            transition: opacity 0.5s ease-in-out;
            opacity: 0;
        }

        .content-section[style*="display: block"] {
            opacity: 1;
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

        // function showContent(tab) {
        //     //Envoyer l'event pour reset le contenu des tabs
        //     this.dispatchEvent(
        //         new Event('reset-post-views')
        //     );

        //     // Cacher tous les contenus
        //     document.getElementById('followed-content').style.display = 'none';
        //     document.getElementById('following-content').style.display = 'none';

        //     // Enlever la classe active de tous les onglets
        //     document.getElementById('followed-tab').classList.remove('active');
        //     document.getElementById('following-tab').classList.remove('active');

        //     // Afficher la section sélectionnée et rendre l'onglet actif
        //     document.getElementById(tab + '-content').style.display = 'block';
        //     document.getElementById(tab + '-tab').classList.add('active');
        // }
        function showContent(tabName) {
            const tabs = document.querySelectorAll('.content-section');
            const activeTab = document.querySelector(`#${tabName}-content`);

            // Masquer tous les contenus
            tabs.forEach(tab => {
                tab.classList.add('hidden');
            });

            // Afficher le contenu actif
            activeTab.classList.remove('hidden');
            activeTab.classList.add('active');

            // Mettre à jour l'état des onglets
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });

            document.getElementById(`${tabName}-tab`).classList.add('active');
        }

        function showContent(tabName) {
            const tabs = document.querySelectorAll('.content-section');
            const activeTab = document.querySelector(`#${tabName}-content`);

            // Masquer tous les contenus
            tabs.forEach(tab => {
                tab.style.display = 'none';
            });

            // Afficher le contenu actif
            activeTab.style.display = 'block';

            // Mettre à jour l'état des onglets
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });

            document.getElementById(`${tabName}-tab`).classList.add('active');
        }

    </script>
</x-app-layout>