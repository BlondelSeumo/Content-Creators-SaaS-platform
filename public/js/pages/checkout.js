/**
 * Component used for handling checkout dialog actions
 */
"use strict";
/* global app, trans, trans_choice */

$(function () {
    // Document ready
    // Deposit amount change event listener
    $('#checkout-amount').on('change', function () {
        if (!checkout.checkoutAmountValidation()) {
            return false;
        }

        // update payment amount
        checkout.paymentData.amount = parseFloat($('#checkout-amount').val());
        checkout.updatePaymentSummaryData();
    });

    // Checkout proceed button event listener
    $('.checkout-continue-btn').on('click', function () {
        checkout.initPayment();
    });

    $('.custom-control').on('change', function () {
        $('.error-message').hide();
    });

    $('#headingOne').on('click', function () {
        if ($('#headingOne').hasClass('collapsed')) {
            $('.card-header .label-icon').html('<ion-icon name="chevron-up-outline"></ion-icon>');
        } else {
            $('.card-header .label-icon').html('<ion-icon name="chevron-down-outline"></ion-icon>');
        }
    });

    $('#checkout-center').on('show.bs.modal', function (e) {
        //get data-id attribute of the clicked element
        var postId = $(e.relatedTarget).data('post-id');
        var recipientId = $(e.relatedTarget).data('recipient-id');
        var amount = $(e.relatedTarget).data('amount');
        var type = $(e.relatedTarget).data('type');
        var username = $(e.relatedTarget).data('username');
        var firstName = $(e.relatedTarget).data('first-name');
        var lastName = $(e.relatedTarget).data('last-name');
        var billingAddress = $(e.relatedTarget).data('billing-address');
        var name = $(e.relatedTarget).data('name');
        var avatar = $(e.relatedTarget).data('avatar');
        var country = $(e.relatedTarget).data('country');
        var city = $(e.relatedTarget).data('city');
        var state = $(e.relatedTarget).data('state');
        var postcode = $(e.relatedTarget).data('postcode');
        var availableCredit = $(e.relatedTarget).data('available-credit');

        checkout.initiatePaymentData(
            type,
            amount,
            postId,
            recipientId,
            firstName,
            lastName,
            billingAddress,
            country,
            city,
            state,
            postcode,
            availableCredit
        );
        checkout.updateUserDetails(avatar, username, name);
        checkout.fillCountrySelectOptions();
        checkout.updatePaymentSummaryData();
        checkout.prefillBillingDetails();

        let paymentTitle = '';
        let paymentDescription = '';
        if (type === 'tip') {
            $('.payment-body .checkout-amount-input').removeClass('d-none');
            paymentTitle = trans("Send a tip");
            paymentDescription = trans("Send a tip for this user to improve your binding with him");
            checkout.toggleCoinbasePaymentMethod(true);
        } else if (type === 'one-month-subscription'
            || type === 'three-months-subscription'
            || type === 'six-months-subscription'
            || type === 'yearly-subscription') {
            let numberOfMonths = 1;
            if (type === 'three-months-subscription') {
                numberOfMonths = 3;
            } else if (type === 'six-months-subscription') {
                numberOfMonths = 6;
            } else if (type === 'yearly-subscription') {
                numberOfMonths = 12;
            }
            $('.payment-body .checkout-amount-input').addClass('d-none');
            paymentTitle = trans(type);
            let subscriptionInterval = trans_choice('months', numberOfMonths, {'number': numberOfMonths});
            paymentDescription = trans("Subscribe to", {
                'amount': amount,
                'currency': app.currencySymbol,
                'username': name,
                'subscription_interval': subscriptionInterval
            });
            checkout.toggleCoinbasePaymentMethod(false);
        } else if (type === 'post-unlock') {
            $('.payment-body .checkout-amount-input').addClass('d-none');
            paymentTitle = trans('Unlock post');
            paymentDescription = trans('Unlock post for') + ' ' + app.currencySymbol + amount;
            checkout.toggleCoinbasePaymentMethod(true);
        }

        if (paymentTitle !== '' || paymentDescription !== '') {
            $('#payment-title').text(paymentTitle);
            $('.payment-body .payment-description').removeClass('d-none');
            $('.payment-body .payment-description').text(paymentDescription);
        }

        if (!firstName || !lastName || !billingAddress || !city || !state || !postcode || !country) {
            $('#billingInformation').collapse('show');
        } else {
            $('#billingInformation').collapse('hide');
        }
        $('#checkout-amount').val(amount);
    });

    $('#checkout-center').on('hidden.bs.modal', function () {
        $(this).find('#billing-agreement-form').trigger('reset');
        $('.payment-error').addClass('d-none');
    });

    // Radio button
    $('.radio-group .radio').on('click', function () {
        $(this).parent().parent().find('.radio').removeClass('selected');
        $(this).addClass('selected');
        $('.payment-error').addClass('d-none');
    });

    $('.country-select').on('change', function () {
        checkout.updatePaymentSummaryData();
    });
});

