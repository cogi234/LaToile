<x-app-layout>
    @auth
        <livewire:messages.messageBoard :targetUserId="$targetUserId" />
    @endauth
</x-app-layout>
