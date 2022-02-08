<?php

namespace App\Providers;

use App\Model\PublicPage;
use App\Product;
use App\User;
use Illuminate\Support\ServiceProvider;

class SitemapServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Generates a sitemap for user profiles and public pages.
     * @return bool
     */
    public static function generateSitemap()
    {
        $creators = User::where('public_profile', 1)->orderByDesc('created_at')->get();
        $publicPages = PublicPage::all();

        $sitemapData = '
        <?xml version="1.0" encoding="UTF-8"?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Items
        foreach ($creators as $user) {
            $sitemapData .= '
           <url>
              <loc>'.route('profile', ['username' => $user->username]).'</loc>
              <lastmod>'.$user->updated_at->format('Y-m-d').'</lastmod>
              <changefreq>daily</changefreq>
              <priority>0.8</priority>
           </url>
            ';
        }

        // Items
        foreach ($publicPages as $page) {
            $sitemapData .= '
           <url>
              <loc>'.route('pages.get', ['slug' => $page->slug]).'</loc>
              <lastmod>'.$page->updated_at->format('Y-m-d').'</lastmod>
              <changefreq>daily</changefreq>
              <priority>0.8</priority>
           </url>
            ';
        }

        $sitemapData .= '</urlset> ';
        file_put_contents(public_path('sitemap.xml'), trim($sitemapData));

        return true;
    }
}
