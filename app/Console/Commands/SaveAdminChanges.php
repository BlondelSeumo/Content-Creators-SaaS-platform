<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SaveAdminChanges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:save';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Saves admin settings to laravel seeds';

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
     * Saves admin settings to laravel seeds.
     *
     * @return mixed
     */
    public function handle()
    {
        echo '[*]['.date('H:i:s')."] Creating db seeds.\r\n";
        exec('php artisan iseed data_types,data_rows,menus,menu_items,roles,permissions,permission_role,user_roles,settings,public_pages --force');
        echo '[*]['.date('H:i:s')."] Updating seeds namespaces.\r\n";
        $files = scandir(app_path().'/../database/seeds');
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $this->replaceInFile(app_path().'/../database/seeds/'.$file, 'namespace Database\Seeders;', 'namespace Database\Seeds;', false);
            }
        }
        echo '[*]['.date('H:i:s')."] Dumping autoload classes.\r\n";
        exec('composer dump-autoload');
    }

    protected function replaceInFile($file, $match, $replace, $recursive = true)
    {
        $fileContent = file_get_contents($file);
        if ($recursive == true) {
            while (is_int(strpos($fileContent, $match))) {
                $fileContent = str_replace($match, $replace, $fileContent);
            }
        } else {
            $fileContent = str_replace($match, $replace, $fileContent);
        }
        file_put_contents($file, $fileContent);
    }
}
