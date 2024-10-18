<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Ban;

class CheckBan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !Auth::user()->moderator) {
            // Récupérez l'utilisateur connecté
            $user = Auth::user();

            // Vérifiez s'il a un bannissement actif
            $ban = Ban::where('user_id', $user->id)->first();

            // Si un bannissement existe, vérifiez sa date de fin
            if ($ban && $ban->end_time > now()) {
                return redirect()->route('banned');
            } else if ($ban) {
                $ban->delete(); // Supprimez le bannissement de la base de données
            }
        }
        return $next($request);
    }
}
