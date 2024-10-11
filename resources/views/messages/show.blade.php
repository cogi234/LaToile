<x-app-layout>
    @auth
        <livewire:messages.messageBoard :targetUserId="$targetUserId" :currentUserId="$currentUserId" />
    @endauth
</x-app-layout>
