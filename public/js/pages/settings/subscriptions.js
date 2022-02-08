/**
 * Subscription settings component
 */
"use strict";
/* global app */

var SubscriptionsSettings = {
    selectedSubID: null,
    confirmSubCancelation: function (subIDToCancel) {
        SubscriptionsSettings.selectedSubID = subIDToCancel;
        $('#subscription-cancel-dialog').modal('show');
    },
    cancelSubscription: function () {
        window.location.href = app.baseUrl + '/subscriptions/'+SubscriptionsSettings.selectedSubID+'/cancel';
    }
};
