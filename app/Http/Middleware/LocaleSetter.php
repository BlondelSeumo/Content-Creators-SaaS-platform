<?php

namespace App\Http\Middleware;

use App\Providers\InstallerServiceProvider;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class LocaleSetter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! Session::has('locale')) {
            if (InstallerServiceProvider::checkIfInstalled()) {
                Session::put('locale', getSetting('site.default_site_language'));
            } else {
                Session::put('locale', Config::get('app.locale'));
            }
        }

        if (isset(Auth::user()->settings['locale'])) {
            App::setLocale(Auth::user()->settings['locale']);
        } else {
            App::setLocale(getSetting('site.default_site_language')); // If user has missing locale setting - default on site setting
        }

        // Prepping the translation files for frontend usage
        $langPath = resource_path('lang/'.App::getLocale());
        if (env('APP_ENV') == 'production') {
            Cache::rememberForever('translations', function () use ($langPath) {
                return file_get_contents($langPath.'.json');
            });
        } else {
            if(!file_exists($langPath.'.json')){
                $langPath = resource_path('lang/en');
            }
            Cache::remember('translations', 5, function () use ($langPath) {
                return file_get_contents($langPath.'.json');
            });
        }

        return $next($request);
    }
}
