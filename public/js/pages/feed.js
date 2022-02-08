/*
* Feed page & component
 */
"use strict";
/* global app, initialPostIDs, paginatorConfig, PostsPaginator, Post, SuggestionsSlider, initStickyComponent, getCookie */

$(function () {
    if(typeof paginatorConfig !== 'undefined'){
        if((paginatorConfig.total > 0 && paginatorConfig.total > paginatorConfig.per_page) && paginatorConfig.hasMore) {
            PostsPaginator.initScrollLoad();
        }
        PostsPaginator.init(paginatorConfig.next_page_url, '.posts-wrapper');
    }
    else{
        // eslint-disable-next-line no-console
        console.error('Pagination failed to initialize.');
    }
    PostsPaginator.initPostsGalleries(initialPostIDs);
    Post.setActivePage('feed');
    if(getCookie('app_prev_post') !== null){
        PostsPaginator.scrollToLastPost(getCookie('app_prev_post'));
    }
    Post.initPostsMediaModule();
    SuggestionsSlider.init();
    if(app.feedDisableRightClickOnMedia !== null){
        Post.disablePostsRightClick();
    }
});

window.onunload = function(){
    // Reset scrolling to top
    window.scrollTo(0,0);
};

$(window).scroll(function () {
    initStickyComponent('.feed-widgets','sticky');
});

// eslint-disable-next-line no-unused-vars
var Feed = {

};
