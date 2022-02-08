<?php

namespace App\Http\Controllers;

use App\Events\NewUserMessage;
use App\Model\Attachment;
use App\Model\Subscription;
use App\Model\UserMessage;
use App\Providers\AttachmentServiceProvider;
use App\Providers\EmailsServiceProvider;
use App\Providers\NotificationServiceProvider;
use App\Providers\PostsHelperServiceProvider;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Javascript;
use Pusher\Pusher;

class MessengerController extends Controller
{
    /**
     * Renders the main messenger view / layout
     * Rest of the messenger elements are mostly loaded via JS.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $lastContactID = false;
        $lastContact = $this->fetchContacts(1);
        if ($lastContact) {
            $lastContactID = $lastContact[0]->receiverID == Auth::user()->id ? $lastContact[0]->senderID : $lastContact[0]->receiverID;
        }
        Javascript::put([
            'messengerVars' => [
                'userAvatarPath' =>  ($request->getHost() == 'localhost' ? 'http://localhost' : 'https://'.$request->getHost()).$request->getBaseUrl().'/uploads/users/avatars/',
                'lastContactID' => $lastContactID,
                'pusherDebug' => (bool) env('APP_DEBUG'),
                'pusherCluster' => config('broadcasting.connections.pusher.options.cluster'),
                'bootFullMessenger' => true,
            ],
            'mediaSettings' => [
                'allowed_file_extensions' => '.'.str_replace(',', ',.', AttachmentServiceProvider::filterExtensions('videosFallback')),
                'max_file_upload_size' => (int) getSetting('media.max_file_upload_size'),
            ],
            'user' => [
                'username' => Auth::user()->username,
                'user_id' => Auth::user()->id,
                'lists' => [
                    'blocked'=>Auth::user()->lists->firstWhere('type', 'blocked')->id,
                    'following'=>Auth::user()->lists->firstWhere('type', 'followers')->id,
                ],
            ],
        ]);

        $unseenMessages = UserMessage::where('receiver_id', Auth::user()->id)->where('isSeen', 0)->count();
        $data = [
            'lastContactID' => $lastContactID,
            'unseenMessages' => $unseenMessages,
        ];

        return view('pages.messenger', $data);
    }

    /**
     * Method used for fetching available contacts/conversations.
     *
     * @param string $limit
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchContacts($limit = '0')
    {
        $userID = Auth::user()->id;
        $contacts = DB::select('
            SELECT
             t1.sender_id as lastMessageSenderID,
             t1.message as lastMessage,
             t1.isSeen,
             t1.created_at,
             senderDetails.id as senderID,
             senderDetails.name as senderName,
             senderDetails.avatar as senderAvatar,
             receiverDetails.id as receiverID,
             receiverDetails.name as receiverName,
             receiverDetails.avatar as receiverAvatar,
             IF(receiverDetails.id = '.Auth::user()->id.', senderDetails.id, receiverDetails.id) as contactID
            FROM user_messages AS t1
            INNER JOIN
            (
                SELECT
                    LEAST(receiver_id, sender_id) AS receiverID,
                    GREATEST(receiver_id, sender_id) AS senderID,
                    MAX(id) AS max_id
                FROM user_messages
                GROUP BY
                    LEAST(receiver_id, sender_id),
                    GREATEST(receiver_id, sender_id)
            ) AS t2
                ON LEAST(t1.receiver_id, t1.sender_id) = t2.receiverID AND
                   GREATEST(t1.receiver_id, t1.sender_id) = t2.senderID AND
                   t1.id = t2.max_id
            INNER JOIN users senderDetails ON t1.sender_id = senderDetails.id #AND senderDetails.level <> 3
            INNER JOIN users receiverDetails ON t1.receiver_id = receiverDetails.id #AND receiverDetails.level <> 3
            LEFT JOIN user_list_members listMembers ON listMembers.list_id = '.Auth::user()->lists->firstWhere('type', 'blocked')->id.' AND (listMembers.user_id = senderID OR listMembers.user_id = receiverID)
            WHERE listMembers.id IS NULL
                AND (t1.receiver_id = ? OR t1.sender_id = ?)
                ORDER BY created_at DESC
                '.($limit != '0' ? "LIMIT 0,$limit" : '').'
            ', [$userID, $userID]);

        foreach ($contacts as $contact) {
            $dateDiff = Carbon::createFromTimeStamp(strtotime($contact->created_at))->diffForHumans(null, true, true);
            $contact->created_at = $dateDiff;
            $contact->senderAvatar = User::getStorageAvatarPath($contact->senderAvatar);
            $contact->receiverAvatar = User::getStorageAvatarPath($contact->receiverAvatar);
        }

        if ($limit) {
            return $contacts;
        }

        return response()->json([
            'status'=>'success',
            'data'=>[
                'contacts' => $contacts,
            ], ]);
    }

    /**
     * Method used for fetching the conversation messages.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchMessages(Request $request)
    {
        $senderID = Auth::user()->id;
        $receiverID = $request->route('userID');
        $conversation = UserMessage::with(['sender', 'receiver', 'attachments'])->where(function ($q) use ($senderID, $receiverID) {
            $q->where('sender_id', $senderID)
                ->where('receiver_id', $receiverID);
        })
            ->orWhere(
                function ($q) use ($senderID, $receiverID) {
                    $q->where('receiver_id', $senderID)
                        ->Where('sender_id', $receiverID);
                }
            )
            ->get()->map(function ($message) {
                $message->sender->profileUrl = route('profile', ['username'=> $message->sender->username]);
                $message->receiver->profileUrl = route('profile', ['username'=> $message->receiver->username]);

                return $message;
            });

        return response()->json([
            'status'=>'success',
            'data'=>[
                'messages' => $conversation,
            ], ]);
    }

    /**
     * Sends the user message.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        $senderID = (int) Auth::user()->id;
        $receiverID = (int) $request->get('receiverID');
        $messageValue = $request->get('message');
        $isFirstMessage = $request->get('new');

        $message = UserMessage::create([
            'sender_id' => $senderID,
            'receiver_id' => $receiverID,
            'message' => $messageValue,
        ]);

        NotificationServiceProvider::createNewUserMessageNotification($message);

        // Turning date into human readable format
        $dateDiff = $message->created_at->diffForHumans(null, true, true);
        $message = $message->toArray();
        $message['dateAdded'] = $dateDiff;

        if ($message['id']) {
            $attachments = collect($request->get('attachments'))->map(function ($v, $k) {
                if (isset($v['attachmentID'])) {
                    return $v['attachmentID'];
                }
                if (isset($v['id'])) {
                    return $v['id'];
                }
            })->toArray();
            if ($request->get('attachments')) {
                Attachment::whereIn('id', $attachments)->update(['message_id'=>$message['id']]);
            }
        }

        $message = UserMessage::with(['sender', 'receiver', 'attachments'])->where('id', $message['id'])->first();

        // Sending the email
        if (isset($message->receiver->settings['notification_email_new_message']) && $message->receiver->settings['notification_email_new_message'] == 'true') {
            App::setLocale($message->receiver->settings['locale']);
            EmailsServiceProvider::sendGenericEmail(
                [
                    'email' => $message->receiver->email,
                    'subject' => __('New message received'),
                    'title' => __('Hello, :name,', ['name'=>$message->receiver->name]),
                    'content' => __('Email new message title', ['siteName'=>getSetting('site.name')]),
                    'button' => [
                        'text' => __('View your messages'),
                        'url' => route('my.messenger.get'),
                    ],
                ]
            );
            App::setLocale(Auth::user()->settings['locale']);
        }

        // Sending the message to the socket
        broadcast(new NewUserMessage(json_encode($message), $senderID, $receiverID))->toOthers();

        $return = [
            'message' => $message,
        ];

        if ($isFirstMessage) {
            $lastContact = $this->fetchContacts(1);
            $return['contact'] = $lastContact;
        }

        return response()->json([
            'status'=>'success',
            'data'=> $return,
        ]);
    }

    /**
     * Marks message as being seen.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markSeen(Request $request)
    {
        $senderID = $request->get('userID');
        $unreadMessages = UserMessage::where('receiver_id', Auth::user()->id)->where('sender_id', $senderID)->where('isSeen', 0)->count();
        UserMessage::where('receiver_id', Auth::user()->id)->where('sender_id', $senderID)->where('isSeen', 0)->update(['isSeen'=>1]);

        return response()->json([
            'status'=>'success',
            'data'=>[
                'count' => $unreadMessages,
            ], ]);
    }

    /**
     * Authorize socket connections.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authorizeUser(Request $request)
    {
        $envVars['PUSHER_APP_KEY'] = config('broadcasting.connections.pusher.key');
        $envVars['PUSHER_APP_SECRET'] = config('broadcasting.connections.pusher.secret');
        $envVars['PUSHER_APP_ID'] = config('broadcasting.connections.pusher.app_id');
        $envVars['PUSHER_APP_CLUSTER'] = config('broadcasting.connections.pusher.options.cluster');
        $pusher = new Pusher(
            $envVars['PUSHER_APP_KEY'],
            $envVars['PUSHER_APP_SECRET'],
            $envVars['PUSHER_APP_ID'],
            [
                'cluster' => $envVars['PUSHER_APP_CLUSTER'],
                'encrypted' => true,
            ]
        );

        try {
            $output = [];
            foreach ($request->get('channel_name') as $channelName) {
                $users = explode('-', $channelName);
                $users = array_slice($users, 3, 2);
                $users = array_map('intval', $users);
                if (in_array(Auth::user()->id, $users)) {
                    $auth = $pusher->socket_auth(
                        $channelName,
                        $request->input('socket_id')
                    );
                    $output[$channelName] = ['status'=>200, 'data'=>json_decode($auth)];
                } else {
                    $output[$channelName] = [
                        'code' => '403',
                        'data' => [
                            'errors' => ['Not authorized'],
                        ],
                    ];
                }
            }

            return $output;
        } catch (\Exception $exception) {
            return response()->json([
                'code' => '403',
                'data' => [
                    'errors' => [__('Invalid channel name(s) provided')],
                ], ]);
        }
    }

    /**
     * Gets available users to start a conversation with.
     *
     * @param Request $request
     * @return false|string
     */
    public function getUserSearch(Request $request)
    {
        $users = $this->selectizeList($request->input('q'), Auth::user()->id);
        return response()->json($users);
    }

