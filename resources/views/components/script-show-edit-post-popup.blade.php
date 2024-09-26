<script>
    function openEditPopup(actionURL, postContent) {
        document.getElementById('editPostForm').action = actionURL;
        // Décoder le contenu du post et mettre à jour le champ de texte
        let contentArray = Array.isArray(postContent) ? postContent : JSON.parse(postContent);
        let textContent = contentArray[0] && contentArray[0].content ? contentArray[0].content : '';

        document.getElementById('postContent').value = textContent;

        document.getElementById('editPostModal').classList.remove('hidden');
    }

    // Fonction pour fermer le modal
    function closeEditPopup() {
        document.getElementById('editPostModal').classList.add('hidden');
    }

    function openDeleteConfirmationPopup(actionURL) {
        document.getElementById('deletePostForm').action = actionURL;
        document.getElementById('deleteConfirmationModal').classList.remove('hidden');
    }

    // Fonction pour fermer le modal
    function closeDeleteConfirmationPopup() {
        document.getElementById('deleteConfirmationModal').classList.add('hidden');
    }

    function copyToClipboard(postId) {
        // Génère l'URL du post
        var copyText = location.origin + "/post/" + postId;

        // Vérifiez si l'API clipboard est disponible
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(copyText).then(function() {

                // Afficher le message de succès
                const message = document.getElementById('clipboardMessage' + postId);
                message.classList.remove('hidden');
                message.classList.add('block');

                // Masquer le message après 2 secondes
                setTimeout(function() {
                    message.classList.remove('block');
                    message.classList.add('hidden');
                }, 2000);
            }).catch(function(error) {
                console.error('Erreur lors de la copie :', error);
            });
        }
    }
</script>