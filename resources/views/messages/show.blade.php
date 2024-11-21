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
    
