<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClearListRequest;
use App\Http\Requests\ManageUserFollowsRequest;
use App\Http\Requests\SaveListRequest;
use App\Http\Requests\UpdateUserListMemberRequest;
use App\Model\UserList;
use App\Model\UserListMember;
use App\Model\UserReport;
use App\Providers\ListsHelperServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JavaScript;
use View;

class ListsController extends Controller
{
    /**
     * Renders main lists page.
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $lists = ListsHelperServiceProvider::getUserLists();

        return view('pages.lists', [
            'lists' => $lists,
        ]);
    }

    /**
     * Renders individual lists page.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showList(Request $request)
    {
        $listID = $request->route('list_id');
        $list = UserList::with(['members', 'members.user'])->where('id', $listID)->where('user_id', Auth::user()->id)->first();

        $list->setAttribute('isManageable', true);
        if ($list->type == 'followers' || $list->type == 'blocked') {
            $list->setAttribute('isManageable', false);
        }
        if (! $list) {
            abort(404);
        }
        JavaScript::put([
            'listVars' => ['name'=>$list->name, 'list_id'=>$list->id],
        ]);

        return view('pages.list', [
            'list' => $list,
        ]);
    }

    /**
     * Method used for creating/updating a lists.
     *
     * @param SaveListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveList(SaveListRequest $request)
    {
        $type = $request->get('type');
        $name = $request->get('name');
        if ($type == 'create') {
            $listID = UserList::create([
                'user_id' => Auth::user()->id,
                'name' => $name,
                'type' => 'custom',
            ])->id;
            $list = UserList::with(['members', 'members.user'])->where('id', $listID)->where('user_id', Auth::user()->id)->first();

            return response()->json([
                'success'=>true,
                'data'=> View::make('elements.lists.list-box')->with(['list' => $list, 'isLastItem' => true])->render(),
            ]);
        } elseif ($type == 'edit') {
            $listID = $request->get('list_id');
            $list = UserList::where('id', $listID)->where('user_id', Auth::user()->id)->where('type', 'custom');
            $list->update([
                'name' => $name,
            ]);

            return response()->json([
                'success'=> true,
            ]);
        }
    }

    /**
     * Method used for deleting a list.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteList(Request $request)
    {
        $listID = $request->get('id');
        $list = UserList::where('id', $listID)->where('user_id', Auth::user()->id)->where('type', 'custom')->first();
        if ($list) {
            $list->delete();

            return response()->json(['success' => true, 'message' => __('List deleted successfully.')]);
        } else {
            return response()->json(['success' => false, 'error' => __('List deleted successfully.')]);
        }
    }

    /**
     * Method used for adding an user to a list.
     *
     * @param UpdateUserListMemberRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addListMember(UpdateUserListMemberRequest $request)
    {
        $listID = $request->get('list_id');
        $userID = $request->get('user_id');
        $returnData = $request->get('returnData') == 'false' ? false : true;
        if (! $this->isAuthorized($listID)) {
            return response()->json(['success' => false, 'errors' => [__('Not authorized')], 'message'=> __('Not authorized')], 403);
        }

        return ListsHelperServiceProvider::addListMember($listID, $userID, $returnData);
    }

    /**
     * Method used for deleting an user from a list.
     *
     * @param UpdateUserListMemberRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteListMember(UpdateUserListMemberRequest $request)
    {
        $listID = $request->get('list_id');
        $userID = $request->get('user_id');
        $returnData = $request->get('returnData') == 'false' ? false : true;
        if (! $this->isAuthorized($listID)) {
            return response()->json(['success' => false, 'errors' => [__('Not authorized')], 'message'=> __('Not authorized')], 403);
        }

        return ListsHelperServiceProvider::deleteListMember($listID, $userID, $returnData);
    }

    /**
     * Method used for deleting all members withing a list.
     *
     * @param ClearListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearList(ClearListRequest $request)
    {
        try {
            $listID = $request->get('list_id');
            if (! $this->isAuthorized($listID)) {
                return response()->json(['success' => false, 'errors' => [__('Not authorized')], 'message'=> __('Not authorized')], 403);
            }
            if (! UserList::where('id', $listID)->where('user_id', Auth::user()->id)->count()) {
                return response()->json(['success' => false, 'errors' => [__('List not found.')]]);
            }
            UserListMember::where('list_id', $listID)->delete();

            return response()->json(['success' => true, 'message' => __('List cleared.')]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'errors' => [__('An internal error has occurred.')]]);
        }
    }

    /**
     * Method used for saving user reports.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postReport(Request $request)
    {
        $fromUserID = Auth::user()->id;
        $reportedUserID = $request->get('user_id');
        $reportedPostID = $request->get('post_id');
        $reportType = $request->get('type');
        $details = $request->get('details');
        try {
            $data = [
                'from_user_id' => $fromUserID,
                'user_id' => $reportedUserID,
                'post_id' => $reportedPostID,
                'type' => $reportType,
                'status' => UserReport::$statusMap[0],
                'details' => $details,
            ];
            UserReport::create($data);

            return response()->json(['success' => true, 'message' => __('Report sent.')]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'errors' => [__('An internal error has occurred.')], 'message'=>$exception->getMessage()]);
        }
    }

    /**
     * Method used for checking if user is authorized to manage a certain list.
     *
     * @param $listID
     * @return bool
     */
    public function isAuthorized($listID)
    {
        // Checking if is authorized
        $userLists = UserList::where('user_id', Auth::user()->id)->get()->pluck('id')->toArray();
        $isOwnedList = in_array($listID, $userLists);
        if (! $isOwnedList) {
            return false;
        }

        return true;
    }

    /**
     * Method used for adding/removing an user from followers list.
     * @param ManageUserFollowsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function manageUserFollows(ManageUserFollowsRequest $request) {
        $userId = $request->get('user_id');
        try {
            ListsHelperServiceProvider::managePredefinedUserMemberList(Auth::user()->id, $userId, ListsHelperServiceProvider::getUserFollowingType($userId));
        } catch (\Exception $exception){
            return response()->json(['success' => false, 'text' => ListsHelperServiceProvider::getUserFollowingType($userId)]);
        }

        return response()->json(['success' => true, 'text' => ListsHelperServiceProvider::getUserFollowingType($userId, true)]);
    }
}
