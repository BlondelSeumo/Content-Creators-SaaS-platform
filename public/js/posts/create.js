/*
* Post create page
 */
"use strict";
/* global PostCreate, FileUpload */

$(function () {
    // Initing button save
    $('.post-create-button').on('click',function () {
        PostCreate.save('create');
    });

    $('.draft-clear-button').on('click',function () {
        PostCreate.clearDraft();
    });
    // Populating draft data, if available
    const draftData = PostCreate.populateDraftData();
    PostCreate.initPostDraft(draftData);
    // Initiating file manager
    FileUpload.initDropZone('.dropzone','/attachment/upload/post');
});

// Saving draft data before unload
window.addEventListener('beforeunload', function () {
    PostCreate.saveDraftData();
});
