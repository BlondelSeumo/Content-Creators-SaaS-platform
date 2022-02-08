/*
* Feed page & component
 */
"use strict";
/* global app, user, listVars, profileVars, launchToast, redirect, trans */

$(function () {

});

var Lists = {

    state : {
        listMemberToDelete: null,
    },

    /**
     * Shows up confirmation box for list clear
     */
    showListClearConfirmation: function(){
        $('#list-clear-dialog').modal('show');
    },

    /**
     * Clears all members within a list
     */
    clearList: function(){
        $.ajax({
            type: 'POST',
            data: { list_id: listVars.list_id },
            dataType: 'json',
            url: app.baseUrl + '/my/lists/members/clear',
            success: function (result) {
                // eslint-disable-next-line no-unused-vars
                let element = $('*[data-memberuserid="'+Lists.state.listMemberToDelete+'"]');
                if(result.success){
                    redirect(app.baseUrl+'/my/lists');
                }
                else{
                    launchToast('danger',trans('Error'),result.errors[0]);
                }
            },
            error: function (result) {
                launchToast('danger',trans('Error'),result.responseJSON.message);
            }
        });
    },

    /**
     * Removes list member
     */
    removeListMember: function(){
        const requestMethod = 'DELETE';
        const requestUrl = app.baseUrl + '/my/lists/members/delete';
        let data = {
            'list_id': listVars.list_id,
            'user_id': Lists.state.listMemberToDelete
        };
        $('#list-member-delete-dialog').modal('show');
        $.ajax({
            type: requestMethod,
            data: data,
            dataType: 'json',
            url: requestUrl,
            success: function (result) {
                if(result.success){
                    let element = $('*[data-memberuserid="'+Lists.state.listMemberToDelete+'"]');
                    launchToast('success',trans('Success'),result.message);
                    $('#list-member-delete-dialog').modal('hide');
                    element.parent().fadeOut(300,function(){
                        $(this).remove();
                    });
                }
                else{
                    launchToast('danger',trans('Error'),result.errors[0]);
                }
            },
            error: function (result) {
                launchToast('danger',trans('Error'),result.responseJSON.message);
            }
        });
    },

    /**
     * Shows up confirmation box for removing a user from a list
     * @param member_id
     */
    showListMemberRemoveModal: function(member_id){
        $('#list-member-delete-dialog').modal('show');
        Lists.state.listMemberToDelete = member_id;
    },

    /**
     * Shows up list create dialog
     */
    showListAddModal: function(){
        $('#list-add-user-dialog').modal('show');
        Lists.initListsCheckboxes();
    },

    /**
     * Initiates list checkboxes ( on profile )
     */
    initListsCheckboxes: function(){
        let listCheckboxes = $('input[type=checkbox]');
        listCheckboxes.unbind('click');
        listCheckboxes.on('click',function () {
            let type = 'add';
            if(!$(this).is(':checked')){
                type = 'remove';
            }
            Lists.updateListMember(
                $(this).data('listid'),
                profileVars.user_id,
                type
            );
        });
    },

    /**
     * Updates list member
     * @param list_id
     * @param user_id
     * @param type
     * @param showMessages
     * @returns {boolean}
     */
    updateListMember: function(list_id, user_id, type, showMessages = true){
        let data = {
            'list_id': list_id,
            'user_id': user_id
        };
        let requestMethod = 'POST';
        let requestUrl = app.baseUrl + '/my/lists/members/save';
        if(type !== 'add'){
            requestMethod = 'DELETE';
            requestUrl = app.baseUrl + '/my/lists/members/delete';
        }
        data.returnData = showMessages;
        $.ajax({
            type: requestMethod,
            data: data,
            url: requestUrl,
            success: function (result) {
                let element = $('*[data-listid="'+list_id+'"]');
                if(type === 'add'){
                    if(showMessages){
                        $('.lists-wrapper').append(result.data);
                        launchToast('success',trans('Success'),result.message);
                        element.parent().find('.list-subtitle').html(result.data.members_count.toString() + ' members - ' + result.data.posts_count.toString() + ' posts');
                    }

                }
                else{
                    if(showMessages){
                        launchToast('success',trans('Success'),result.message);
                        element.parent().find('.list-subtitle').html(result.data.members_count.toString() + ' members - ' + result.data.posts_count.toString() + ' posts');
                    }

                }

                if(!showMessages){
                    window.reload();
                }

            },
            error: function (result) {
                launchToast('danger',trans('Error'),result.responseJSON.message);
            }
        });
        return true;
    },

    /**
     * Posts user report
     * @param user_id
     * @param post_id
     * @param type
     * @param details
     */
    postReport: function(user_id,post_id,type,details){
        $.ajax({
            type: 'POST',
            data: {
                user_id,
                post_id,
                type,
                details,
            },
            url: app.baseUrl + '/report/content',
            success: function () {
                launchToast('success',trans('Success'),trans('Report submitted'));
                $('#report-user-post').modal('hide');
                $('#post_report_details').val('');
            },
            error: function (result) {
                launchToast('danger',trans('Error'),result.responseJSON.message);
            }
        });
    },

    /**
     * Shows up user report box
     * @param user_id
     * @param post_id
     */
    showReportBox: function(user_id,post_id){
        let dialogElement = $('#report-user-post');
        dialogElement.modal('show');
        $('.submit-report-button').unbind();
        $('.submit-report-button').on('click',function () {
            Lists.postReport(user_id,post_id,$('#reasonExamples').val(),$('#post_report_details').val());
        });
    },

    /**
     * Shows up list management confirmation box
     * @param type
     * @param user_id
     */
    showListManagementConfirmation: function(type ,user_id){
        let dialogElement = $('#post-lists-management-dialog');
        dialogElement.modal('show');
        $('.post-list-management-btn').unbind();

        if(type === 'unfollow'){
            dialogElement.find('.block-user-label').addClass('d-none');
            dialogElement.find('.unfollow-user-label').removeClass('d-none');
            $('.post-list-management-btn').on('click',function () {
                Lists.manageList(user.lists['following'], user_id, 'unfollow');
            });
        }
        else if(type === 'block'){
            dialogElement.find('.block-user-label').removeClass('d-none');
            dialogElement.find('.unfollow-user-label').addClass('d-none');
            $('.post-list-management-btn').on('click',function () {
                Lists.manageList(user.lists['blocked'], user_id, 'block');
            });
        }

    },

    /**
     * Manages a list method
     * @param list_id
     * @param user_id
     * @param type
     */
    manageList: function(list_id, user_id, type){
        let innerType = '';
        switch (type) {
        case 'unfollow':
            innerType = 'remove';
            break;
        case 'block':
            innerType = 'add';
            break;
        }

        Lists.updateListMember(list_id,user_id,innerType, false);
    },

    /**
     * Method used for removing a list
     */
    removeList: function(){
        $.ajax({
            type: 'DELETE',
            data: {
                'id': listVars.list_id
            },
            dataType: 'json',
            url: app.baseUrl+'/my/lists/delete',
            success: function (result) {
                if(result.success){
                    redirect(app.baseUrl+'/my/lists');
                }
                else{
                    launchToast('danger',trans('Error'),result.errors[0]);
                }
            },
            error: function (result) {
                launchToast('danger',trans('Error'),result.responseJSON.message);
            }
        });
    },

    /**
     * Shows up lists delete confirmation
     */
    showListDeleteConfirmation: function(){
        $('#list-delete-dialog').modal('show');
    },

    /**
     * Shows up list management dialog
     * @param mode
     */
    showListEditDialog: function (mode) {
        $('#list-update-dialog').modal('show');
        if(mode === 'edit' && $('#list-name').val().length === 0){
            $('#list-name').val(listVars.name);
        }
    },

    /**
     * Method used for updating a list
     * @param type
     */
    updateList: function (type) {
        let data = {
            'name': $('#list-name').val(),
            'type': type
        };
        if(type === 'edit'){
            data.list_id = listVars.list_id;
        }
        $.ajax({
            type: 'POST',
            data: data,
            url: app.baseUrl + '/my/lists/save',
            success: function (result) {
                if(type === 'create'){
                    $('.lists-wrapper').append('<hr class="my-2">');
                    $('.lists-wrapper').append(result.data);
                    $('#list-update-dialog').modal('hide');
                    launchToast('success',trans('Success'),trans('List added')+'.');
                    $('#list-name').val('');
                }
                else{
                    $('.list-name-label').html($('#list-name').val());
                    $('#list-update-dialog').modal('hide');
                    launchToast('success',trans('Success'),trans('List renamed')+'.');
                }
            },
            error: function (result) {
                $.each(result.responseJSON.errors,function (field) {
                    if(field === 'name'){
                        $('#list-name').addClass('is-invalid');
                        $('#list-name').focus();
                    }
                });
            }
        });
    },

    /**
     * Manage user follows action
     * @returns {boolean}
     */
    manageFollowsAction: function (userId) {
        $.ajax({
            type: 'POST',
            data: {
                user_id: userId,
            },
            url: app.baseUrl + '/my/lists/manage/follows',
            success: function (result) {
                // eslint-disable-next-line no-undef
                $('.manage-follows-text').text(result.text);
                window.reload();
            }
        });
    }

};
