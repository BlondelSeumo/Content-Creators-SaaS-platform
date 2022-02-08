<?php

namespace App\Observers;

use App\Model\UserVerify;
use App\Providers\EmailsServiceProvider;
use App\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserVerifyObserver
{
    /**
     * Listen to the User updating event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function saving(UserVerify $userVerify)
    {
        if ($userVerify->getOriginal('status') == 'pending' && $userVerify->status != 'pending') {
            if ($userVerify->status == 'rejected') {
                // Reject
                $emailSubject = __('Your identity check failed.');
                $button = [
                    'text' => __('Try again'),
                    'url' => route('my.settings', ['type'=>'verify']),
                ];
            } elseif ($userVerify->status = 'verified') {
                // Check ok
                $emailSubject = __('Your identity check passed.');
                $button = [
                    'text' => __('Create a post'),
                    'url' => route('posts.create'),
                ];
            }

            // Sending out the user notification
            $user = User::find($userVerify->user_id);
            App::setLocale($user->settings['locale']);
            EmailsServiceProvider::sendGenericEmail(
                [
                    'email' => $user->email,
                    'subject' => $emailSubject,
                    'title' => __('Hello, :name,', ['name'=>$user->name]),
                    'content' => __('Email identity checked', ['siteName'=>getSetting('site.name'), 'status'=>__($userVerify->status)]),
                    'button' => $button,
                ]
            );
        }

        // Deleting files
        $storage = Storage::disk(config('filesystems.defaultFilesystemDriver'));
        $oldFiles = json_decode($userVerify->getOriginal('files')) ? json_decode($userVerify->getOriginal('files')) : [];
        $newFiles = json_decode($userVerify->files) ? json_decode($userVerify->files) : [];

        $toDelete = array_diff($oldFiles, $newFiles);

        foreach ($toDelete as $file) {
            Log::debug($file);
            $storage->delete($file);
        }
    }
}
