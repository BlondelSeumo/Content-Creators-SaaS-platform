/**
 * Rates settings component
 */
"use strict";
/* global GeneralSettings */
$(function () {
    $("#profile_access_offer_date").on('change',function () {
        $("#is_offer").prop('checked',true);
    });

    $("#is_offer").on('change',function () {
        $("#profile_access_offer_date").val('');
    });

    $('.custom-control-input').on('change',function () {
        const key = $(this).attr('id');
        const val = $(this).prop("checked");
        GeneralSettings.updateFlagSetting(key,val);

        if(val){
            if($('.paid-profile-rates').hasClass('d-none')){
                $('.paid-profile-rates').removeClass('d-none');
            }
        } else {
            if(!$('.paid-profile-rates').hasClass('d-none')){
                $('.paid-profile-rates').addClass('d-none');
            }
        }
    });
});

// eslint-disable-next-line no-unused-vars
var RatesSettings = {

};
