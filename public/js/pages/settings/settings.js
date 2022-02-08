/**
 * General settings component
 */
"use strict";
/* global app, trans, launchToast, initStickyComponent */

$(function () {
    $('.setting-menu-mobile .nav').mCustomScrollbar({
        theme: "minimal-dark",
        axis:'x',
        scrollInertia: 200,
    });
});

$(window).scroll(function () {
    initStickyComponent('.settings-menu-wrapper','sticky-sm');
});

// eslint-disable-next-line no-unused-vars
var GeneralSettings = {

    /**
     * Updates general (whitelisted) settings flags
     * @param key
     * @param value
     */
    updateFlagSetting: function (key,value) {
        $.ajax({
            type: 'POST',
            data: {
                'key': key,
                'value': value
            },
            dataType: 'json',
            url: app.baseUrl+'/my/settings/flags/save',
            success: function (result) {
                if(result.success){
                    launchToast('success',trans('Success'),trans('Setting saved'));
                }
                else{
                    launchToast('danger',trans('Error'),trans('Setting save failed'));
                }
            },
            error: function () {
                launchToast('danger',trans('Error'),trans('Setting save failed'));
            }
        });
    }
};
