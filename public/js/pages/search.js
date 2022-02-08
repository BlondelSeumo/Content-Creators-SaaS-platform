/*
* Feed page & component
 */
"use strict";
/* global  SuggestionsSlider, Post, initStickyComponent, paginatorConfig, PostsPaginator, redirect, app, searchType,initialPostIDs, getCookie, UsersPaginator  */

$(function () {

    if(searchType === 'feed'){
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
        Post.setActivePage('search');
        if(getCookie('app_prev_post') !== null){
            PostsPaginator.scrollToLastPost(getCookie('app_prev_post'));
        }
        Post.initPostsMediaModule();
    }

    if(searchType === 'people') {
        if(typeof paginatorConfig !== 'undefined'){
            if((paginatorConfig.total > 0 && paginatorConfig.total > paginatorConfig.per_page) && paginatorConfig.hasMore) {
                UsersPaginator.initScrollLoad();
            }
            UsersPaginator.init(paginatorConfig.next_page_url, '.users-wrapper');
        }
        else{
            // eslint-disable-next-line no-console
            console.error('Pagination failed to initialize.');
        }
    }
    SuggestionsSlider.init();
});

$(window).scroll(function () {
    initStickyComponent('.search-widgets','sticky');
});

// eslint-disable-next-line no-unused-vars
var Search = {

    goBack: function () {
        redirect(app.baseUrl+'/feed');
    },

};
