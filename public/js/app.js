/**
 *
 * Main App Component
 *
 */
"use strict";
/* global app, user, pusher, Pusher, PostsPaginator, notifications */

// Init
$(function () {

    log('🚀 AK2 Loaded');

    if(app.showCookiesBox !== null){
        window.cookieconsent.initialise({
            "theme": "classic",
            "position": "bottom-right",
            "palette": {
                "popup": {
                    "background": "#efefef",
                    "text": "#404040"
                },
                "button": {
                    "background": "#007BFF",
                    "text": "#ffffff"
                }
            },
            content: {
                message: '🍪 This website uses cookies to improve your experience.'
            },
        });
    }

    // Auto-including the CSRF token in all AJAX Requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
    });

    // Globally handling AJAX requests, especially for handling expired tokens and sesisions
    // TODO: Decide if this should be left enabled on prod mode or if it would help clients more
    $(document).ajaxError(function (event, jqXHR) {
        if (jqXHR.status === 0) {
            log('Not connect.n Verify Network.', 'error');
        } else if (jqXHR.status === 404) {
            log('Requested page not found. [404]', 'error');
        } else if (jqXHR.status === 500) {
            log('Internal Server Error [500].', 'error');
        } else if (jqXHR.status === 401) {
            log('Session expired. Redirecting you to refresh the session.', 'error');
            redirect(app.baseUrl);
        } else if (jqXHR.status === 408) {
            reload();
        } else {
            log('Uncaught Error.n' + jqXHR.responseText, 'error');
        }
    });

    // Displaying error messages for expired sessions
    if (app.sessionStatus === 'expired') {
        launchToast('info', 'Session expired ', 'Page refreshed', 'now');
    }

    // Dark mode switcher event
    $('.dark-mode-switcher').on('click', function () {
        let currentTheme = getCookie('app_theme');
        if (currentTheme === 'dark') {
            setCookie('app_theme', 'light', 365);
        } else {
            setCookie('app_theme', 'dark', 365);
        }
        reload();
    });

    // RTL mode switcher event
    $('.rtl-mode-switcher').on('click', function () {
        let currentTheme = getCookie('app_rtl');
        if (currentTheme === 'rtl') {
            setCookie('app_rtl', 'ltr', 365);
        } else {
            setCookie('app_rtl', 'rtl', 365);
        }
        reload();
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    $('.to-tooltip').tooltip();

    // Initialize user connection to pusher
    try {
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = pusher.logging;
        var pusherClient = new Pusher(pusher.key, {
            cluster: pusher.cluster
        });
        var channel = pusherClient.subscribe(user.username);
        channel.bind('new-notification', function (data) {
            launchToast('success', 'Success', data.message);

            if (window.location.href !== null && window.location.href.indexOf('/my/notifications') >= 0) {
                notifications.updateUserNotificationsList(this.getNotificationsActiveFilter());
            }
        });
    } catch (e) {
        // eslint-disable-next-line no-console
        // console.warn(e);
    }
});

$(window).scroll(function () {
    if(typeof skipDefaultScrollInits === 'undefined'){
        initStickyComponent('.side-menu','sticky');
    }
});

/**
 * Log function sugar syntax
 * @param v
 */
function log(v,type = 'log') {
    if(app.debug){
        switch (type) {
        case 'info':
            // eslint-disable-next-line no-console
            console.info(v);
            break;
        case 'log':
            // eslint-disable-next-line no-console
            console.log(v);
            break;
        case 'warn':
            // eslint-disable-next-line no-console
            console.warn(v);
            break;
        case 'error':
            // eslint-disable-next-line no-console
            console.error(v);
            break;
        }
    }
    return true;
}

/**
 * Redirect function
 * @param url
 */
function redirect(url) {
    window.location.href = url;
}

/**
 * Submits the search form
 */
// eslint-disable-next-line no-unused-vars
function submitSearch() {
    $('.search-box-wrapper').submit();
}

/**
 * Page reload function
 */
