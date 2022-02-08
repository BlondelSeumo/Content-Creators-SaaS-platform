<?php

namespace App\Http\Controllers;

use App\Model\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    protected $notificationTypes = [
        Notification::MESSAGES_FILTER,
        Notification::LIKES_FILTER,
        Notification::SUBSCRIPTIONS_FILTER,
        Notification::TIPS_FILTER,
        // Notification::PROMOS_FILTER, // Temporarily disabled
    ];

    /**
     * Renders the main notifications center view.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $activeType = $request->route('type');
        $listOnly = $request->get('list');

        $notifications = $this->getUserNotifications($activeType);

        if ($listOnly != null && $listOnly) {
            return view('elements.notifications.notifications-wrapper', [
                'notifications' => $notifications,
            ]);
        } else {
            return view('pages.notifications', [
                'notificationTypes' => $this->notificationTypes,
                'activeType' => $activeType,
                'notifications' => $notifications,
            ]);
        }
    }

    /**
     * Gets notification type by the current active filter.
     *
     * @param $filter
     * @return array
     */
    private function getNotificationTypesByActiveFilter($filter)
    {
        $types = [];
        if ($filter != null) {
            switch ($filter) {
                case Notification::MESSAGES_FILTER:
                    $types[] = Notification::NEW_COMMENT;
                    $types[] = Notification::NEW_MESSAGE;
                    break;
                case Notification::LIKES_FILTER:
                    $types[] = Notification::NEW_REACTION;
                    break;
                case Notification::SUBSCRIPTIONS_FILTER:
                    $types[] = Notification::NEW_SUBSCRIPTION;
                    break;
                case Notification::TIPS_FILTER:
                    $types[] = Notification::NEW_TIP;
                    break;
                case Notification::PROMOS_FILTER:
                    $types[] = Notification::PROMOS_FILTER;
                    break;
            }
        }

        return $types;
    }

    /**
     * Gets user notifications within the selected filter.
     *
     * @param $activeType
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function getUserNotifications($activeType)
    {
        $notificationTypes = $this->getNotificationTypesByActiveFilter($activeType);
        if (is_array($notificationTypes) && count($notificationTypes) > 0) {
            $notifications = Notification::query()
                ->where(['to_user_id' => Auth::id()])
                ->whereIn('type', $notificationTypes)
                ->orderBy('created_at', 'DESC')
                ->with(['fromUser', 'post'])
                ->paginate(8);
        } else {
            $notifications = Notification::query()
                ->where(['to_user_id' => Auth::id()])
                ->orderBy('created_at', 'DESC')
                ->with(['fromUser', 'post'])
                ->paginate(7);
        }

        return $notifications;
    }
}
