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
