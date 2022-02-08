<?php

namespace App\Console\Commands;

use App\Providers\SitemapServiceProvider as SiteMapGenerator;
use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generateSitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates the sitemap';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generates the sitemap.
     *
     * @return mixed
     */
    public function handle()
    {
        if (SiteMapGenerator::generateSitemap()) {
            echo '[*]['.date('H:i:s').']Sitemap generated successfully.';
        } else {
            echo '[*]['.date('H:i:s').']Sitemap generation failed';
        }
    }
}
