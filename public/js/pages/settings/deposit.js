/**
 * Deposit page component
 */
"use strict";
/* global app */

$(function () {
    // Deposit amount change event listener
    $('#deposit-amount').on('change', function () {
        if (!DepositSettings.depositAmountValidation()) {
            return false;
        }

        // update payment amount
        DepositSettings.amount = $('#deposit-amount').val();
    });

    // Checkout proceed button event listener
    $('.deposit-continue-btn').on('click', function () {
        DepositSettings.initPayment();
    });

    $('.custom-control').on('change', function () {
        $('.error-message').hide();
    });
});

/**
 * Deposit class
 */
var DepositSettings = {

    stripe: null,
    paymentProvider: null,
    amount: null,

    /**
     * Instantiates new payment session
     */
    initPayment: function () {
        if (!DepositSettings.depositAmountValidation()) {
            return false;
        }

        let processor = DepositSettings.getSelectedPaymentMethod();
        if (processor !== false) {
            $('.paymentProcessorError').hide();
            $('.error-message').hide();
            DepositSettings.updateDepositForm();
            $('.payment-button').trigger('click');
        } else {
            $('.payment-error').removeClass('d-none');
        }
    },

    /**
     * Returns currently selected payment method
     */
    getSelectedPaymentMethod: function () {
        const val = $('input[name="payment-radio-option"]:checked').val();
        if (val) {
            switch (val) {
                case 'payment-stripe':
                    DepositSettings.provider = 'stripe';
                    break;
                case 'payment-paypal':
                    DepositSettings.provider = 'paypal';
                    break;
                case 'payment-coinbase':
                    DepositSettings.provider = 'coinbase';
                    break;
            }
            return DepositSettings.provider;
        }
        return false;
    },

    /**
     * Updates deposit form with predefined values
     */
    updateDepositForm: function () {
        $('#payment-type').val('deposit');
        $('#provider').val(DepositSettings.provider);
        $('#wallet-deposit-amount').val(DepositSettings.amount);
    },

    /**
     * Validates deposit amount field
     * @returns {boolean}
     */
    depositAmountValidation: function () {
        const depositAmount = $('#deposit-amount').val();
        if (depositAmount.length > 0 && (parseFloat(depositAmount) < parseFloat(app.depositMinAmount) || parseFloat(depositAmount) > parseFloat(app.depositMaxAmount))) {
            $('#deposit-amount').addClass('is-invalid');
            return false;
        } else {
            $('#deposit-amount').removeClass('is-invalid');
            $('#wallet-deposit-amount').val(depositAmount);
            return true;
        }
    }
};
