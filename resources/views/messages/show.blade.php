<x-app-layout showFooter="false">
    <x-slot:showFooter>false</x-slot>

    <livewire:messages.report />
        @if ($isGroup)
                <livewire:messages.group-messages :targetGroupId="$targetGroupId" />
        @else
                <livewire:messages.private-messages :targetUserId="$targetUserId" />
        @endif

    <script>
        function toggleMembersMenu() {
            this.dispatchEvent(
                new CustomEvent('open-member-menu')
            );
        }
        
        function toggleInvitesMenu() {
            this.dispatchEvent(
                new CustomEvent('open-invite-menu')
            );
        }

        function toggleGroupNameMenu() {
            this.dispatchEvent(
                new CustomEvent('open-groupName-menu')
            );
        }

        function copyToClipboard(elementId) {
            const textElement = document.getElementById(elementId);

            if (textElement) {
                const rawContent = textElement.textContent || textElement.innerText;

                const tempContainer = document.createElement('div');
                tempContainer.innerHTML = rawContent;

                const anchorTags = tempContainer.querySelectorAll('a');

                anchorTags.forEach(anchor => {
                    const href = anchor.getAttribute('href');
                    anchor.replaceWith(href);
                });

                const textToCopy = tempContainer.textContent || tempContainer.innerText;

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(textToCopy).then(() => {
                        // alert('Message copié dans le presse-papier !');
                    }).catch(err => {
                        console.error('Erreur lors de la copie : ', err);
                        alert('Échec de la copie.');
                    });
                } else {
                    // Fallback pour environnements non sécurisés
                    const textarea = document.createElement('textarea');
                    textarea.value = textToCopy;
                    textarea.style.position = 'fixed';
                    textarea.style.opacity = 0;
                    document.body.appendChild(textarea);
                    textarea.select();

                    try {
                        document.execCommand('copy');
                        // alert('Message copié !');
                    } catch (err) {
                        console.error('Erreur lors de la copie : ', err);
                        alert('Échec de la copie.');
                    }

                    document.body.removeChild(textarea);
                }
            } else {
                alert('Élément introuvable.');
            }
        }
    </script>
    
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }

        main {
            height: calc(100vh - 4rem);
            overflow-y: auto;
        }

        #discussion::-webkit-scrollbar {
            width: 0;
            height: 0;
        }
    
        #discussion {
            scrollbar-width: none;
            -ms-overflow-style: none;
            overflow-y: scroll;
        }

        .min-h-screen{
            max-height: fit-content !important;
            min-height: fit-content !important;
        }
    
        .focus-bg-white {
            background-color: white;
            border: 3px solid #2563eb;
        }
    </style>
</x-app-layout>
    
