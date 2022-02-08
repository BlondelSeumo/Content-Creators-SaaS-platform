<?php

namespace App\Http\Controllers;

use App\Admin\Dashboard\Metrics\Trend;
use App\Admin\Dashboard\Metrics\Value;
use App\Model\Subscription;
use App\User;
use Illuminate\Http\Request;

class MetricsController extends Controller
{
    public function newUsersValue(Request $request)
    {
        $users = (new Value())->get(User::class, $request->input('function'), $request->input('range'), 'id', 'created_at');
        return response()->json($users);
    }

    public function newUsersTrend(Request $request)
    {
        $users = (new Trend())->get(User::class, $request->input('function'), $request->input('unit'), $request->input('range'), 'id', 'created_at');
        return response()->json($users);
    }

    public function newUsersPartition(Request $request)
    {
        $c = User::count();
        $cc = User::whereRaw('email_verified_at IS NOT NULL')->count();
        $nc = $c - $cc;
        return response()->json(['values'=>['Confirmed' => $cc,'Not Confirmed' => $nc]]);
    }

    public function subscriptionsValue(Request $request)
    {
        $users = (new Value())->get(Subscription::class, $request->input('function'), $request->input('range'), 'id', 'created_at');
        return response()->json($users);
    }

    public function subscriptionsTrend(Request $request)
    {
        $users = (new Trend())->get(Subscription::class, $request->input('function'), $request->input('unit'), $request->input('range'), 'id', 'created_at');
        return response()->json($users);
    }

    public function subscriptionsPartition(Request $request)
    {
        $totalSubscriptionsCount = Subscription::count();
        $activeSubscriptionsCount = Subscription::where('expires_at', '>', new \DateTime('now', new \DateTimeZone('UTC')))->count();
        $expiredSubscriptionsCount = $totalSubscriptionsCount - $activeSubscriptionsCount;
        return response()->json(['values'=>['Active' => $activeSubscriptionsCount,'Expired' => $expiredSubscriptionsCount]]);
    }
}