function reload() {
    return window.location.reload();
}

/**
 * Copy to clipboard function
 * @param textToCopy
 */
function copyToClipboard(textToCopy) {
    let $temp = $("<textarea>");
    $("body").append($temp);
    $temp.val(textToCopy).select();
    document.execCommand("copy");
    $temp.remove();
}

/**
 * Attaches scroll handlers & sticky behaviour to desired components
 * @param component
 * @param stickyClass
 */
function initStickyComponent(component,stickyClass) {
    let sticky = false;
    let top = $(window).scrollTop();
    if ($(".main-wrapper").offset().top < top) {
        $(component).addClass(stickyClass);
        // eslint-disable-next-line no-unused-vars
        sticky = true;
    } else {
        $(".side-menu, .suggestions-box").removeClass(stickyClass);
    }
}

/**
 * Go to login via UI redirect
 */
// eslint-disable-next-line no-unused-vars
function goToLogin() {
    redirect(app.baseUrl + '/login');
}

/**
 * Set cookie
 * @param key
 * @param value
 * @param expiry
 */
function setCookie(key, value, expiry) {
    var expires = new Date();
    expires.setTime(expires.getTime() + (expiry * 24 * 60 * 60 * 1000));
    document.cookie = key + '=' + value + ';expires=' + expires.toUTCString() + ';path=/';
}

/**
 * Get cookie value
 * @param key
 * @returns {any}
 */
function getCookie(key) {
    var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
    return keyValue ? keyValue[2] : null;
}

/**
 * Delete cookie
 * @param key
 */
// eslint-disable-next-line no-unused-vars
function eraseCookie(key) {
    var keyValue = getCookie(key);
    setCookie(key, keyValue, '-1');
}

/**
 * Reload themes on the fly
 */
// eslint-disable-next-line no-unused-vars
function reloadTheme() {
    let appTheme = 'css/bootstrap/bootstrap';
    let currentTheme = getCookie('app_theme');
    let currentRTLSetting = getCookie('app_rtl');
    if (currentRTLSetting === 'rtl') {
        appTheme += '.rtl';
    }

    if (currentTheme === 'dark') {
        appTheme += '.dark';
    }
    appTheme += ".css";
    $('#app-theme').attr('href', appTheme);
}

/**
 * Launches custom, stackable and dismisable toasts
 * @param type
 * @param title
 * @param message
 * @param subtitle
 */
function launchToast(type, title, message, subtitle = '') {
    $.toast({
        type: '',
        title: title,
        subtitle: subtitle,
        content: message,
        dismissible: true,
        indicator: {
            type: type
        },
        delay: 5000,
    });
}

/**
 * Opens up device share API or fallbacks to URL copy
 * @param url
 */
// eslint-disable-next-line no-unused-vars
function shareOrCopyLink(url = false) {
    if (url === false) {
        url = window.location.href;
    }
    if (navigator.share) {
        navigator.share({
            title: document.title,
            url: url
        })
            // eslint-disable-next-line no-console
            .then(() => console.log('Successful share'))
            // eslint-disable-next-line no-console
            .catch(error => console.log('Error sharing:', error));
    } else {
        copyToClipboard(url);
        launchToast('success', 'Success ', 'Link copied to clipboard.', 'now');
    }
}

/**
 * Auto Adjusts textareas on resize
 * @param el
 */
// eslint-disable-next-line no-unused-vars
function textAreaAdjust(el) {
    el.style.height = (el.scrollHeight > el.clientHeight) ? (el.scrollHeight) + "px" : "45px";
}

/**
 * Filters up user received notifications ( via sockets )
 * @returns {string}
 */
// eslint-disable-next-line no-unused-vars
function getNotificationsActiveFilter() {
    let activeType = '';
    // get active filter if exists
    if (window.location.href.indexOf('/likes') >= 0) {
        activeType = '/likes';
    } else if (window.location.href.indexOf('/messages') >= 0) {
        activeType = '/messages';
    } else if (window.location.href.indexOf('/subscriptions') >= 0) {
        activeType = '/subscriptions';
    } else if (window.location.href.indexOf('/tips') >= 0) {
        activeType = '/tips';
    } else if (window.location.href.indexOf('/promos') >= 0) {
        activeType = '/promos';
    }

    return activeType;
}

