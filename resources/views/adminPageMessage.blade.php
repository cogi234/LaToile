<x-app-layout>
    <!-- Post forms -->
    @auth
    <x-post-forms />
    @endauth

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- admin page -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-3 md:mb-5 mt-14 xl:mt-0">
                <div class="tabs p-6 text-gray-900 dark:text-gray-100 flex">
                    <a href="javascript:void(0);" class="tab flex-1" id="allAdmin-tab" data-target="allAdmin-content">
                        Message à traiter
                    </a>
                    <a href="javascript:void(0);" class="tab flex-1" id="messageTraiter-tab" data-target="messageTraiter-content">
                        Message traité
                    </a>
                    <a href="javascript:void(0);" class="tab flex-1" id="UtilisateurBanni-tab" data-target="UtilisateursBannis-content">
                        Utilisateurs bannis (pour un message)
                    </a>
                </div>
            </div>

            <div class="bg-transparent overflow-hidden">
                <div class="text-gray-900 dark:text-gray-100">
                    <div id="allAdmin-content" class="content-section content-active">
                        <livewire:admin.viewallMessage />
                    </div>
                    <div id="messageTraiter-content" class="content-section">
                        <livewire:admin.viewallMessageTraiter />
                    </div>
                    <div id="UtilisateursBannis-content" class="content-section">
                        <livewire:admin.viewall-banned/>
                    </div>
                </div>
            </div>

        </div>
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

        .content-active {
            display: block; /* Ensure only the active content section is displayed */
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.tab');
            const contents = document.querySelectorAll('.content-section');

            // Récupérer l'onglet actif depuis localStorage
            const activeTab = localStorage.getItem('activeTab') || 'allAdmin-tab';

            // Fonction pour afficher le contenu de l'onglet actif
            function showTab(tabId) {
                // Remove active class from all tabs and contents
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('content-active'));

                // Add active class to the clicked tab and corresponding content
                const activeTabElement = document.getElementById(tabId);
                activeTabElement.classList.add('active');
                const target = document.getElementById(activeTabElement.getAttribute('data-target'));
                target.classList.add('content-active');
            }

            // Afficher l'onglet actif lors du chargement de la page
            showTab(activeTab);

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Enregistrer l'onglet actif dans localStorage
                    localStorage.setItem('activeTab', tab.id);
                    showTab(tab.id); // Afficher le nouvel onglet
                });
            });

            // Envoyer l'event pour reset le contenu des tabs
            this.dispatchEvent(new Event('reset-messages-views'));
        });
    </script>
</x-app-layout>
