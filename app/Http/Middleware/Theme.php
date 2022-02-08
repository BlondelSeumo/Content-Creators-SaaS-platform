<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Cookie;

class Theme extends Middleware
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
        $response = $next($request);

        $routeName = $request->route()->getName();
        if (in_array($routeName, ['voyager.voyager_assets'])) {
            return $response;
        }

        $buffer = $response->getContent();

        $replacePreps = [
            '/text-dark-r/' => 'text-dark-tbr',
            '/text-white-r/' => 'text-white-tbr',
            '/text-muted-r/' => 'text-muted-tbr',
            '/text-light-r/' => 'text-light-tbr',
            '/btn-outline-dark-r/' => 'btn-outline-dark-tbr',
            '/btn-outline-light-r/' => 'btn-outline-light-tbr',
        ];
        $mode = Cookie::get('app_theme');
        if(!$mode){
            $mode = getSetting('site.default_user_theme');
        }
        if ($mode == 'light') {
            $replace = [
                '/text-dark-tbr/' => 'text-dark',
                '/text-white-tbr/' => 'text-white',
                '/text-muted-tbr/' => 'text-muted',
                '/text-light-tbr/' => 'text-light',
                '/btn-outline-dark-tbr/' => 'btn-outline-dark',
                '/btn-outline-light-tbr/' => 'btn-outline-light',
            ];
        } else {
            $replace = [
                '/text-dark-tbr/' => 'text-white',
                '/text-white-tbr/' => 'text-dark',
                '/text-muted-tbr/' => 'text-light',
                '/text-light-tbr/' => 'text-muted',
                '/btn-outline-dark-tbr/' => 'btn-outline-light',
                '/btn-outline-light-tbr/' => 'btn-outline-dark',
            ];
        }

        $buffer = preg_replace(array_keys($replacePreps), array_values($replacePreps), $buffer);
        $buffer = preg_replace(array_keys($replace), array_values($replace), $buffer);

        $response->setContent($buffer);

        return $response;
    }
}
