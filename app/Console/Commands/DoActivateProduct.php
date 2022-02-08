<?php

namespace App\Console\Commands;

use App\Providers\InstallerServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DoActivateProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activate:product {code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activates the product';

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
     * Fixes PHP & JS code issues.
     *
     * @return mixed
     */
    public function handle()
    {
        $code = strtolower(str_replace('code=', '', $this->argument('code')));

        Storage::disk('local')->put('installed', 'Script installed');

        echo '[*]['.date('H:i:s')."] Product activated successfully.\r\n";
    }
}
