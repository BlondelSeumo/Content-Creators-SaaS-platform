<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUploadRequest;
use App\Http\Requests\UpdateUserFlagSettingsRequest;
use App\Http\Requests\UpdateUserProfileSettingsRequest;
use App\Http\Requests\UpdateUserRatesSettingsRequest;
use App\Http\Requests\UpdateUserSettingsSettingsRequest;
use App\Http\Requests\VerifyProfileAssetsRequest;
use App\Model\CreatorOffer;
use App\Model\Subscription;
use App\Model\Transaction;
use App\Model\UserVerify;
use App\Providers\AttachmentServiceProvider;
use App\Providers\EmailsServiceProvider;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use JavaScript;
use Ramsey\Uuid\Uuid;

class SettingsController extends Controller
{
    /**
     * Available settings types.
     * Note*: The values are translated over on view side
     * @var array
     */
    public $availableSettings = [
        'profile' => ['heading' => 'Update your bio, cover and avatar', 'icon' => 'person'],
        'account' => ['heading' => 'Manage your account settings', 'icon' => 'settings'],
        'wallet' => ['heading' => 'Your payments & wallet', 'icon' => 'wallet'],
        'payments' => ['heading' => 'Your payments & wallet', 'icon' => 'card'],
        'rates' => ['heading' => 'Prices & Bundles', 'icon' => 'layers'],
        'subscriptions' => ['heading' => 'Your active subscriptions', 'icon' => 'people'],
        'notifications' => ['heading' => 'Your email notifications settings', 'icon' => 'notifications'],
        'privacy' => ['heading' => 'Your privacy and safety matters', 'icon' => 'shield'],
        'verify' => ['heading' => 'Get verified and start to earning now', 'icon' => 'checkmark'],
    ];

    public function __construct()
    {
        if(getSetting('site.hide_identity_checks')){
            unset($this->availableSettings['verify']);
        }
    }

    /**
     * Check if active route is a valid one, based on setting types.
     *
     * @param $route
     * @return bool
     */
    public function checkIfValidRoute($route)
    {
        if ($route) {
            if (! isset($this->availableSettings[$route])) {
                abort(404);
            }
        }

        return true;
    }

