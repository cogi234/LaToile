<div class="max-w-5xl mx-auto px-3 sm:px-8">
    <!-- Ajout des onglets -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-3 md:mb-5">
        <div class="tabs p-6 text-gray-900 dark:text-gray-100">
            <!-- Les blocs sont maintenant des liens entièrement cliquables -->
            <a href="javascript:void(0);" class="tab active" id="posts-tab" onclick="showContent('followed')">
                Abonnés 
            </a>
            <a href="javascript:void(0);" class="tab" id="users-tab" onclick="showContent('following')">
                Abonnements
            </a>
        </div>
    </div>

    <!-- Contenu associé aux onglets -->
    <div class="bg-transparent overflow-hidden">
        <div class="text-gray-900 dark:text-gray-100">
            <div id="followed-content" class="content-section" style="display: block;">
                <livewire:user.view-followed-users />
            </div>
            <div id="following-content" class="content-section" style="display: none;">
                <livewire:search.view-following-users />
            </div>
        </div>
    </div>
</div>
