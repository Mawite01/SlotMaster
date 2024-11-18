<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;


class AdminLogoMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $logoFilename = Auth::user()->agent_logo;
            $siteName = Auth::user()->site_name ?? 'DelightMyanmar'; // Default site name

            //Log::info('Auth User Logo:', ['logo' => $logoFilename]);
            //Log::info('Site Name:', ['site_name' => $siteName]);

            $adminLogo = $logoFilename
                ? asset('assets/img/logo/' . $logoFilename)
                : asset('assets/img/logo/default-logo.jpg');

            //Log::info('Admin Logo Path:', ['path' => $adminLogo]);

            View::share([
                'adminLogo' => $adminLogo,
                'siteName' => $siteName, // Share site name globally
            ]);
        }

        return $next($request);
    }

    // public function handle($request, Closure $next)
    // {
    //     if (Auth::check()) {
    //          $logoFilename = Auth::user()->agent_logo;
    // Log::info('Auth User Logo:', ['logo' => $logoFilename]);
    //         $adminLogo = Auth::user()->agent_logo ? asset('assets/img/logo/' . Auth::user()->agent_logo) : asset('assets/img/logo/default-logo.jpg');
    // Log::info('Admin Logo Path:', ['path' => $adminLogo]);
    //         View::share('adminLogo', $adminLogo);
    //     }

    //     return $next($request);
    // }
}