    /**
     * Renders the main settings page.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->checkIfValidRoute($request->route('type'));
        $userID = Auth::user()->id;
        $data = [];
        switch ($request->route('type')) {
            case 'wallet':
                JavaScript::put([
                    'stripeConfig' => [
                        'stripePublicID' => getSetting('payments.stripe_public_key'),
                    ],
                ]);
                $activeWalletTab = $request->get('active');
                $data['activeTab'] = $activeWalletTab != null ? $activeWalletTab : 'deposit';
                break;
            case 'subscriptions':
                $subscriptions = Subscription::with(['creator'])->where('sender_user_id', $userID)->paginate(6);
                $data['subscriptions'] = $subscriptions;
                break;
            case 'payments':
                $payments = Transaction::with(['receiver', 'sender'])->where('sender_user_id', $userID)->orWhere('recipient_user_id', $userID)->paginate(6);
                $data['payments'] = $payments;
                break;
        }

        return $this->renderSettingView($request->route('type'), $data);
    }

    /**
     * Renders the selected setting type page.
     *
     * @param $route
     * @param array $data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderSettingView($route, $data = [])
    {
        $currentTab = $route ? $route : 'profile';
        $currentSettingTab = $this->availableSettings[$currentTab];
        Javascript::put(
            [
                'mediaSettings' => [
                    'allowed_file_extensions' => '.'.str_replace(',', ',.', AttachmentServiceProvider::filterExtensions('imagesOnly')),
                    'max_file_upload_size' => (int) getSetting('media.max_file_upload_size'),
                ],
            ]
        );

        return view('pages.settings', array_merge(
            $data,
            [
                'availableSettings' => $this->availableSettings,
                'currentSettingTab' => $currentSettingTab,
                'activeSettingsTab' => $currentTab,
                'additionalAssets'   => $this->getAdditionalRouteAssets($route),
            ]
        ));
    }

    /**
     * Custom method for saving profile settings.
     *
     * @param UpdateUserProfileSettingsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveProfile(UpdateUserProfileSettingsRequest $request)
    {
        $validator = $this->validateUsername($request->get('username'));
        if($validator->fails()){
            return back()->withErrors($validator);
        }
        $user = Auth::user();
        $user->update([
            'name' => $request->get('name'),
            'username' => $request->get('username'),
            'bio' => $request->get('bio'),
            'location' => $request->get('location'),
            'website' => $request->get('website'),
            'birthdate' => $request->get('birthdate'),
        ]);

        return back()->with('success', __('Settings saved.'));
    }

    private function validateUsername($username){
        $routes = [];

        // You need to iterate over the RouteCollection you receive here
        // to be able to get the paths and add them to the routes list
        foreach (Route::getRoutes() as $route)
        {
            $routes[] = $route->uri;
        }

        $validator = \Illuminate\Support\Facades\Validator::make(
            ['username' => $username],
            ['username' => 'not_in:' . implode(',', $routes)],
            ['username.*' => __('The selected username is invalid.')]
        );

        return $validator;
    }

    /**
     * Custom method for saving user rates.
     *
     * @param UpdateUserRatesSettingsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveRates(UpdateUserRatesSettingsRequest $request)
    {
        $user = Auth::user();
        if ($request->get('profile_access_offer_date')) {
            $offerExpireData = $request->get('profile_access_offer_date');
            $isOffer = $request->get('profile_access_offer_date') ? $request->get('profile_access_offer_date') : false;
            $currentOffer = CreatorOffer::where('user_id', Auth::user()->id)->first();
            if ($currentOffer) {
                $data = [
                    'expires_at' => $offerExpireData,
                    'old_profile_access_price' => $user->profile_access_price,
                    'old_profile_access_price_6_months' => $user->profile_access_price_6_months,
                    'old_profile_access_price_12_months' => $user->profile_access_price_12_months,
                ];
                $currentOffer->update($data);
            } else {
                $data = [
                    'expires_at' => $offerExpireData,
                    'user_id' => $user->id,
                    'old_profile_access_price' => $request->get('profile_access_price'),
                    'old_profile_access_price_6_months' => $request->get('profile_access_price_6_months'),
                    'old_profile_access_price_12_months' => $request->get('profile_access_price_12_months'),
                ];
                CreatorOffer::create($data);
            }
        } else {
            $currentOffer = CreatorOffer::where('user_id', Auth::user()->id)->first();
            if ($currentOffer) {
                $currentOffer->delete();
            }
        }

        $user->update([
            'profile_access_price' => $request->get('profile_access_price'),
            'profile_access_price_6_months' => $request->get('profile_access_price_6_months'),
            'profile_access_price_12_months' => $request->get('profile_access_price_12_months'),
        ]);

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Saves one user flag at the time
     * Used for on the fly custom BS switches used on privacy & notifications settings
     * !Must whitelist all allowed keys to be updated!
     *
     * @param UpdateUserFlagSettingsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateFlagSettings(UpdateUserFlagSettingsRequest $request)
    {
        $key = $request->get('key');
        $value = filter_var($request->get('value'), FILTER_VALIDATE_BOOLEAN);
        if (! in_array($key, ['public_profile', 'paid-profile'])) {
            return response()->json(['success' => false, 'message' => __('Settings not saved')]);
        }
        if($key === 'paid-profile'){
            $key = 'paid_profile';
        }

        $user = Auth::user();
        $user->update([
            $key => $value,
        ]);

        return response()->json(['success' => true, 'message' => __('Settings saved')]);
    }

    /**
     * Custom method for saving account (password) settings.
     *
     * @param UpdateUserSettingsSettingsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveAccount(UpdateUserSettingsSettingsRequest $request)
    {
        Auth::user()->update(['password'=>Hash::make($request->input('confirm_password'))]);

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Method used for injecting additional assets into any desired setting type page.
     *
     * @param $settingRoute
     * @return array
     */
    public function getAdditionalRouteAssets($settingRoute)
    {
        $additionalAssets = ['js' => [], 'css' => []];
        switch ($settingRoute) {
            case 'wallet':
                $additionalAssets['js'][] = '/js/pages/settings/deposit.js';
                $additionalAssets['js'][] = '/js/pages/settings/withdrawal.js';
                break;
            case 'profile':
            case null:
                $additionalAssets['css'][] = '/libs/dropzone/dist/dropzone.css';
                $additionalAssets['js'][] = '/libs/dropzone/dist/dropzone.js';
                $additionalAssets['js'][] = '/js/pages/settings/profile.js';
                break;
            case 'privacy':
                $additionalAssets['js'][] = '/js/pages/settings/privacy.js';
                break;
            case 'notifications':
                $additionalAssets['js'][] = '/js/pages/settings/notifications.js';
                break;
            case 'subscriptions':
                $additionalAssets['js'][] = '/js/pages/settings/subscriptions.js';
                break;
            case 'verify':
                $additionalAssets['css'][] = '/libs/dropzone/dist/dropzone.css';
                $additionalAssets['js'][] = '/libs/dropzone/dist/dropzone.js';
                $additionalAssets['js'][] = '/js/pages/settings/verify.js';
                $additionalAssets['js'][] = '/js/FileUpload.js';
                break;
            case 'rates':
                $additionalAssets['js'][] = '/js/pages/settings/rates.js';
                break;
        }

        return $additionalAssets;
    }

