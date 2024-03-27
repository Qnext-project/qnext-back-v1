<?php

namespace App\Http\Middleware;

use App\Models\Clinic;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetClinicMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $clinic = Clinic::find($user->clinic_id);
        $request->session()->put(['clinic' => $clinic]);
        return $next($request);
    }
}
