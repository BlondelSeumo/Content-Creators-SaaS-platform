<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class CodeChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'code:check {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for PHP or JS errors and generates a report';

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
     * Checks for PHP or JS errors and generates a report.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = strtolower(str_replace('type=', '', $this->argument('type')));
        echo '[*]['.date('H:i:s')."] Checking for code errors on {$type} side\r\n";
        if ($type == 'php') {
            $process = new Process('php ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --config php-cs-fixer.php --allow-risky=yes --dry-run --verbose');
            $process->run();
            echo $process->getOutput();
        } elseif ($type == 'js') {
            $process = new Process('.\node_modules\.bin\eslint  --fix-dry-run  .\public\js');
            $process->run();
            echo $process->getOutput();
        }
        echo '[*]['.date('H:i:s')."] Report done\r\n";
    }
}