    /**
     * Method used for uploading and saving the profile assets ( avatar & cover ).
     *
     * @param ProfileUploadRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadProfileAsset(ProfileUploadRequest $request)
    {
        $file = $request->file('file');
        $type = $request->route('uploadType');

        try {
            $directory = 'users/'.$type;
            $s3 = Storage::disk(config('filesystems.defaultFilesystemDriver'));
            $fileId = Uuid::uuid4()->getHex();
            $filePath = $directory.'/'.$fileId.'.'.$file->guessClientExtension();

            $img = Image::make($file);
            if ($type == 'cover') {
                $img->fit(599, 180)->orientate();
                $data = ['cover' => $filePath];
            } else {
                $img->fit(96)->orientate();
                $data = ['avatar' => $filePath];
            }

            // Resizing the asset
            $img->encode('jpg', 100);
            // Saving to user db
            Auth()->user()->update($data);
            // Saving to disk
            $s3->put($filePath, $img, 'public');
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'errors' => ['file'=>$exception->getMessage()]]);
        }

        return response()->json(['success' => true, 'assetSrc' => asset(Storage::url($filePath))]);
    }

    /**
     * Method used for deleting profile asset from db & storage disk.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeProfileAsset(Request $request)
    {
        $type = $request->route('assetType');
        $data = ['avatar' => ''];
        if ($type == 'cover') {
            $data = ['cover' => ''];
        }
        Auth::user()->update($data);

        return response()->json(['success' => true, 'message' => ucfirst($type).' '.__("removed successfully").'.', 'data' => [
            'avatar' => Auth::user()->avatar,
            'cover' => Auth::user()->cover,
        ]]);
    }

    /**
     * General method for saving user fields, they must be valid and fillable fields.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserSettings(Request $request)
    {
        try {
            if (! in_array($request->key, [
                'notification_email_new_sub',
                'notification_email_new_message',
                'notification_email_expiring_subs',
                'notification_email_renewals',
                'notification_email_new_tip',
                'notification_email_new_comment',
            ])) {
                return response()->json(['success' => false, 'message' => __('Invalid setting key')]);
            }

            User::where('id', Auth::user()->id)->update(['settings'=> array_merge(
                Auth::user()->settings->toArray(),
                [$request->get('key') => $request->get('value')]
            ),
            ]);

            return response()->json(['success' => true, 'message' => __('Settings saved')]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => __('Settings not saved'), 'error' => $exception->getMessage()]);
        }
    }

    /**
     * Method used for uploading ID check files.
     *
     * @param VerifyProfileAssetsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyUpload(VerifyProfileAssetsRequest $request)
    {
        $file = $request->file('file');
        try {
            $directory = 'users/verifications';
            $s3 = Storage::disk(config('filesystems.defaultFilesystemDriver'));
            $fileId = Uuid::uuid4()->getHex();
            $filePath = $directory.'/'.$fileId.'.'.$file->guessClientExtension();

            $img = Image::make($file);

            // Resizing the asset
            $img->encode('jpg', 100);
            // Saving to disk
            $s3->put($filePath, $img, 'public');

            if ($request->session()->get('verifyAssets')) {
                $data = json_decode($request->session()->get('verifyAssets'));
                $data[] = $filePath;
                session(['verifyAssets' => json_encode($data)]);
            } else {
                $data = [$filePath];
                session(['verifyAssets' => json_encode($data)]);
            }
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'errors' => ['file'=>$exception->getMessage()]]);
        }

        return response()->json(['success' => true, 'assetSrc' => $filePath]);
    }

    /**
     * Delete ID checks assets.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteVerifyAsset(Request $request)
    {
        try {
            $filePath = $request->get('assetSrc');
            $data = json_decode($request->session()->get('verifyAssets'));
            $newData = array_diff($data, [$filePath]);
            session(['verifyAssets' => json_encode($newData)]);
            $storage = Storage::disk(config('filesystems.defaultFilesystemDriver'));
            $storage->delete($filePath);

            return response()->json(['success' => true]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'errors' => ['file'=>$exception->getMessage()]]);
        }
    }

    /**
     * Send ID check to admin for approval.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveVerifyRequest(Request $request)
    {
        if ($request->session()->get('verifyAssets')) {
            if (! Auth::user()->verification) {
                UserVerify::create([
                    'user_id' => Auth::user()->id,
                    'files' => $request->session()->get('verifyAssets'),
                ]);
            } else {
                Auth::user()->verification->update(
                    [
                        'user_id' => Auth::user()->id,
                        'files' => $request->session()->get('verifyAssets'),
                        'status' => 'pending',
                    ]
                );
            }

            // Sending out admin email
            $adminEmails = User::where('role_id', 1)->select(['email', 'name'])->get();
            foreach ($adminEmails as $user) {
                EmailsServiceProvider::sendGenericEmail(
                    [
                        'email' => $user->email,
                        'subject' => __('Action required | New identity check'),
                        'title' => __('Hello, :name,', ['name' => $user->name]),
                        'content' => __('There is a new identity check on :siteName that requires your attention.', ['siteName' => getSetting('site.name')]),
                        'button' => [
                            'text' => __('Go to admin'),
                            'url' => route('voyager.dashboard'),
                        ],
                    ]
                );
            }

            $request->session()->forget('verifyAssets');

            return back()->with('success', __('Request sent. You will be notified once your verification is processed.'));
        } else {
            return back()->with('error', __('Please attach photos with the front and back sides of your ID.'));
        }
    }
}