/**
 * Checkout class
 */
var checkout = {
    paymentData: {},

    /**
     * Initiates the payment data payload
     */
    initiatePaymentData: function (type, amount, post, recipient, firstName, lastName, billingAddress, country, city, state, postcode, availableCredit) {
        checkout.paymentData = {
            type: type,
            amount: amount,
            post: post,
            recipient: recipient,
            firstName: firstName,
            lastName: lastName,
            billingAddress: billingAddress,
            country: country,
            city: city,
            state: state,
            postcode: postcode,
            availableCredit: availableCredit
        };
    },

    /**
     * Updates the payment form
     */
    updatePaymentForm: function () {
        $('#payment-type').val(checkout.paymentData.type);
        $('#post').val(checkout.paymentData.post);
        $('#recipient').val(checkout.paymentData.recipient);
        $('#provider').val(checkout.paymentData.provider);
        $('#paymentFirstName').val(checkout.paymentData.firstName);
        $('#paymentLastName').val(checkout.paymentData.lastName);
        $('#paymentBillingAddress').val(checkout.paymentData.billingAddress);
        $('#paymentCountry').val(checkout.paymentData.country);
        $('#paymentState').val(checkout.paymentData.state);
        $('#paymentPostcode').val(checkout.paymentData.postcode);
        $('#paymentCity').val(checkout.paymentData.city);
        $('#payment-deposit-amount').val(checkout.paymentData.totalAmount);
        $('#paymentTaxes').val(JSON.stringify(checkout.paymentData.taxes));
    },

    stripe: null,

    /**
     * Instantiates the payment session
     */
    initPayment: function () {
        if (!checkout.checkoutAmountValidation()) {
            return false;
        }

        let billingAgreementValidation = checkout.checkoutBillingAgreementValidation();
        if (!billingAgreementValidation) {
            $('.billing-agreement-error').removeClass('d-none');
        }

        let processor = checkout.getSelectedPaymentMethod();
        if (!processor) {
            $('.payment-error').removeClass('d-none');
        }

        if (!billingAgreementValidation) {
            return false;
        }

        if (processor) {
            $('.paymentProcessorError').hide();
            $('.error-message').hide();
            $('.checkout-continue-btn .spinner-border').removeClass('d-none');
            if (processor === 'stripe' || processor === 'paypal' || processor === 'credit' || processor === 'coinbase') {
                checkout.updatePaymentForm();
                $('.payment-button').trigger('click');
            }
        }
    },

    /**
     * Returns currently selected payment method
     */
    getSelectedPaymentMethod: function () {
        const paypalProvider = $('.paypal-payment-provider').hasClass('selected');
        const stripeProvider = $('.stripe-payment-provider').hasClass('selected');
        const creditProvider = $('.credit-payment-provider').hasClass('selected');
        const coinbaseProvider = $('.coinbase-payment-provider').hasClass('selected');
        let val = null;
        if (paypalProvider) {
            val = 'paypal';
        } else if (stripeProvider) {
            val = 'stripe';
        } else if (creditProvider) {
            val = 'credit';
        } else if(coinbaseProvider){
            val = 'coinbase';
        }

        if (val) {
            checkout.paymentData.provider = val;
            return val;
        }
        return false;
    },

    /**
     * Validates the amount field
     * @returns {boolean}
     */
    checkoutAmountValidation: function () {
        const checkoutAmount = $('#checkout-amount').val();
        // For all payments besides post-unlocks, apply a 5-500 min max constrain
        if ( (checkout.paymentData.type !== 'post-unlock' && (checkoutAmount.length > 0 && checkoutAmount >= 5 && checkoutAmount <= 500)) || checkout.paymentData.type === 'post-unlock') {
            $('#checkout-amount').removeClass('is-invalid');
            $('#paypal-deposit-amount').val(checkoutAmount);
            if (checkout.paymentData.availableCredit < checkoutAmount) {
                $(".credit-payment-provider").css("pointer-events", "none");
            }
            return true;
        } else {
            $('#checkout-amount').addClass('is-invalid');
            return false;
        }
    },

    /**
     * Validates user billing info
     * @returns {boolean}
     */
    checkoutBillingAgreementValidation: function () {
        // validate individual fields
        let firstName = checkout.paymentData.firstName !== null && checkout.paymentData.firstName !== '';
        let lastName = checkout.paymentData.lastName !== null && checkout.paymentData.lastName !== '';
        let billingAddress = checkout.paymentData.billingAddress !== null && checkout.paymentData.billingAddress !== '';
        let city = checkout.paymentData.city !== null && checkout.paymentData.city !== '';
        let state = checkout.paymentData.state !== null && checkout.paymentData.state !== '';
        let postcode = checkout.paymentData.postcode !== null && checkout.paymentData.postcode !== '';
        let country = checkout.paymentData.country !== null && checkout.paymentData.country !== '';

        if (firstName && lastName && billingAddress && city && state && postcode && country) {
            $('.billing-agreement-error').addClass('d-none');
        } else {
            $('#billingInformation').collapse('show');
            $('.billing-agreement-error').removeClass('d-none');

            return false;
        }

        return true;
    },

    /**
     * Validates FN field
     */
    validateFirstNameField: function () {
        let firstNameField = $('input[name="firstName"]');
        let firstNameValidation = firstNameField.valid();

        if (firstNameValidation) {
            checkout.paymentData.firstName = firstNameField.val();
        }

        if (checkout.checkoutBillingAgreementValidation()) {
            $('.billing-agreement-error').addClass('d-none');
        }
    },

    /**
     * Validates LN field
     */
    validateLastNameField: function () {
        let lastNameField = $('input[name="lastName"]');
        let lastNameValidation = lastNameField.valid();
        if (lastNameValidation) {
            checkout.paymentData.lastName = lastNameField.val();
        }

        if (checkout.checkoutBillingAgreementValidation()) {
            $('.billing-agreement-error').addClass('d-none');
        }
    },

    /**
     * Validates Adress field
     */
    validateBillingAddressField: function () {
        let billingAddressField = $('textarea[name="billingAddress"]');
        let billingAddressValidation = billingAddressField.valid();
        if (billingAddressValidation) {
            checkout.paymentData.billingAddress = billingAddressField.val();
        }

        if (checkout.checkoutBillingAgreementValidation()) {
            $('.billing-agreement-error').addClass('d-none');
        }
    },

    /**
     * Validates city field
     */
    validateCityField: function () {
        let cityField = $('input[name="billingCity"]');
        let cityValidation = cityField.valid();
        if (cityValidation) {
            checkout.paymentData.city = cityField.val();
        }

        if (checkout.checkoutBillingAgreementValidation()) {
            $('.billing-agreement-error').addClass('d-none');
        }
    },

    /**
     * Validates state field
     */
    validateStateField: function () {
        let stateField = $('input[name="billingState"]');
        let stateValidation = stateField.valid();
        if (stateValidation) {
            checkout.paymentData.state = stateField.val();
        }

        if (checkout.checkoutBillingAgreementValidation()) {
            $('.billing-agreement-error').addClass('d-none');
        }
    },

    /**
     * Validates the ZIP code
     */
    validatePostcodeField: function () {
        let postcodeField = $('input[name="billingPostcode"]');
        let postcodeValidation = postcodeField.valid();
        if (postcodeValidation) {
            checkout.paymentData.postcode = postcodeField.val();
        }

        if (checkout.checkoutBillingAgreementValidation()) {
            $('.billing-agreement-error').addClass('d-none');
        }
    },

    /**
     * Validates the country field
     */
    validateCountryField: function () {
        let countryField = $('.country-select');
        let countryValidation = countryField.valid();
        if (countryValidation) {
            checkout.paymentData.country = $('.country-select').find(':selected').text();
        }

        if (checkout.checkoutBillingAgreementValidation()) {
            $('.billing-agreement-error').addClass('d-none');
        }
    },

    /**
     * Prefills user billing data, if available
     */
    prefillBillingDetails: function () {
        $('input[name="firstName"]').val(checkout.paymentData.firstName);
        $('input[name="lastName"]').val(checkout.paymentData.lastName);
        $('input[name="billingCity"]').val(checkout.paymentData.city);
        $('input[name="billingState"]').val(checkout.paymentData.state);
        $('input[name="billingPostcode"]').val(checkout.paymentData.postcode);
        $('textarea[name="billingAddress"]').val(checkout.paymentData.billingAddress);
        $('#paypalFirstName').val(checkout.paymentData.firstName);
        $('#paypalLastName').val(checkout.paymentData.lastName);
        $('#paypalBillingAddress').val(checkout.paymentData.billingAddress);
        $('#paypalCity').val(checkout.paymentData.city);
        $('#paypalState').val(checkout.paymentData.state);
        $('#paypalPostcode').val(checkout.paymentData.postcode);
        $('#paypalCountry').val(checkout.paymentData.country);

        let validForm = $('#billing-agreement-form').valid();
        if (validForm) {
            $('.billing-agreement-error').addClass('d-none');
        }
    },

    /**
     * Updates user details
     * @param userAvatar
     * @param username
     * @param name
     */
    updateUserDetails: function (userAvatar, username, name) {
        $('.payment-body .user-avatar').attr('src', userAvatar);
        $('.payment-body .name').text(name);
        $('.payment-body .username').text('@' + username);
    },

    /**
     * Fetches list of countries, in order to calculcate taxes
     */
    fillCountrySelectOptions: function () {
        $.ajax({
            type: 'GET',
            url: app.baseUrl + '/countries',
            success: function (result) {
                if (result !== null && typeof result.countries !== 'undefined' && result.countries.length > 0) {
                    $('.country-select').find('option').remove().end().append('<option value="0">Select a country</option>');
                    $.each(result.countries, function (i, item) {
                        let selected = checkout.paymentData.country !== null && checkout.paymentData.country === item.name;
                        $('.country-select').append($('<option>', {
                            value: item.id,
                            text: item.name,
                            selected: selected
                        }).data({taxes: item.taxes}));
                        if (selected) {
                            checkout.updatePaymentSummaryData();
                        }
                    });
                }
            }
        });
    },

    /**
     * Updates payment summary data, taxes included
     */
    updatePaymentSummaryData: function () {
        let subtotalAmount = typeof checkout.paymentData.amount !== 'undefined' ? parseFloat(checkout.paymentData.amount) : 0.00;
        let countryInclusiveTaxesPercentage = 0.00;
        let countryExclusiveTaxesPercentage = 0.00;
        let taxesAmount = 0.00;
        let totalAmount = subtotalAmount;
        let inclusiveTaxesAmount = 0.00;
        let exclusiveTaxesAmount = 0.00;
        checkout.paymentData.totalAmount = subtotalAmount;
        let taxes = [];

        // calculate taxes by country
        $('.taxes-details').html("");
        let selectedCountry = $('.country-select').find(':selected');
        if (selectedCountry !== null && selectedCountry.val() > 0) {
            let countryTaxes = selectedCountry.data('taxes');
            if (countryTaxes !== null) {
                if (countryTaxes.length > 0) {
                    for (let i = 0; i < countryTaxes.length; i++) {
                        let countryTaxPercentage = countryTaxes[i].percentage;
                        if (countryTaxPercentage !== null && countryTaxPercentage > 0) {
                            let countryTaxAmount = 0.00;
                            if (countryTaxes[i].type === 'exclusive') {
                                countryExclusiveTaxesPercentage += parseFloat(countryTaxPercentage);
                                taxes.push({
                                    countryTaxName: countryTaxes[i].name,
                                    type: 'exclusive',
                                    countryTaxPercentage: parseFloat(countryTaxPercentage)
                                });
                            } else {
                                countryInclusiveTaxesPercentage += parseFloat(countryTaxPercentage);
                                taxes.push({
                                    countryTaxAmount: countryTaxAmount,
                                    countryTaxName: countryTaxes[i].name,
                                    type: 'inclusive',
                                    countryTaxPercentage: parseFloat(countryTaxPercentage)
                                });
                            }
                        }
                    }
                }
            }
        }

        let formattedTaxes = {data: [], taxesTotalAmount: 0.00, subtotal: subtotalAmount.toFixed(2)};

        if (subtotalAmount > 0) {
            for (let j = 0; j < taxes.length; j++) {
                let taxAmount = 0.00;
                let inclusiveTaxesAmount = subtotalAmount - subtotalAmount / (1 + countryInclusiveTaxesPercentage.toFixed(2) / 100);
                if (taxes[j].type === 'inclusive') {
                    let countryTaxAmount = subtotalAmount - subtotalAmount / (1 + taxes[j].countryTaxPercentage.toFixed(2) / 100);
                    let remainingFees = inclusiveTaxesAmount - countryTaxAmount;
                    let amountWithoutRemainingFees = subtotalAmount - remainingFees;
                    taxAmount = amountWithoutRemainingFees - amountWithoutRemainingFees / (1 + taxes[j].countryTaxPercentage.toFixed(2) / 100);

                    try {
                        taxAmount = taxAmount.toString().match(/^-?\d+(?:\.\d{0,2})?/)[0];
                    } catch (e) {
                        taxAmount = taxAmount.toFixed(2);
                    }
                }

                if (taxes[j].type === 'exclusive') {
                    let amountWithInclusiveFeesDeducted = subtotalAmount - inclusiveTaxesAmount;
                    taxAmount = (countryExclusiveTaxesPercentage.toFixed(2) / 100) * amountWithInclusiveFeesDeducted;
                    taxAmount = taxAmount.toFixed(2);
                }

                formattedTaxes.data.push({
                    taxName: taxes[j].countryTaxName,
                    taxAmount: taxAmount,
                    taxPercentage: taxes[j].countryTaxPercentage,
                    taxType: taxes[j].type
                });

                let taxType = taxes[j].type === 'inclusive' ? ' incl.' : '';
                let item = "<div class=\"row ml-2\">\n" +
                    "<span class=\"col-sm left\">" + taxes[j].countryTaxName + " (" + taxes[j].countryTaxPercentage + "%" + taxType + ")</span>\n" +
                    "<span class=\"country-tax col-sm right text-right\">\n" +
                    "    <b>" + app.currencySymbol + taxAmount + "</b>\n" +
                    "</span>\n" +
                    "</div>";
                $('.taxes-details').append(item);
            }

            let subtotal = subtotalAmount;
            if (countryInclusiveTaxesPercentage > 0) {
                inclusiveTaxesAmount = subtotalAmount - subtotalAmount / (1 + countryInclusiveTaxesPercentage.toFixed(2) / 100);
                if (inclusiveTaxesAmount > 0) {
                    subtotal = subtotalAmount - inclusiveTaxesAmount;
                }
            }

            if (countryExclusiveTaxesPercentage > 0) {
                exclusiveTaxesAmount = (countryExclusiveTaxesPercentage.toFixed(2) / 100) * subtotal;
                totalAmount = totalAmount + exclusiveTaxesAmount;
            }

            if (formattedTaxes.data && formattedTaxes.data.length > 0) {
                for (let i = 0; i < formattedTaxes.data.length; i++) {
                    if (formattedTaxes.data[i]['taxAmount'] !== undefined) {
                        taxesAmount = taxesAmount + parseFloat(formattedTaxes.data[i]['taxAmount']);
                    }
                }
            }
            formattedTaxes.taxesTotalAmount = taxesAmount.toFixed(2);
            checkout.paymentData.totalAmount = totalAmount.toFixed(2);
        }

        checkout.paymentData.taxes = formattedTaxes;

        $('.available-credit').html('(' + app.currencySymbol + checkout.paymentData.availableCredit + ')');
        if (checkout.paymentData.availableCredit < totalAmount) {
            $(".credit-payment-provider").css("pointer-events", "none");
        }

        $('.subtotal-amount b').html(app.currencySymbol + subtotalAmount.toFixed(2));
        $('.total-amount b').html(app.currencySymbol + totalAmount.toFixed(2));
        $('.country-taxes b').html(app.currencySymbol + taxesAmount.toFixed(2));
    },

    toggleCoinbasePaymentMethod: function(toggle){
        let paymentMethod = $('.coinbase-payment-method');
        if(toggle){
            if(paymentMethod.hasClass('d-none')){
                $('.coinbase-payment-method').removeClass('d-none')
            }
        } else {
            if(!paymentMethod.hasClass('d-none')){
                $('.coinbase-payment-method').addClass('d-none')
            }
        }

    }
};
