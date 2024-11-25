{{-- 
    - Améliorer le layout pour voir les tags followed

--}}
{{-- <x-app-layout>
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
        //     if(tab == 'followers'){
        //         $viewFollowers = true;
        //     } else{
        //         $viewFollowers = false;
        //     }

        //     //Envoyer l'event pour reset le contenu des tabs
        //     this.dispatchEvent(
        //         new Event('reset-post-views')
        //     );

        //     // Cacher tous les contenus
        //     document.getElementById('followers-content').style.display = 'none';
        //     document.getElementById('following-content').style.display = 'none';

        //     // Enlever la classe active de tous les onglets
        //     document.getElementById('followers-tab').classList.remove('active');
        //     document.getElementById('following-tab').classList.remove('active');

        //     // document.getElementById(tab + '-content').style.display = 'block';
        //     // document.getElementById(tab + '-tab').classList.add('active');

        //     // Afficher la section sélectionnée et rendre l'onglet actif
        //     if($viewFollowers){
        //         document.getElementById('followers-content').style.display = 'block';
        //         document.getElementById('followers-tab').classList.add('active');
        //     } else {
        //         document.getElementById('following-content').style.display = 'block';
        //         document.getElementById('following-tab').classList.add('active');
        //     }
        // }

        window.onload = function() {
        // S'assurer que tous les éléments sont chargés avant d'essayer de les manipuler
        const followersTab = document.getElementById('followers-tab');
        const followingTab = document.getElementById('following-tab');
        const followersContent = document.getElementById('followers-content');
        const followingContent = document.getElementById('following-content');
        
        if (followersTab && followingTab && followersContent && followingContent) {
            const currentTab = window.location.pathname.split('/').pop();

            // Afficher l'onglet actif en fonction de l'URL
            if (currentTab === 'followers') {
                showContent('followers');
            } else if (currentTab === 'followings') {
                showContent('followings');
            } else {
                showContent('followers');
            }
        }
    }

    function showContent(tab) {
        // Met à jour l'URL sans recharger la page
        const url = window.location.pathname.split('/');
        url[url.length - 1] = tab;
        window.history.pushState({}, '', url.join('/'));

        // Cacher tous les contenus
        const followersContent = document.getElementById('followers-content');
        const followingContent = document.getElementById('following-content');
        const followersTab = document.getElementById('followers-tab');
        const followingTab = document.getElementById('following-tab');

        if (followersContent && followingContent && followersTab && followingTab) {
            followersContent.style.display = 'none';
            followingContent.style.display = 'none';

            // Enlever la classe active de tous les onglets
            followersTab.classList.remove('active');
            followingTab.classList.remove('active');

            // Afficher la section sélectionnée et rendre l'onglet actif
            if (tab === 'followers') {
                followersContent.style.display = 'block';
                followersTab.classList.add('active');
            } else {
                followingContent.style.display = 'block';
                followingTab.classList.add('active');
            }
        }
    }

    </script>
</x-app-layout> --}}

<x-app-layout>
    <div class="py-6">
        <x-follow-view :user="$user" :viewFollowers="$viewFollowers"/>
    </div>

    <style>
        .tabs {
            display: flex;
            justify-content: space-around;
            background-color: #2d3748;
            padding: 10px;
        }

        .tab {
            cursor: pointer;
            padding: 10px;
            color: white;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            flex-grow: 1;
            border-radius: 8px;
            transition: 0.3s ease;
        }

        .tab:hover {
            background-color: #4a5568;
        }

        .active {
            background-color: #4a5568;
            border-bottom: 3px solid #ff6961;
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
        function showContent(tab) {
            const url = window.location.pathname.split('/');
            url[url.length - 1] = tab;
            window.history.pushState({}, '', url.join('/'));

            // Cacher tous les contenus et enlever la classe active des onglets
            document.getElementById('followers-content').classList.remove('active');
            document.getElementById('following-content').classList.remove('active');
            document.getElementById('followers-tab').classList.remove('active');
            document.getElementById('following-tab').classList.remove('active');

            // Afficher la section correspondante et ajouter la classe active aux onglets
            if (tab === 'followers') {
                document.getElementById('followers-content').classList.add('active');
                document.getElementById('followers-tab').classList.add('active');
            } else {
                document.getElementById('following-content').classList.add('active');
                document.getElementById('following-tab').classList.add('active');
            }
        }

        window.onload = function() {
            const currentTab = window.location.pathname.split('/').pop();
            showContent(currentTab);
        };
    </script>
</x-app-layout>
