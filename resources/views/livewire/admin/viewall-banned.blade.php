<?php

use Livewire\Volt\Component;
use App\Models\Ban; 
use App\Models\User;
use Livewire\Attributes\On;
use Carbon\Carbon;

new class extends Component {
    public $bans;
    public $posts;
    public $currentAdminPage = "adminPage";
    

    public function mount()
    {
        if (request()->routeIs('adminPage')) {
            $this->currentAdminPage = "Report";
            $this->bans = Ban::with('user')
            ->where('report_type', 'Report')
            ->get();
        } else {
            $this->currentAdminPage = "ReportMessage";
            $this->bans = Ban::with('user')
            ->where('report_type', 'ReportMessage')
            ->get();
        }
    }
};

?>

<div>
    @if($bans->isEmpty())
        <p>Aucun ban n'a été trouvé pour ce type de bannissement.</p>
    @else
        <table class="w-full">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Raison</th>
                    <th>Date de bannissement</th>
                    <th>Date de fin</th>
                </tr>
            </thead>
            <body>
                @foreach($bans as $ban)
                    <tr class="p-[15px] border-2 border-gray-300/50">
                        <td class="px-10 text-center">
                            <a class="flex flex-row items-center hover:font-bold hover:underline" href="/user/{{$ban->user->id}}">
                                <img src="{{ \App\Models\User::find($ban->user->id)->getAvatar() }}" alt="Image de profil"
                                    class="w-8 h-8 rounded-full mr-2 shadow-lg hover:outline hover:outline-2 hover:outline-black/10">
                                {{ $ban->user ? $ban->user->name : 'Utilisateur inconnu' }}
                            </a>
                        </td>
                        <td class="px-10 text-wrap items-center" style="max-width: 250px; word-break: break-word;">
                            {{ $ban->reason }}
                        </td>
                        @php
                            Carbon::setLocale('fr');
                            $dateDebut = $ban->created_at ? Carbon::createFromFormat('Y-m-d H:i:s', $ban->created_at) : null;
                            $dateFin = $ban->end_time ? Carbon::createFromFormat('Y-m-d', $ban->end_time) : null;
                        @endphp
                        <td class="px-10 text-center">{{ $dateDebut ? $dateDebut->translatedFormat('d F Y \à H:i') : 'N/A' }}</td>
                        <td class="px-10 text-center">{{ $dateFin ? $dateFin->translatedFormat('d F Y') : 'PERMANENT' }}</td>
                        <!-- débannir l'utilisateur -->
                        <td><livewire:admin.unban :userId="$ban->user_id" :reportType="'{{$currentAdminPage}}'" /></td>
                    </tr>
                @endforeach
            </body>
        </table>
    @endif
</div>

