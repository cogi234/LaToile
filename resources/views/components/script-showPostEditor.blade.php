<script>
    function showPostEditor(postId = -1) {
        //Envoyer l'event pour activer le post editor
        if (postId < 0) {
            this.dispatchEvent(
                new Event('open-post-editor')
            );
        } else {
            this.dispatchEvent(
                new CustomEvent('open-post-editor', {
                    detail: {
                        sharedId: postId
                    }
                })
            );
        }
    }
</script>