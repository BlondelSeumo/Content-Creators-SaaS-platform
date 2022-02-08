/**
 * Login modal - Used for easy login from profile page
 */
"use strict";
/* global app, trans, launchToast */

$(function () {
    // eslint-disable-next-line no-undef
    if(showLoginDialog){
        LoginModal.launchModal();
    }
    $('.login-section form, .register-section form, .forgot-section form').on('submit',function () {
        LoginModal.submitForm($(this).serialize());
        return false;
    });
});

var LoginModal = {

    activeTab: 'login',
    tabs : [
        'login-section',
        'register-section',
        'forgot-section',
    ],

    /**
     * Changes modal active tab
     * @param activeTab
     */
    changeActiveTab: function(activeTab){
        LoginModal.activeTab = activeTab;
        LoginModal.tabs.map(function (tab) {
            $('.'+tab).addClass('d-none');
        });
        $('.'+activeTab+'-section').removeClass('d-none');
        LoginModal.clearFormErrors();
    },

    /**
     * Shows up login modal dialog
     */
    launchModal: function () {
        $('#login-dialog').modal('show');
    },

    /**
     * Submits the dialog form, for all form types (login/register/forgot)
     * @param data
     */
    submitForm: function (data) {
        LoginModal.clearFormErrors();
        let route = '';
        if(LoginModal.activeTab === 'forgot'){
            route = app.baseUrl + '/password/email';
        }
        else{
            route = app.baseUrl + '/'+LoginModal.activeTab;
        }
        $.ajax({
            type: 'POST',
            data: data,
            url: route,
            success: function (result) {
                if(result.success){
                    if(LoginModal.activeTab === 'forgot'){
                        launchToast('success',trans('Success'),result.message);
                        $('#login-dialog').modal('hide');
                        $('input[name="email"]').val('');
                    }
                    else{
                        window.reload();
                    }
                }
            },
            error: function (result) {
                if(result.status === 500){
                    launchToast('danger',trans('Error'),result.responseJSON.message);
                }
                // Handling case of not found user - does not return backend error for some reason
                if(result.status === 404){
                    result.responseJSON.errors = {email:[trans('These credentials do not match our records.')]} ;
                }
                $.each(result.responseJSON.errors,function (field,error) {
                    let fieldElement = $('input[name="'+field+'"]');
                    fieldElement.addClass('is-invalid');
                    fieldElement.parent().append(
                        `
                            <span class="invalid-feedback" role="alert">
                                <strong>${error}</strong>
                            </span>
                        `
                    );
                });
            }
        });
    },

    /**
     * Clears up dialog (all) form errors
     */
    clearFormErrors: function () {
        // Clearing up prev form errors
        $('.invalid-feedback').remove();
        $('input').removeClass('is-invalid');
    }

};
