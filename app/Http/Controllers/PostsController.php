<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeletePostRequest;
use App\Http\Requests\SavePostCommentRequest;
use App\Http\Requests\SavePostRequest;
use App\Http\Requests\UpdatePostBookmarkRequest;
use App\Http\Requests\UpdateReactionRequest;
use App\Model\Attachment;
use App\Model\Post;
use App\Model\PostComment;
use App\Model\Reaction;
use App\Model\UserBookmark;
use App\Providers\AttachmentServiceProvider;
use App\Providers\GenericHelperServiceProvider;
use App\Providers\ListsHelperServiceProvider;
use App\Providers\NotificationServiceProvider;
use App\Providers\PostsHelperServiceProvider;
use Cookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JavaScript;
use Log;
use View;

class PostsController extends Controller
{
    /**
     * Method used for rendering the single post page.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function getPost(Request $request)
    {
        $post_id = $request->route('post_id');
        $username = $request->route('username');

        $user = PostsHelperServiceProvider::getUserByUsername($username);
        if (! $user) {
            abort(404);
        }

        $post = Post::withCount('tips')->with('user', 'attachments', 'reactions')->where('id', $post_id)->first();
        if (! $post) {
            abort(404);
        }

        $post->setAttribute('isSubbed', false);
        // Checking authorization & post existence
        if (PostsHelperServiceProvider::hasActiveSub(Auth::user()->id, $post->user->id)
            || Auth::user()->id == $post->user->id
            || PostsHelperServiceProvider::userPaidForPost(Auth::user()->id, $post->id)
            || (!$post->user->paid_profile && ListsHelperServiceProvider::loggedUserIsFollowingUser($post->user->id))
        ) {
            $post->setAttribute('isSubbed', true);
        }

        JavaScript::put([
            'postVars' => [
                'post_id' => $post->id,
            ],
        ]);

        $data = [
            'post' => $post,
            'user' => $user,
        ];

        $data['recentMedia'] = false;
        if ($post->isSubbed || Auth::user()->id == $post->user->id) {
            $data['recentMedia'] = PostsHelperServiceProvider::getLatestUserAttachments($user->id, 'image');
        }

        return view('pages.post', $data);
    }

    /**
     * Renders the post create page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        Javascript::put([
            'mediaSettings' => [
                'allowed_file_extensions' => '.' . str_replace(',', ',.', AttachmentServiceProvider::filterExtensions('videosFallback')),
                'max_file_upload_size' => (int)getSetting('media.max_file_upload_size'),
            ],
        ]);

        return view('pages.create', []);
    }

    /**
     * Shows post edit template.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        $postID = $request->route('post_id');
        $post = Post::where('id', $postID)->where('user_id', Auth::user()->id)->with(['attachments'])->first();
        if (! $post) {
            abort(404);
        }
        Javascript::put([
            'postData' => [
                'id' => $post->id,
                'text' => $post->text,
                'attachments' => $post->attachments,
                'price' => $post->price,
            ],
            'mediaSettings' => [
                'allowed_file_extensions' => '.'.str_replace(',', ',.', AttachmentServiceProvider::filterExtensions('videosFallback')),
                'max_file_upload_size' => (int) getSetting('media.max_file_upload_size'),
            ],
        ]);

        return view('pages.create', [
            'post' => $post,
        ]);
    }

    /**
     * Method used for creating / editing posts.
     *
     * @param SavePostRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function savePost(SavePostRequest $request)
    {
        try {
            if (! GenericHelperServiceProvider::isUserVerified() && getSetting('site.enforce_user_identity_checks')) {
                return response()->json(['success' => false, 'errors' => ['permissions' => __('User not verified. Can not post content.')]], 500);
            }

            $type = $request->get('type');

            if ($type == 'create') {
                $postID = Post::create([
                    'user_id' => $request->user()->id,
                    'text' => $request->get('text'),
                    'price' => $request->get('price'),
                    'status' => 1,
                ])->id;
            } elseif ($type == 'update') {
                $postID = $request->get('id');
                $post = Post::where('id', $postID)->where('user_id', Auth::user()->id)->first();
                if ($post) {
                    $post->update([
                        'text' => $request->get('text'),
                        'price' => $request->get('price'),
                    ]);
                    $postID = $post->id;
                } else {
                    return response()->json(['success' => false, 'errors' => [__('Not authorized')], 'message' => __('Post not found')], 403);
                }
            }

            if ($postID) {
                $attachments = collect($request->get('attachments'))->map(function ($v, $k) {
                    if (isset($v['attachmentID'])) {
                        return $v['attachmentID'];
                    }
                    if (isset($v['id'])) {
                        return $v['id'];
                    }
                })->toArray();

                if ($request->get('attachments')) {
                    Attachment::whereIn('id', $attachments)->update(['post_id' => $postID]);
                }
            }

            $message = __('Post created.');
            if ($type == 'update') {
                $message = __('Post updated successfully.');
            }

            return response()->json([
                'success' => 'true', 'message' => $message,
            ]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'errors' => [$exception->getMessage()]]);
        }
    }

    /**
     * Gets (ajaxed) post comments.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPostComments(Request $request)
    {
        try {
            $postID = $request->get('post_id');

            // Checking authorization & post existence
            $post = Post::with(['user'])->where('id', $postID)->first();
            if (! $post) {
                return response()->json(['success' => false, 'errors' => [__('Not found')], 'message'=> __('Post not found')], 404);
            }

            if (PostsHelperServiceProvider::hasActiveSub(Auth::user()->id, $post->user_id) || Auth::user()->id == $post->user_id || (!$post->user->paid_profile && ListsHelperServiceProvider::loggedUserIsFollowingUser($post->user->id))) {
                $limit = $request->get('limit') ? $request->get('limit') : 9;

                return response()->json([
                    'success' => true,
                    'data' => PostsHelperServiceProvider::getPostComments($postID, $limit, 'DESC', true),
                ]);
            } else {
                return response()->json(['success' => false, 'errors' => [__('Not authorized')], 'message' => __('Not authorized')], 403);
            }
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'errors' => [$exception->getMessage()]]);
        }
    }

    /**
     * Method used for adding a new post comment.
     *
     * @param SavePostCommentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addNewComment(SavePostCommentRequest $request)
    {
        try {
            $comment = $request->get('message');
            $postID = $request->get('post_id');

            // Checking authorization & post existence
            $post = Post::where('id', $postID)->first();
            if (!$post) {
                return response()->json(['success' => false, 'errors' => [__('Not found')], 'message' => __('Post not found')], 404);
            }

            if (PostsHelperServiceProvider::hasActiveSub(Auth::user()->id, $post->user_id) || Auth::user()->id == $post->user_id || (!$post->user->paid_profile && ListsHelperServiceProvider::loggedUserIsFollowingUser($post->user->id))) {
                $comment = PostComment::create([
                    'message' => $comment,
                    'post_id' => $postID,
                    'user_id' => Auth::user()->id,
                ]);

                $post = Post::query()->where('id', $postID)->first();
                if ($comment != null && $post != null && $comment->user_id != $post->user_id) {
                    NotificationServiceProvider::createNewPostCommentNotification($comment);
                }

                return response()->json([
                    'success' => true,
                    'data' => View::make('elements.feed.post-comment')->with('comment', $comment)->render(),
                ]);
            }
            else{
                return response()->json(['success' => false, 'errors' => [__('Not authorized')], 'message' => __('Not authorized')], 403);

            }

        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'errors' => [$exception->getMessage()]]);
        }
    }

    /**
     * Method used for adding / removing a post / comment reaction.
     *
     * @param UpdateReactionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateReaction(UpdateReactionRequest $request)
    {
        $type = $request->get('type');
        $action = $request->get('action');
        $id = $request->get('id');

        $data = [
            'reaction_type' => 'like',
            'user_id' => Auth::user()->id,
        ];

        try {
            // Checking authorization & post existence
            $postComment = PostComment::where('id', $id)->first();
            $post = null;
            if ($postComment != null) {
                $post = $postComment->post;
            } else if ($type === 'post' && $id != null) {
                $post = Post::where('id', $id)->first();
            }

            if (!$post) {
                return response()->json(['success' => false, 'errors' => [__('Not found')], 'message' => __('Post not found')], 404);
            }

            if (PostsHelperServiceProvider::hasActiveSub(Auth::user()->id, $post->user_id) || Auth::user()->id == $post->user_id || (!$post->user->paid_profile && ListsHelperServiceProvider::loggedUserIsFollowingUser($post->user->id))) {
                if ($type == 'post') {
                    $data['post_id'] = $id;
                } elseif ($type == 'comment') {
                    $data['post_comment_id'] = $id;
                }
                $message = '';
                if ($action == 'add') {
                    $message = __('Reaction added.');
                    $reaction = Reaction::create($data);

                    if ($reaction != null) {
                        NotificationServiceProvider::createNewReactionNotification($reaction);
                    }
                } elseif ($action == 'remove') {
                    $message = __('Reaction removed.');
                    Reaction::where($data)->first()->delete();
                }

                return response()->json(['success' => true, 'message' => $message]);
            }

        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'errors' => [__('An internal error has occurred.')], 'message' => $exception->getMessage()]);
        }
    }

    /**
     * Method used for adding / deleting a post bookmark.
     *
     * @param UpdatePostBookmarkRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePostBookmark(UpdatePostBookmarkRequest $request)
    {
        $action = $request->get('action');
        $id = $request->get('id');
        $data = [
            'post_id' => $id,
            'user_id' => Auth::user()->id,
        ];
        try {

            // Checking authorization & post existence
            $post = Post::where('id', $id)->first();
            if (! $post) {
                return response()->json(['success' => false, 'errors' => [__('Not found')], 'message'=> __('Post not found')], 404);
            }

            if (Auth::user()->id != $post->user_id) {
                if (! PostsHelperServiceProvider::hasActiveSub(Auth::user()->id, $post->user_id)) {
                    return response()->json(['success' => false, 'errors' => [__('Not authorized')], 'message'=> __('Not authorized')], 403);
                }
            }

            $message = '';
            if ($action == 'add') {
                $message = 'Bookmark added.';
                UserBookmark::create($data);
            } elseif ($action == 'remove') {
                $message = 'Bookmark removed.';
                UserBookmark::where($data)->first()->delete();
            }

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'errors' => [__('An internal error has occurred.')]]);
        }
    }

    /**
     * Method used for deleting a post.
     *
     * @param DeletePostRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePost(DeletePostRequest $request)
    {
        $postID = $request->get('id');
        $post = Post::where('id', $postID)->where('user_id', Auth::user()->id)->first();
        if ($post) {
            $post->delete();

            return response()->json(['success' => true, 'message' => __('Post deleted successfully.')]);
        }

        return response()->json(['success' => false, 'errors' => [__('Post not found.')]]);
    }
}
