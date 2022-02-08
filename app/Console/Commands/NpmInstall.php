<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NpmInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'npm:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs client side dependencies within public folder';

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
     * Installs client side dependencies within public folder.
     *
     * @return mixed
     */
    public function handle()
    {
        echo '[*]['.date('H:i:s')."] Installing dependencies.\r\n";
        exec('npm i');
        $this->call('npm:publish');
    }
}
