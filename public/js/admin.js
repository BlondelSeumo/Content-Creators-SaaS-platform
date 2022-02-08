/**
 * Admin panel JS functions
 */
"use strict";
/* global toastr, trans */

$(function () {
    const location = window.location.href;
    if(location.indexOf('admin/settings') >= 0){
        Admin.settingsPageInit();
        // eslint-disable-next-line no-undef
        Admin.emailSettingsSwitch(site_settings["emails.driver"]);
        // eslint-disable-next-line no-undef
        Admin.storageSettingsSwitch(site_settings["storage.driver"]);
        Admin.setCustomSettingsTabEvents();
    }

    // master
    var appContainer = document.querySelector('.app-container'),
        sidebar = appContainer.querySelector('.side-menu'),
        navbar = appContainer.querySelector('nav.navbar.navbar-top'),
        loader = document.getElementById('voyager-loader'),
        hamburgerMenu = document.querySelector('.hamburger'),
        sidebarTransition = sidebar.style.transition,
        navbarTransition = navbar.style.transition,
        containerTransition = appContainer.style.transition;

    sidebar.style.WebkitTransition = sidebar.style.MozTransition = sidebar.style.transition =
        appContainer.style.WebkitTransition = appContainer.style.MozTransition = appContainer.style.transition =
            navbar.style.WebkitTransition = navbar.style.MozTransition = navbar.style.transition = 'none';

    if (window.innerWidth > 768 && window.localStorage && window.localStorage['voyager.stickySidebar'] === 'true') {
        appContainer.className += ' expanded no-animation';
        loader.style.left = (sidebar.clientWidth/2)+'px';
        hamburgerMenu.className += ' is-active no-animation';
    }

    navbar.style.WebkitTransition = navbar.style.MozTransition = navbar.style.transition = navbarTransition;
    sidebar.style.WebkitTransition = sidebar.style.MozTransition = sidebar.style.transition = sidebarTransition;
    appContainer.style.WebkitTransition = appContainer.style.MozTransition = appContainer.style.transition = containerTransition;

    // login
    if(location.indexOf('admin/login') >= 0){
        var btn = document.querySelector('button[type="submit"]');
        var form = document.forms[0];
        var email = document.querySelector('[name="email"]');
        var password = document.querySelector('[name="password"]');
        btn.addEventListener('click', function(ev){
            if (form.checkValidity()) {
                btn.querySelector('.signingin').className = 'signingin';
                btn.querySelector('.signin').className = 'signin hidden';
            } else {
                ev.preventDefault();
            }
        });
        email.focus();
        document.getElementById('emailGroup').classList.add("focused");

        // Focus events for email and password fields
        email.addEventListener('focusin', function(){
            document.getElementById('emailGroup').classList.add("focused");
        });
        email.addEventListener('focusout', function(){
            document.getElementById('emailGroup').classList.remove("focused");
        });

        password.addEventListener('focusin', function(){
            document.getElementById('passwordGroup').classList.add("focused");
        });
        password.addEventListener('focusout', function(){
            document.getElementById('passwordGroup').classList.remove("focused");
        });
    }

    $('.save-settings-form').on('submit',function(evt){
        // code
        if(!Admin.validateSettingFields()){
            evt.preventDefault();
            // launch toast

        }
    });

});

var Admin = {

    activeSettingsTab : '',

    /**
     * Catches the Settings > tab switches event
     */
    setCustomSettingsTabEvents: function(){
        $('.settings  .nav a').on('click',function () {
            const tab = $(this).attr('href').replace('#','');
            Admin.activeSettingsTab = tab;
            switch (tab) {
            case 'payments':
                $('.tab-additional-info').css('display','block');
                break;
            default:
                $('.tab-additional-info').css('display','none');
                break;
            }
        });
    },

    /**
     * Binds few setting field custom events
     */
    settingsPageInit: function(){
        $('select[name="emails.driver"]').on('change',function () {
            Admin.emailSettingsSwitch($(this).val());
        });
        $('select[name="storage.driver"]').on('change',function () {
            Admin.storageSettingsSwitch($(this).val());
        });
        Admin.settingsHide();
    },

    /**
     * Validate setting fields manually, as voyager doesn't apply rules on setting fields
     * @returns {boolean}
     */
    validateSettingFields: function(){
        if(Admin.activeSettingsTab === 'storage' && $('select[name="storage.driver"]').val() === 's3'){
            if(
                $('input[name="storage.aws_access_key').val().length > 0 &&
                $('input[name="storage.aws_secret_key').val().length > 0 &&
                $('input[name="storage.aws_region').val().length > 0 &&
                $('input[name="storage.aws_bucket_name').val().length > 0
            ){
                return true;
            }
            else{
                toastr.error(trans('If using S3 driver, please fill in all the fields.'));
                return false;
            }
        }
        if(Admin.activeSettingsTab === 'storage' && $('select[name="storage.driver"]').val() === 'wasabi'){
            if(
                $('input[name="storage.was_access_key').val().length > 0 &&
                $('input[name="storage.was_secret_key').val().length > 0 &&
                $('input[name="storage.was_region').val().length > 0 &&
                $('input[name="storage.was_bucket_name').val().length > 0
            ){
                return true;
            }
            else{
                toastr.error(trans('If using Wasabi driver, please fill in all the fields.'));
                return false;
            }
        }
        return true;
    },

    /**
     * Filters up emails settings based on a dropdown value
     * @param type
     */
    emailSettingsSwitch: function(type){
        Admin.settingsHide('emails');
        $('.setting-row').each(function(key,element) {
            if($(element).attr('class').indexOf(type) >= 0){
                $(element).show();
            }
        });
    },

    /**
     * Filters up storage settings based on a dropdown value
     * @param type
     */
    storageSettingsSwitch: function(type){
        Admin.settingsHide('storage');
        if(type === 's3'){
            $('.setting-row').each(function(key,element) {
                if($(element).attr('class').indexOf('aws') >= 0 || $(element).attr('class').indexOf('cdn') >= 0){
                    $(element).show();
                }
            });
        }
        else if(type === 'wasabi'){
            $('.setting-row').each(function(key,element) {
                if($(element).attr('class').indexOf('was') >= 0){
                    $(element).show();
                }
            });
        }
    },

    /**
     * Hides some settings fields by default
     * @param prefix
     */
    settingsHide: function (prefix) {
        $('.setting-row').each(function(key,element) {
            if($(element).attr('class').indexOf(prefix+'.') >= 0){
                let settingName = $(element).data('settingkey');
                switch (prefix) {
                case 'emails':
                    if(settingName !== 'emails.driver' && settingName !== 'emails.from_name' && settingName !== 'emails.from_address'){
                        $(element).hide();
                    }
                    break;
                case 'storage':
                    if(settingName !== 'storage.driver'){
                        $(element).hide();
                    }
                    break;
                }
            }
        });
    },
};
