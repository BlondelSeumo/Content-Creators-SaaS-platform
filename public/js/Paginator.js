/**
 * Paginator component - used for posts (feed+profile) pagination
 */
"use strict";
/* global Post */

var Paginator = {

    Posts : {
        nextPageUrl: '',
        container: '',
    },

    /**
     * Instantiates the feed paginator component
     * @param route
     * @param container
     */
    init: function (route,container) {
        Paginator.nextPageUrl = route;
        Paginator.container = container;
        Paginator.initScrollLoad();
    },

    /**
     * Load paginated results
     */
    loadResults: function () {
        $.ajax({
            type: 'GET',
            url: Paginator.nextPageUrl,
            dataType: 'json',
            success: function(result) {
                if(result.success){

                    // Appending the items & incrementing the counter
                    Paginator.appendPostResults(result.data.posts);

                    if(result.data.hasMore === false){
                        Paginator.unbindPaginator();
                    }
                    else{
                        Paginator.nextPageUrl = result.data.next_page_url;
                    }
                    // Re-init stuff ( eg: Gallery, comments etc )
                }
                else{
                    // Handle error-ed requests
                }
            }
        });
    },

    /**
     * Append new posts to the feed box
     * @param posts
     */
    appendPostResults: function(posts){
        // Building up the HTML array
        let htmlOut = [];
        let postIDs = [];
        $.map(posts,function (post) {
            htmlOut.push(post.html);
            postIDs.push(post.id);
        });
        // Appending the output
        $(Paginator.container).append(htmlOut.join('<hr>') + '<hr>').fadeIn('slow');
        // Init swiper for posts
        Post.initPostsMediaModule();
        // Init gallery module for each post
        Paginator.initPostsGalleries(postIDs);
    },

};
