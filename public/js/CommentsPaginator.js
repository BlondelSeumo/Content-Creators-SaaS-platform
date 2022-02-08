/**
 * Paginator component - used for posts (feed+profile) pagination
 */
"use strict";
/* global trans, launchToast */

var CommentsPaginator = {

    nextPageUrl: '',
    container: '',

    init: function (route,container) {
        CommentsPaginator.nextPageUrl = route;
        CommentsPaginator.container = container;
    },

    /**
     * Loads up paginated results and appends them to the page
     * @param post_id
     * @param limit
     */
    loadResults: function (post_id, limit = 9) {
        let postElement = $('*[data-postID="'+post_id+'"] .post-comments');
        $.ajax({
            type: 'GET',
            data: {
                'post_id': post_id,
                'limit': limit,
            },
            url: CommentsPaginator.nextPageUrl,
            success: function (result) {
                if(result.data.comments.length > 0){
                    let htmlOut = [];
                    $.map(result.data.comments,function (comment) {
                        htmlOut.push(comment.html);
                    });
                    CommentsPaginator.appendCommentsResults(result.data.comments);
                    if(result.data.hasMore){
                        postElement.find('.show-all-comments-label').removeClass('d-none');
                        CommentsPaginator.nextPageUrl = result.data.next_page_url;
                    }
                    else{
                        postElement.find('.show-all-comments-label').addClass('d-none');
                    }
                }
                else{
                    postElement.find('.no-comments-label').removeClass('d-none');
                }
                $(CommentsPaginator.container).find('.comments-loading-box').addClass('d-none'); // Hiding out the loading element
            },
            error: function (result) {
                launchToast('danger',trans('Error'),result.responseJSON.message);
            }
        });
    },

    /**
     * Appends the new comments to the comments box
     * @param comments
     */
    appendCommentsResults: function(comments){
        // Building up the HTML array
        let htmlOut = [];
        let commentIDs = [];
        $.map(comments,function (comments) {
            htmlOut.push(comments.html);
            commentIDs.push(comments.id);
        });
        // Appending the output
        if(typeof CommentsPaginator.container === 'string'){
            $(CommentsPaginator.container).append(htmlOut.join("\n")).fadeIn('slow');
        }
        else{
            CommentsPaginator.container.append(htmlOut.join("\n")).fadeIn('slow');
        }
    },

};
