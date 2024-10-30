<x-app-layout>
    <livewire:messages.report />
    <livewire:messages.private-messages :targetUserId="$targetUserId" />

    

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
    </style>
</x-app-layout>
    
