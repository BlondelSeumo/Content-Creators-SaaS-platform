const mix = require('laravel-mix');
require('laravel-mix-purgecss');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .sass('resources/sass/bootstrap.scss', 'public/css/theme')
    .sass('resources/sass/bootstrap.rtl.scss', 'public/css/theme')
    .sass('resources/sass/bootstrap.dark.scss', 'public/css/theme')
    .sass('resources/sass/bootstrap.rtl.dark.scss', 'public/css/theme');

