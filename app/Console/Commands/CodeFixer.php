<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class CodeFixer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'code:fix {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes PHP & JS code issues';

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
        $type = strtolower(str_replace('type=', '', $this->argument('type')));
        echo '[*]['.date('H:i:s')."] Fixing code errors on {$type} side\r\n";
        if ($type == 'php') {
            $process = new Process('php ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --config php-cs-fixer.php');
            $process->run();
            echo $process->getOutput();
        } elseif ($type == 'js') {
            $process = new Process('.\node_modules\.bin\eslint  --fix  .\public\js');
            $process->run();
            echo $process->getOutput();
        }
        echo '[*]['.date('H:i:s')."] Fixes done\r\n";
    }
}
