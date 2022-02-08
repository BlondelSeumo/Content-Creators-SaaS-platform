<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatsController extends Controller
{
    /**
     * Sends a data beacon on page exit | Can be used for collecting stats / last active page, etc
     * TODO: Can be deleted / left for future usage.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendBeacon(Request $request)
    {
        Log::debug('An informational message.');
        $type = $request->route('type');

        if ($type == 'post') {
            $postID = $request->get('id');
            $created_at = $request->get('created_at');
            Cookie::queue('app_prev_post', json_encode(['post_id'=>$postID, 'created_at'=>$created_at]));
        } elseif ($type == 'feed') {
            $prev_page = $request->get('prevPage');
            Log::debug($prev_page);
            Cookie::queue('app_feed_prev_page', $prev_page, 356, null, null, null, false, false, null);
        } else {
            return response()->json(['success' => false, 'message' => __('Beacon not valid')], 404);
        }

        return response()->json(['success' => true, 'message' => __('Beacon accepted')], 200);
    }
}