/**
 * Method used for translating locale strings
 * @param key
 * @param replace
 * @returns {T|*}
 */
// eslint-disable-next-line no-unused-vars
function trans(key, replace = {})
{
    let translation = window.translations[key];
    if(translation === null){ // If no translation available, return the ( default - en ) key
        return key;
    }
    for (var placeholder in replace) {
        translation = translation.replace(`:${placeholder}`, replace[placeholder]);
    }
    if(typeof translation === 'undefined'){
        return key;
    }
    return translation;
}

/**
 * Method used for translating locale strings
 * Supports multiple choices translations
 * @param key
 * @param replace
 * @returns {T|*}
 */
// eslint-disable-next-line no-unused-vars
function trans_choice(key, count = 1, replace = {})
{
    let keyValue = window.translations[key];
    if(typeof keyValue === 'undefined'){
        return key;
    }
    const translations = keyValue.split('|');
    let translation = count > 1 || count === 0 ? translations[1] : translations[0];

    for (var placeholder in replace) {
        translation = translation.replace(`:${placeholder}`, replace[placeholder]);
    }
    return translation;
}

/**
 * Updates button state, adding loading icon to it and disabling it
 * @param state
 * @param buttonElement
 */
// eslint-disable-next-line no-unused-vars
function updateButtonState(state, buttonElement, buttonContent = false){
    if(state === 'loaded'){
        if(buttonContent){
            buttonElement.html(buttonContent);
        }
        else{
            buttonElement.html('<div class="d-flex justify-content-center align-items-center"><ion-icon name="paper-plane"></ion-icon></div>');
        }
        buttonElement.removeClass('disabled');
    }
    else{
        buttonElement.html('<div class="d-flex justify-content-center align-items-center">\n' +
            '<div class="spinner-border text-primary spinner-border-sm" role="status">\n' +
            '  <span class="sr-only">'+trans('Loading...')+'</span>\n' +
            '</div>\n' +
            '</div>');
        buttonElement.addClass('disabled');
    }
}

/**
 * Re-sends the user email verification
 * @param callback
 */
// eslint-disable-next-line no-unused-vars
function sendEmailConfirmation(callback = function(){}){
    $('.unverified-email-box').attr('onClick','');
    $.ajax({
        url:app.baseUrl +'/resendVerification',
        type:'POST',
        success : function(){
            $('.unverified-email-box').fadeOut();
            launchToast('success', trans('Success'), trans('Confirmation email sent. Please check your inbox and spam folder.'), 'now');
            callback();
        },
        error: function () {

        }
    });
}

/**
 * Preps a data beacon data sample, to be saved before page unload
 * @returns {FormData}
 */
// eslint-disable-next-line no-unused-vars
function prepBeaconDataSample(){
    var fd = new FormData();
    fd.append('prevPage', PostsPaginator.currentPage);
    return fd;
}

/**
 * Returns current bootstrap breakpoint to the JS side
 * @returns {{name: (string|string), index: number}|null}
 */
// eslint-disable-next-line no-unused-vars
function bootstrapDetectBreakpoint() {
    // cache some values on first call
    let breakpointNames = ["xl", "lg", "md", "sm", "xs"];
    let breakpointValues = [];
    for (const breakpointName of breakpointNames) {
        breakpointValues[breakpointName] = window.getComputedStyle(document.documentElement).getPropertyValue('--breakpoint-' + breakpointName);
    }
    let i = breakpointNames.length;
    for (const breakpointName of breakpointNames) {
        i--;
        if (window.matchMedia("(min-width: " + breakpointValues[breakpointName] + ")").matches) {
            return {name: breakpointName, index: i};
        }
    }
    return null;
}
