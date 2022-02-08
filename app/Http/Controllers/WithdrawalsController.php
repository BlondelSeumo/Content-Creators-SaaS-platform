<?php

namespace App\Http\Controllers;

use App\Model\Withdrawal;
use App\Providers\EmailsServiceProvider;
use App\Providers\GenericHelperServiceProvider;
use App\Providers\PaymentsServiceProvider;
use App\Providers\SettingsServiceProvider;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalsController extends Controller
{
    /**
     * Method used for requesting an withdrawal request from the admin.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestWithdrawal(Request $request)
    {
        try {
            $amount = $request->request->get('amount');
            $message = $request->request->get('message');

            $user = Auth::user();
            if ($amount != null && $user != null) {
                if ($user->wallet == null) {
                    $user->wallet = GenericHelperServiceProvider::createUserWallet($user);
                }

                if(floatval($amount) === floatval(PaymentsServiceProvider::getWithdrawalMinimumAmount()) && floatval($amount) > $user->wallet->total){
                    return response()->json(
                        [
                            'success' => false,
                            'message' => __("You don't have enough credit to withdraw. Minimum amount is: ", ['minAmount' => PaymentsServiceProvider::getWithdrawalMinimumAmount()])
                        ]
                    );
                }

                if (floatval($amount) > $user->wallet->total) {
                    return response()->json(['success' => false, 'message' => __('You cannot withdraw this amount, try with a lower one')]);
                }

                Withdrawal::create([
                    'user_id' => Auth::user()->id,
                    'amount' => floatval($amount),
                    'status' => Withdrawal::REQUESTED_STATUS,
                    'message' => $message,
                ]);

                $user->wallet->update([
                    'total' => $user->wallet->total - floatval($amount),
                ]);

                $totalAmount = number_format($user->wallet->total, 2, '.', '');
                $pendingBalance = number_format($user->wallet->pendingBalance, 2, '.', '');

                // Sending out admin email
                $adminEmails = User::where('role_id', 1)->select(['email', 'name'])->get();
                foreach ($adminEmails as $user) {
                    EmailsServiceProvider::sendGenericEmail(
                        [
                            'email' => $user->email,
                            'subject' => __('Action required | New withdrawal request'),
                            'title' => __('Hello, :name,', ['name' => $user->name]),
                            'content' => __('There is a new withdrawal request on :siteName that requires your attention.', ['siteName' => getSetting('site.name')]),
                            'button' => [
                                'text' => __('Go to admin'),
                                'url' => route('voyager.dashboard'),
                            ],
                        ]
                    );
                }

                return response()->json([
                    'success' => true,
                    'message' => __('Successfully requested withdrawal'),
                    'totalAmount' => SettingsServiceProvider::getWebsiteCurrencySymbol().$totalAmount,
                    'pendingBalance' => SettingsServiceProvider::getWebsiteCurrencySymbol().$pendingBalance,
                ]);
            }
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }

        return response()->json(['success' => false, 'message' => __('Something went wrong, please try again')]);
    }
}
