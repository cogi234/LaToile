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

            // Vérifiez s'il est banni
            $isBanned = Ban::where('user_id', $user->id)
                ->where('end_time', '>', now()) // Voir si bannissement est toujours actif
                ->exists();

            if ($isBanned) {
                // Déconnectez l'utilisateur et redirigé vers une page de ban
                return redirect()->route('banned');
            }
        }
        return $next($request);
    }
}
