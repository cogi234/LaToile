<x-app-layout>
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
        main{
            height: calc(100vh - 4rem);
        }
    
        #discussion::-webkit-scrollbar {
            width: 0;
            height: 0;
        }
    
        #discussion {
            scrollbar-width: none;
        }
    
        #discussion {
            -ms-overflow-style: none;
            overflow-y: scroll;
        }
        
        .focus-bg-white {
            background-color: white;
            border: 3px solid #2563eb;
        }
    </style>
</x-app-layout>
    
