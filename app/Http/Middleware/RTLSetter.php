<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Cookie;

class RTLSetter extends Middleware
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

        $actionName = $request->route()->getActionMethod();
        if (in_array($actionName, [/*Skipped routes*/]) || (is_null(Cookie::get('app_rtl')) || Cookie::get('app_rtl') == 'ltr')) {
            return $response;
        }

        $buffer = $response->getContent();

        $replacePreps = [
            // Margins
            '/mr-/' => 'mtl-',
            '/ml-/' => 'mtr-',
            // Paddings
            '/pr-/' => 'ptl-',
            '/pl-/' => 'ptr-',
            // Flexs
            '/flex-row/' => 'flexr-',
            '/flex-row-reverse/' => 'flexrr-',
            '/flex-row-no-rtl/' => 'flex-row-nortl'
        ];

        $replace = [
            // Margins
            '/mtl-/' => 'ml-',
            '/mtr-/' => 'mr-',
            // Paddings
            '/ptr-/' => 'pr-',
            '/ptl-/' => 'pl-',
            // Flexs
            '/flexr-/' => 'flex-row-reverse',
            '/flexrr-/' => 'flex-row',
            '/flex-row-nortl/'=> 'flex-row'
        ];

        $buffer = preg_replace(array_keys($replacePreps), array_values($replacePreps), $buffer);
        $buffer = preg_replace(array_keys($replace), array_values($replace), $buffer);

        $response->setContent($buffer);

        return $response;
    }
}
