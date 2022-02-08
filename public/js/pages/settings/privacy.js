/**
 * Privacy settings component
 */
"use strict";
/* global GeneralSettings */

$(function () {
    $('.custom-control-input').on('change',function () {
        const key = $(this).attr('id');
        const val = $(this).prop("checked");
        GeneralSettings.updateFlagSetting(key,val);
    });
});

// eslint-disable-next-line no-unused-vars
var PrivacySettings = {

};
