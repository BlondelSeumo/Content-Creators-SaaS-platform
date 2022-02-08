/**
 *
 */
"use strict";
/* global app */

$(function () {
    $('.notifications-nav-mobile').mCustomScrollbar({
        theme: "minimal-dark",
        axis:'x',
        scrollInertia: 200,
    });
});

/**
 * Checkout class
 */
// eslint-disable-next-line no-unused-vars
var notifications = {
    data: {},

    /**
     * Returns pusher received notification
     * @param activeFilter
     */
    updateUserNotificationsList: function (activeFilter) {
        $.ajax({
            type: 'GET',
            url: activeFilter !== null
                ? app.baseUrl + '/my/notifications' + activeFilter + '?page=1&list=1'
                : app.baseUrl + '/my/notifications?page=1&list=1',
            success: function (result) {
                $('.notifications-wrapper').html(result);
            }
        });
    }
};
