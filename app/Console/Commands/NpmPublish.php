<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NpmPublish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'npm:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publishes client side dependencies within public folder';

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
     * Publishes client side dependencies within public folder.
     *
     * @return mixed
     */
    public function handle()
    {
        if (PHP_OS == 'WINNT') {
            exec('rmdir "public/libs" /Q/S');
        } else {
            exec('rm -rf "public/libs"');
        }
        $deps = json_decode(file_get_contents('package.json'));
        $deps = $deps->dependencies;
        echo '[*]['.date('H:i:s')."] Publishing prod assets.\r\n";
        foreach ($deps as $dep => $version) {
            $cmd = "mkdir -p public/libs/$dep && cp -r node_modules/$dep/* public/libs/$dep/";
            if (PHP_OS == 'WINNT') {
                $cmd = "robocopy node_modules/$dep public/libs/$dep /s /e";
            }
            exec($cmd);
            echo '[-] ['.date('H:i:s')."] Successfully published $dep under public directory.\r\n";
        }
    }
}
