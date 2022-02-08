/**
 * Settings profile component
 */
"use strict";
/* global app, mediaSettings, launchToast */

$(function () {
    ProfileSettings.initUploader('avatar');
    ProfileSettings.initUploader('cover');

    $('.avatar-holder').on('tap', function(e) {
        e.preventDefault();
        $('.avatar-holder .actions-holder').toggleClass('d-none');
    });

    $('.avatar-holder').on({
        mouseenter: function() {
            $('.avatar-holder .actions-holder').removeClass('d-none');
        },
        mouseleave: function() {
            $('.avatar-holder .actions-holder').addClass('d-none');
        }
    });

    $('.profile-cover-bg').on('tap', function(e) {
        e.preventDefault();
        $('.profile-cover-bg .actions-holder').toggleClass('d-none');
    });

    $('.profile-cover-bg').on({
        mouseenter: function() {
            $('.profile-cover-bg .actions-holder').removeClass('d-none');
        },
        mouseleave: function() {
            $('.profile-cover-bg .actions-holder').addClass('d-none');
        }
    });

});

/**
 * ProfileSettings Class
 */
var ProfileSettings = {

    dropzones : {},

    /**
     * Instantiates the media uploader for avatar / cover
     */
    initUploader:function (type) {
        let selector = '';
        selector = '.profile-cover-bg';
        if(type === 'avatar'){
            selector = '.avatar-holder';
        }
        ProfileSettings.dropzones[type] = new window.Dropzone(selector, {
            url: app.baseUrl + '/my/settings/profile/upload/'+type,
            previewTemplate: document.querySelector('.dz-preview').innerHTML.replace('d-none', ''),
            paramName: "file", // The name that will be used to transfer the file
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            clickable:[`${selector} .upload-button`],
            maxFilesize: mediaSettings.max_file_upload_size, // MB
            addRemoveLinks: true,
            dictRemoveFile: "x",
            acceptedFiles: mediaSettings.allowed_file_extensions,
            autoDiscover: false,
            sending: function(file) {
                file.previewElement.innerHTML = "";
            },
            success: function(file, response) {
                $(selector + ' .card-img-top').attr('src',response.assetSrc);
                $('.side-menu .user-avatar, .sidebar .user-avatar').attr('src',response.assetSrc);
                file.previewElement.innerHTML = "";
            },
            error: function(file, errorMessage) {
                if(typeof errorMessage === 'string'){
                    launchToast('danger','Error ',errorMessage,'now');
                }
                else{
                    launchToast('danger','Error ',errorMessage.errors.file,'now');
                }
                file.previewElement.innerHTML = "";
            }
        });
    },

    /**
     * Removes the user asset ( avatar / cover )
     * @param type
     */
    removeUserAsset(type){
        $.ajax({
            type: 'POST',
            url: app.baseUrl + '/my/settings/profile/remove/'+type,
            success: function (result) {
                launchToast('success','Success ',result.message,'now');
                $('.profile-cover-bg img').attr('src', result.data.cover);
                $('.avatar-holder img').attr('src', result.data.avatar);
            },
            error: function (result) {
                // eslint-disable-next-line no-console
                console.warn(result);
            }
        });
    }

};
