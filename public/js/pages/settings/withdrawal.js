/**
 * Money settings component
 */
"use strict";
/* global app, launchToast */

$(function () {
    // Deposit amount change event listener
    $('#withdrawal-amount').on('change', function () {
        if (!Wallet.withdrawalAmountValidation()) {
            return false;
        }
    });
    // Checkout proceed button event listener
    $('.withdrawal-continue-btn').on('click', function () {
        Wallet.initWithdrawal();
    });
    $('.custom-control').on('change', function () {
        $('.withdrawal-error-message').hide();
    });
});

var Wallet = {

    /**
     * Instantiate withdrawal request
     * @returns {boolean}
     */
    initWithdrawal: function () {
        if (!Wallet.withdrawalAmountValidation()) {
            return false;
        }

        $('.withdrawal-error-message').hide();
        $.ajax({
            type: 'POST',
            data: {
                amount: $('#withdrawal-amount').val(),
                message: $('#withdrawal-message').val(),
            },
            url: app.baseUrl + '/withdrawals/request',
            success: function (result) {
                // eslint-disable-next-line no-undef
                launchToast('success', 'Success', result.message);

                // append new amounts
                $('.wallet-total-amount').html(result.totalAmount);
                $('.wallet-pending-amount').html(result.pendingBalance);

                // clear inputs
                $('#withdrawal-amount').val('');
                $('#withdrawal-message').val('');
            }
        });
    },

    /**
     * Validates the withdrawal amount
     * @returns {boolean}
     */
    withdrawalAmountValidation: function () {
        let withdrawalAmount = $('#withdrawal-amount').val();
        if (withdrawalAmount.length > 0 && (parseFloat(withdrawalAmount) < parseFloat(app.withdrawalsMinAmount) || parseFloat(withdrawalAmount) > parseFloat(app.withdrawalsMaxAmount))) {
            $('#withdrawal-amount').addClass('is-invalid');
            return false;
        } else {
            $('#withdrawal-amount').removeClass('is-invalid');
            return true;
        }
    }
};
