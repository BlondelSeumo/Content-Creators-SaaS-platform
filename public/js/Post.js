/**
 * Main post class
 */
"use strict";
/* global Swiper, CommentsPaginator  */
/* global app */
/* global updateButtonState, redirect, trans, trans_choice, launchToast, mswpScanPage, EmojiButton */

var Post = {

    draftData:{
        text: "",
        attachments:[]
    },

    activePage: 'post',
    postIDToDelete: null,

    /**
     * Sets the current active page
     * @param page
     */
    setActivePage: function(page){
        Post.activePage = page;
    },

    /**
     * Instantiates the media module for post(s)
     * @returns {*}
     */
    initPostsMediaModule: function () {
        return new Swiper(".post-box .mySwiper", {
            // slidesPerColumn:1,
            slidesPerView:'auto',
            pagination: {
                el: ".swiper-pagination",
                // type: "fraction",
                dynamicBullets: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });
    },

    /**
     * Initiates the gallery swiper module
     * @param gallerySelector
     */
    initGalleryModule: function (gallerySelector = false) {
        mswpScanPage(gallerySelector,'mswp');
    },

    /**
     * Method used for adding a new post comment
     * @param postID
     */
    addComment: function (postID) {
        let postElement = $('*[data-postID="'+postID+'"]');
        let newCommentButton = postElement.find('.new-post-comment-area').find('button');
        updateButtonState('loading',newCommentButton);
        $.ajax({
            type: 'POST',
            data: {
                'message': postElement.find('textarea').val(),
                'post_id': postID
            },
            url: app.baseUrl+'/posts/comments/add',
            success: function (result) {
                if(result.success){
                    launchToast('success',trans('Success'),trans('Comment added'));
                    postElement.find('.no-comments-label').addClass('d-none');
                    postElement.find('.post-comments-wrapper').prepend(result.data).fadeIn('slow');
                    postElement.find('textarea').val('');
                    const commentsCount = parseInt(postElement.find('.post-comments-label-count').html()) +1;
                    postElement.find('.post-comments-label-count').html(commentsCount);
                    postElement.find('.post-comments-label').html(trans_choice('comments',commentsCount));
                    updateButtonState('loaded',newCommentButton);
                }
                else{
                    launchToast('danger',trans('Error'),result.errors[0]);
                    updateButtonState('loaded',newCommentButton);
                }
                newCommentButton.blur();
            },
            error: function (result) {
                postElement.find('textarea').addClass('is-invalid');
                if(result.status === 422) {
                    $.each(result.responseJSON.errors,function (field,error) {
                        if(field === 'message'){
                            postElement.find('textarea').parent().find('.invalid-feedback').html(error);
                        }
                    });
                    updateButtonState('loaded',newCommentButton);
                }
                else if(result.status === 403 || result.status === 404){
                    launchToast('danger',trans('Error'), result.responseJSON.message);
                }
                newCommentButton.blur();
            }
        });
    },

    /**
     * Toggle post comment area visibility
     * @param post_id
     */
    showPostComments: function(post_id){
        let postElement = $('*[data-postID="'+post_id+'"] .post-comments');

        // No pagination needed - on feed
        if(typeof postVars === 'undefined'){
            CommentsPaginator.nextPageUrl = '';
        }

        if(CommentsPaginator.nextPageUrl === ''){
            CommentsPaginator.init(app.baseUrl+'/posts/comments',postElement.find('.post-comments-wrapper'));
        }

        const isHidden = postElement.hasClass('d-none');
        if(isHidden){
            if(!postElement.hasClass('latest-comments-loaded')){
                CommentsPaginator.loadResults(post_id,9);
            }
            postElement.removeClass('d-none');
            postElement.addClass('latest-comments-loaded');
        }
        else{
            postElement.addClass('d-none');
        }

        Post.initEmojiPicker(post_id);

    },

    /**
     * Instantiates the emoji picker for any given post
     * @param post_id
     */
    initEmojiPicker: function(post_id){
        try{
            const button = document.querySelector('*[data-postID="'+post_id+'"] .trigger');
            const picker = new EmojiButton(
                {
                    position: 'top-end',
                    theme: app.theme,
                    autoHide: false,
                    rows: 4,
                    recentsCount: 16,
                    emojiSize: '1.3em',
                    showSearch: false,
                }
            );
            picker.on('emoji', emoji => {
                document.querySelector('input').value += emoji;
                $('*[data-postID="'+post_id+'"] .comment-textarea').val($('*[data-postID="'+post_id+'"] .comment-textarea').val() + emoji);

            });
            button.addEventListener('click', () => {
                picker.togglePicker(button);
            });
        }
        catch (e) {
            // Maybe avoid ending up in here entirely
            // console.error(e)
        }

    },

    /**
     * Add new reaction
     * Can be used for post or comment reactionn
     * @param type
     * @param id
     */
    reactTo: function (type,id) {
        let reactElement = null;
        let reactionsCountLabel = null;
        let reactionsLabel = null;
        if(type === 'post'){
            reactElement = $('*[data-postID="'+id+'"] .post-footer .react-button');
            reactionsCountLabel = $('*[data-postID="'+id+'"] .post-footer .post-reactions-label-count');
            reactionsLabel = $('*[data-postID="'+id+'"] .post-footer .post-reactions-label');
        }
        else{
            reactElement = $('*[data-commentID="'+id+'"] .react-button');
            reactionsCountLabel = $('*[data-commentID="'+id+'"] .comment-reactions-label-count');
            reactionsLabel = $('*[data-commentID="'+id+'"] .comment-reactions-label');
        }
        const didReact = reactElement.hasClass('active');
        if(didReact){
            reactElement.removeClass('active');
        }
        else{
            reactElement.addClass('active');
        }
        $.ajax({
            type: 'POST',
            data: {
                'type': type,
                'action': (didReact === true ? 'remove' : 'add'),
                'id': id
            },
            dataType: 'json',
            url: app.baseUrl+'/posts/reaction',
            success: function (result) {
                if(result.success){
                    let count = parseInt(reactionsCountLabel.html());
                    if(didReact){
                        count--;
                    }
                    else{
                        count++;
                    }
                    reactionsCountLabel.html(count);
                    reactionsLabel.html(trans_choice('likes',count));
                    launchToast('success',trans('Success'),result.message);
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
     * Appends replied username to comment field
     * @param username
     */
    addReplyUser: function(username){
        $('.new-post-comment-area textarea').val($('.new-post-comment-area textarea').val()+ ' @' +username+ ' ');
    },

    /**
     * Shows up the post removal confirmation box
     * @param post_id
     */
    confirmPostRemoval: function (post_id) {
        Post.postIDToDelete = post_id;
        $('#post-delete-dialog').modal('show');
    },

    /**
     * Removes user post
     */
    removePost: function(){
        let postElement = $('*[data-postID="'+Post.postIDToDelete+'"]');
        $.ajax({
            type: 'DELETE',
            data: {
                'id': Post.postIDToDelete
            },
            dataType: 'json',
            url: app.baseUrl+'/posts/delete',
            success: function (result) {
                if(result.success){
                    if(Post.activePage !== 'post'){
                        $('#post-delete-dialog').modal('hide');
                        postElement.fadeOut("normal", function() {
                            $(this).remove();
                        });
                    }
                    else{
                        if(document.referrer.indexOf('feed') > 0){
                            redirect(app.baseUrl + '/feed');
                        }
                        else{
                            redirect(document.referrer);
                        }
                    }
                    launchToast('success',trans('Success'),result.message);

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
     * Adds or removes user bookmarks
     * @param id
     */
    togglePostBookmark: function (id) {
        let reactElement = $('*[data-postID="'+id+'"] .bookmark-button');
        const isBookmarked = reactElement.hasClass('active');
        $.ajax({
            type: 'POST',
            data: {
                'action': (isBookmarked === true ? 'remove' : 'add'),
                'id': id
            },
            dataType: 'json',
            url: app.baseUrl+'/posts/bookmark',
            success: function (result) {
                if(result.success){
                    if(isBookmarked){
                        reactElement.removeClass('active');
                        reactElement.html(trans('Bookmark this post'));
                    }
                    else{
                        reactElement.addClass('active');
                        reactElement.html(trans('Remove this bookmark'));
                    }

                    launchToast('success',trans('Success'),result.message);
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
     * Disabling right for posts ( if site wise setting is set to do it )
     */
    disablePostsRightClick: function () {
        $(".post-media, .pswp__item").unbind('contextmenu');
        $(".post-media, .pswp__item").on("contextmenu",function(){
            return false;
        });
    }

};

