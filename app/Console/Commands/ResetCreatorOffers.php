<?php

namespace App\Console\Commands;

use App\Model\CreatorOffer;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResetCreatorOffers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:resetOffers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CJ to clear expired user offers automatically';

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
     * CJ to clear expired user offers automatically.
     *
     * @return mixed
     */
    public function handle()
    {
        echo '[*]['.date('H:i:s')."] Processing expired user offers.\r\n";
        $offers = CreatorOffer::all();
        $current_time = Carbon::now();
        foreach ($offers as $offer) {
            $interval = strtotime($offer->expires_at) - strtotime($current_time);
            if ($interval <= 0) {
                User::find($offer->user_id)->update([
                    'profile_access_price' => $offer->old_profile_access_price,
                    'profile_access_price_6_months' => $offer->old_profile_access_price_6_months,
                    'profile_access_price_12_months' => $offer->old_profile_access_price_12_months,
                ]);
                CreatorOffer::find($offer->id)->delete();
            }
        }
        echo '[*]['.date('H:i:s')."] Expired offers cleared.\r\n";
    }
}
