<?php

namespace App\Observers;

use App\Model\Withdrawal;
use App\Providers\EmailsServiceProvider;
use App\Providers\NotificationServiceProvider;
use App\User;
use Illuminate\Support\Facades\App;

class WithdrawalsObserver
{
    /**
     * Listen to the Withdrawal updating event.
     *
     * @param  \App\Model\Withdrawal  $withdrawal
     * @return void
     */
    public function saving(Withdrawal $withdrawal)
    {
        if ($withdrawal->getOriginal('status') == 'requested' && $withdrawal->status != 'requested') {
            if ($withdrawal->status == 'rejected') {
                $emailSubject = __('Your withdrawal request has been denied.');
                $button = [
                    'text' => __('Try again'),
                    'url' => route('my.settings', ['type'=>'wallet']),
                ];
            } elseif ($withdrawal->status = 'approved') {
                $emailSubject = __('Your withdrawal request has been approved.');
                $button = [
                    'text' => __('My payments'),
                    'url' => route('my.settings', ['type'=>'payments']),
                ];
            }

            // Sending out the user notification
            $user = User::find($withdrawal->user_id);
            App::setLocale($user->settings['locale']);
            EmailsServiceProvider::sendGenericEmail(
                [
                    'email' => $user->email,
                    'subject' => $emailSubject,
                    'title' => __('Hello, :name,', ['name'=>$user->name]),
                    'content' => __('Email withdrawal processed', [
                        'siteName' => getSetting('site.name'),
                        'status' => __($withdrawal->status),
                    ]).($withdrawal->status == 'approved' ? ' $'.$withdrawal->amount.' '.__('has been sent to your account.') : ''),
                    'button' => $button,
                ]
            );
            NotificationServiceProvider::createApprovedOrRejectedWithdrawalNotification($withdrawal);
        }
    }
}
