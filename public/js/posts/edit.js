/*
* Post create page
 */
"use strict";
/* global FileUpload, PostCreate, PostCreate, postData */

$(function () {
    // Initing button save
    $('.post-create-button').on('click',function () {
        PostCreate.save('update',postData.id);
    });
    PostCreate.initPostDraft(postData,'edit');
    PostCreate.postPrice = postData.price;
    FileUpload.initDropZone('.dropzone','/attachment/upload/post');
});
