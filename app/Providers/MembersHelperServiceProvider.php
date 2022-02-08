<?php

namespace App\Providers;

use App\Model\FeaturedUser;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use View;

class MembersHelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Small method that tries to fetch up a list of the most popular profiles across the platform
     * If there isn't a big enough number to choose from, it fallbacks to latest profiles.
     *
     * @param bool $encodeToHtml
     * @param array $filters
     * @return mixed
     */
    public static function getSuggestedMembers($encodeToHtml = false, $filters = [])
    {

        $skipEmptyProfiles = getSetting('feed.suggestions_skip_empty_profiles') ? true : false;

        // Get top 32 list of most subbed users
        $mostSubbedMax = (int) getSetting('feed.feed_suggestions_total_cards') * 3;
        $topSubbedUsers = DB::select("
            SELECT usersTable.id, COUNT(subsTable.id ) AS subs_count FROM users usersTable
            INNER JOIN subscriptions subsTable ON usersTable.id = subsTable.recipient_user_id
            WHERE usersTable.role_id = 2
            ".($skipEmptyProfiles ? 'AND usersTable.avatar IS NOT NULL AND usersTable.cover IS NOT NULL' : '')."
            GROUP BY usersTable.id
            ORDER BY subs_count DESC
            LIMIT 0,{$mostSubbedMax}
        ");
        $topSubbedUsers = array_map(function ($v) {
            return $v->id;
        }, $topSubbedUsers);

        $members = User::limit(getSetting('feed.feed_suggestions_total_cards') * getSetting('feed.feed_suggestions_card_per_page'))->where('public_profile', 1);

        // If there are more than 9 users having subs, use those
        // Otherwise, grab latest 9 users by date
        if (count($topSubbedUsers) >= 6) {
            $members->whereIn('id', $topSubbedUsers);
        } else {
            $members->where('role_id',2);
            $members->orderByDesc('created_at');
            if(Auth::check()){
                $members->where('id', '<>', Auth::user()->id);
            }
            if($skipEmptyProfiles){
                $members->where('avatar', '<>', null);
                $members->where('cover', '<>', null);
            }
        }

        // Filtering free/paid accounts
        if (isset($filters['free'])) {
            $members->where('paid_profile', 0);
        }
        $members = $members->get();

        // Shuffle the list each time for more randomness
        $members = $members->shuffle();

        // Return either raw data to the views or json encoded, rendered views
        if ($encodeToHtml) {
            $membersData['html'] = View::make('elements.feed.suggestions-wrapper')->with('profiles', $members)->render();

            return $membersData;
        } else {
            return $members;
        }
    }

    /**
     * Returns a list of latest profiles.
     * @param $limit
     * @return mixed
     */
    public static function getFeaturedMembers($limit)
    {
        $members = FeaturedUser::with(['user'])->orderByDesc('featured_users.created_at')->limit($limit);
        $members->join('users', function ($join) {
            $join->on('users.id', '=', 'featured_users.user_id');
        });
        $members = $members->get()->map(function ($v){
            return $v->user;
        });
        if(count($members)){
            return $members;
        }
        else{
            $members = User::limit(3)->where('public_profile', 1)->where('role_id',2)->orderByDesc('created_at')->get();
            return $members;
        }
    }


    public static function getSearchUsers($options){

        $users = User::where('public_profile',1);
        $users->where('role_id',2);

        if(Auth::check()){
            $users->where('id', '<>', Auth::user()->id);
        }

        if(isset($options['searchTerm'])){
            // Might take a small hit on performance
            $users->where(function($query) use ($options) {
                $query->where('username', 'like', '%'.$options['searchTerm'].'%');
                $query->orWhere('bio', 'like', '%' . $options['searchTerm'] . '%');
                $query->orWhere('name', 'like', '%' . $options['searchTerm'] . '%');
            });
        }

        if (isset($options['pageNumber'])) {
            $users = $users->paginate(9, ['*'], 'page', $options['pageNumber'])->appends(request()->query());
        } else {
            $users = $users->paginate(9)->appends(request()->query());
        }

        if(!isset($options['encodePostsToHtml'])){
            $options['encodePostsToHtml'] = false;
        }

        if ($options['encodePostsToHtml']) {
            // Posts encoded as JSON
            $data = [
                'total' => $users->total(),
                'currentPage' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'prev_page_url' => $users->previousPageUrl(),
                'next_page_url' => $users->nextPageUrl(),
                'first_page_url' => $users->nextPageUrl(),
                'hasMore' => $users->hasMorePages(),
            ];
            $postsData = $users->map(function ($user) use ( $data) {
                $user->setAttribute('postPage',$data['currentPage']);
                $user = ['id' => $user->id, 'html' => View::make('elements.search.users-list-element')->with('user', $user)->render()];
                return $user;
            });
            $data['users'] = $postsData;
        } else {
            // Collection data posts | To be rendered on the server side
            $postsCurrentPage = $users->currentPage();
            $users->map(function ($user) use ($postsCurrentPage) {
                $user->setAttribute('postPage',$postsCurrentPage);
                return $user;
            });
            $data = $users;
        }

        return $data;
    }

}
