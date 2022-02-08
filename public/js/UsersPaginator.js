/**
 * Paginator component - used for posts (feed+profile) pagination
 */
"use strict";
/* global app, Post, paginatorConfig */

var UsersPaginator = {

    isFetching: false,
    nextPageUrl: '',
    prevPageUrl: '',
    currentPage: null,
    container: '',
    method: 'GET',

    /**
     * Initiates the component
     * @param route
     * @param container
     * @param method
     */
    init: function (route,container,method='GET') {
        UsersPaginator.nextPageUrl = route;
        UsersPaginator.prevPageUrl = paginatorConfig.prev_page_url;
        UsersPaginator.currentPage = paginatorConfig.current_page;
        UsersPaginator.container = container;
        UsersPaginator.method = method;
    },

    /**
     * Loads (new) up paginated results
     * @param direction
     */
    loadResults: function (direction='next') {
        if(UsersPaginator.isFetching === true){
            return false;
        }
        UsersPaginator.isFetching = true;
        let url = UsersPaginator.nextPageUrl;
        if(direction === 'prev'){
            url = UsersPaginator.prevPageUrl;
        }
        UsersPaginator.toggleLoadingIndicator(true);
        $.ajax({
            type: UsersPaginator.method,
            url: url,
            dataType: 'json',
            success: function(result) {
                if(result.success){

                    if(result.data.hasMore === false){
                        UsersPaginator.unbindPaginator();
                    }
                    if(direction !== 'prev'){
                        UsersPaginator.nextPageUrl = result.data.next_page_url;
                    }
                    else{
                        UsersPaginator.prevPageUrl = result.data.prev_page_url;
                        $('.reverse-paginate-btn').find('button').removeClass('disabled');
                    }

                    if(result.data.prev_page_url === null){
                        $('.reverse-paginate-btn').fadeOut("fast", function() {});
                    }

                    // Appending the items & incrementing the counter
                    UsersPaginator.appendPostResults(result.data.users, direction);
                    UsersPaginator.isFetching = false;
                }
                else{
                    // Handle error-ed requests
                    UsersPaginator.isFetching = false;
                }
                UsersPaginator.toggleLoadingIndicator(false);
            }
        });
    },

    /**
     * Toggles the loading indicator
     * @param loading
     */
    toggleLoadingIndicator: function(loading = false){
        if(loading === true){
            $('.posts-loading-indicator .spinner').removeClass('d-none');
        }
        else{
            $('.posts-loading-indicator .spinner').addClass('d-none');
        }
    },

    /**
     * Appends new posts to the feed container
     * @param posts
     * @param direction
     */
    appendPostResults: function(posts, direction = 'next'){
        // Building up the HTML array
        let htmlOut = [];
        let postIDs = [];
        $.map(posts,function (post) {
            htmlOut.push(post.html);
            postIDs.push(post.id);
        });

        // Appending the output
        if(direction === 'next'){
            $(UsersPaginator.container).append(htmlOut.join('')).fadeIn('slow');
        }else{
            $(UsersPaginator.container).prepend(htmlOut.join('')).fadeIn('slow');
        }

        // Init swiper for posts
        Post.initPostsMediaModule();
        if(app.feedDisableRightClickOnMedia !== null){
            Post.disablePostsRightClick();
        }

    },

    /**
     * Initiates infinite scrolling
     */
    initScrollLoad: function(){
        window.onscroll = function() {
            if (((window.innerHeight + window.scrollY + 2) * window.devicePixelRatio.toFixed(2)) >= document.body.offsetHeight * window.devicePixelRatio.toFixed(2)) {
                UsersPaginator.loadResults();
            }
        };
    },

    /**
     * Unbinds the paginator infinite scrolling behaviour
     */
    unbindPaginator: function () {
        UsersPaginator.nextPageUrl = '';
        window.onscroll = function() {};
    },

};
