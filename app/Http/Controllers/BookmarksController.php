<?php

namespace App\Http\Controllers;

use App\Providers\AttachmentServiceProvider;
use App\Providers\PostsHelperServiceProvider;
use Cookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JavaScript;

class BookmarksController extends Controller
{
    /**
     * Available bookmark types.
     * @var array
     */
    public $bookmarkTypes = [
        'all' => ['heading' => 'All Bookmarks', 'icon' => 'bookmarks'],
        'photos' => ['heading' => 'Photos', 'icon' => 'image'],
        'videos' => ['heading' => 'Videos', 'icon' => 'videocam'],
        'audio' => ['heading' => 'Audio', 'icon' => 'musical-notes'],
        'other' => ['heading' => 'Other', 'icon' => 'person'],
        'locked' => ['heading' => 'Locked', 'icon' => 'lock-closed'],
    ];

    /**
     * Displays the default bookmarks view & available filters.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Avoid (browser) page caching when hitting back button
        header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
        header('Pragma: no-cache'); // HTTP 1.0.
        header('Expires: 0 '); // Proxies.

        $type = AttachmentServiceProvider::getActualTypeByBookmarkCategory($request->route('type'));

        $startPage = PostsHelperServiceProvider::getFeedStartPage(PostsHelperServiceProvider::getPrevPage($request));
        $posts = PostsHelperServiceProvider::getUserBookmarks(Auth::user()->id, false, $startPage, $type);
        PostsHelperServiceProvider::shouldDeletePaginationCookie($request);

        if ($request->method() == 'GET') {
            JavaScript::put([
                'paginatorConfig' => [
                    'next_page_url' =>$posts->nextPageUrl(),
                    'prev_page_url' => $posts->previousPageUrl(),
                    'current_page' => $posts->currentPage(),
                    'total' => $posts->total(),
                    'per_page' => $posts->perPage(),
                    'hasMore' => $posts->hasMorePages(),
                ],
                'initialPostIDs' => $posts->pluck('id')->toArray(),
            ]);

            return view('pages.bookmarks', [
                'posts' => $posts,
                'bookmarkTypes' => $this->bookmarkTypes,
                'activeTab' => $request->route('type'),
            ]);
        } else {
            return response()->json([
                'success'=>true,
                'data'=>PostsHelperServiceProvider::getUserBookmarks(Auth::user()->id, true, false, $type),
            ]);
        }
    }
}