    /**
     * Turns the mysql collection into a selectize-2 list compatible array format.
     *
     * @param $q
     * @param $id
     * @return array
     */
    public static function selectizeList($q, $id)
    {
        $values = [
            'users' => []
        ];

        // Fetching users subscribed to
        $subbedUsers = Subscription::with(['creator' =>  function ($query) use ($id, $q) {
            $query->where('name', 'LIKE', "%$q%");
        }])
            ->where('sender_user_id', $id)
            ->where('status', 'completed')
            ->orwhere([
                ['status', '=', 'canceled'],
                ['expires_at', '<', Carbon::now()],
            ])
            ->get();

        foreach ($subbedUsers as $k => $user) {
            $values['users'][$user->creator->id]['id'] = $user->creator->id;
            $values['users'][$user->creator->id]['name'] = $user->creator->name;
            $values['users'][$user->creator->id]['avatar'] = $user->creator->avatar;
            $values['users'][$user->creator->id]['label'] = '<div><img class="searchAvatar" src="uploads/users/avatars/'.$user->creator->avatar.'" alt=""><span class="name">'.$user->creator->name.'</span></div>';
        }

        // Fetching users followed for free
        $freeFollowIDs = PostsHelperServiceProvider::getFreeFollowingProfiles(Auth::user()->id);
        $freeFollowUsers = User::whereIn('id',$freeFollowIDs)->get();
        foreach ($freeFollowUsers as $k => $user) {
            $values['users'][$user->id]['id'] = $user->id;
            $values['users'][$user->id]['name'] = $user->name;
            $values['users'][$user->id]['avatar'] = $user->avatar;
            $values['users'][$user->id]['label'] = '<div><img class="searchAvatar" src="uploads/users/avatars/'.$user->avatar.'" alt=""><span class="name">'.$user->name.'</span></div>';
        }

        return $values['users'];
    }
}
