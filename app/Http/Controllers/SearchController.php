<?php

namespace App\Http\Controllers;

use App\Providers\MembersHelperServiceProvider;
use App\Providers\PostsHelperServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JavaScript;

class SearchController extends Controller
{

    /**
     * Available search categories
     * @var array
     */
    public $filters = [
        'top',
        'latest',
        'people',
        'photos',
        'videos',
    ];

    public function __construct()
    {

    }

    /**
     * Main search page method
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request){

        // Avoid (browser) page caching when hitting back button
        header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
        header('Pragma: no-cache'); // HTTP 1.0.
        header('Expires: 0 '); // Proxies.

        $jsData = $viewData = [];
        $filters = $this->processFilterParams($request);

        // Redirecting to default people filter if user is not logged in buet selected custom filter
        if(!Auth::check() && $filters['postsFilter'] && $filters['postsFilter'] != 'people') {
            return redirect(route('search.get'));
        }

        // If no filter is selected & user not logged in, default UI to people searcg
        if(!$filters['postsFilter'] && !Auth::check()){
            $filters['postsFilter'] = 'people';
        }

        if(!Auth::check()){
            $this->filters = ['people'];
        }

        if($filters['postsFilter'] != 'people'){
            $startPage = PostsHelperServiceProvider::getFeedStartPage(PostsHelperServiceProvider::getPrevPage($request));
            $posts = PostsHelperServiceProvider::getFeedPosts(Auth::user()->id, false, $startPage, $filters['mediaType'], $filters['sortOrder'], $filters['searchTerm']);
            PostsHelperServiceProvider::shouldDeletePaginationCookie($request);
            $jsData = [
                'paginatorConfig' => [
                    'next_page_url' => str_replace('/search', '/search/posts', $posts->nextPageUrl()),
                    'prev_page_url' => str_replace('/search', '/search/posts', $posts->previousPageUrl()),
                    'current_page' => $posts->currentPage(),
                    'total' => $posts->total(),
                    'per_page' => $posts->perPage(),
                    'hasMore' => $posts->hasMorePages(),
                ],
                'initialPostIDs' => $posts->pluck('id')->toArray(),
                'searchType' => 'feed'
            ];
            $viewData = ['posts' => $posts];
        }
        else{
            $users = MembersHelperServiceProvider::getSearchUsers([
                'searchTerm' => $filters['searchTerm']
            ]);
            $jsData = [
                'paginatorConfig' => [
                    'next_page_url' => str_replace('/search', '/search/users', $users->nextPageUrl()),
                    'prev_page_url' => str_replace('/search', '/search/users', $users->previousPageUrl()),
                    'current_page' => $users->currentPage(),
                    'total' => $users->total(),
                    'per_page' => $users->perPage(),
                    'hasMore' => $users->hasMorePages(),
                ],

                'searchType' => 'people'
            ];
            $viewData = ['users' => $users];
        }
        JavaScript::put(
            array_merge($jsData,
                [
                    'sliderConfig' => [
                        'autoslide'=> getSetting('feed.feed_suggestions_autoplay') ? true : false,
                    ]
                ]
            )
        );

        return view('pages.search',
            array_merge($viewData,[
            'searchTerm' => $filters['searchTerm'],
            'suggestions' => MembersHelperServiceProvider::getSuggestedMembers(),
            'availableFilters' => $this->filters,
            'activeFilter' => $filters['postsFilter']
            ])
        );


    }

    /**
 * Fetches AJAX paginated (feed search) content
 *
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
    public function getSearchPosts(Request $request)
    {
        $filters = $this->processFilterParams($request);
        return response()->json(['success'=>true, 'data'=>PostsHelperServiceProvider::getFeedPosts(Auth::user()->id, true, false, $filters['mediaType'], $filters['sortOrder'], $filters['searchTerm'])]);
    }

    /**
     * Fetches AJAX paginated (users search) content
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersSearch(Request $request)
    {
        $filters = $this->processFilterParams($request);
        return response()->json(['success'=>true, 'data'=> MembersHelperServiceProvider::getSearchUsers(['encodePostsToHtml'=>true,'searchTerm' => $filters['searchTerm']])]);
    }

    /**
     * Filters out incoming search filters
     *
     * @param $request
     * @return array
     */
    protected function processFilterParams($request){
        $searchTerm = $request->get('query') ? $request->get('query') : false;
        $postsFilter = $request->get('filter') ? $request->get('filter') : false;

        $mediaType = 'image';
        if($postsFilter == 'videos'){
            $mediaType = 'video';
        }
        if($postsFilter == 'photos'){
            $mediaType = 'image';
        }
        $sortOrder = '';
        if($postsFilter == 'top'){
            $mediaType = false;
            $sortOrder = 'top';
        }
        if($postsFilter == 'latest'){
            $mediaType = false;
            $sortOrder = 'latest';
        }

        return [
            'searchTerm' => $searchTerm,
            'postsFilter' => $postsFilter,
            'mediaType' => $mediaType,
            'sortOrder' => $sortOrder
        ];

    }

}
