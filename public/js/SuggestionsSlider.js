/**
 * Class for handling feed members suggestions slider
 */
"use strict";
/* global  app, launchToast, trans, Swiper, sliderConfig*/

$(function () {

});

var SuggestionsSlider = {

    /**
     * Instantiates the suggested members slider
     * @returns {*}
     */
    init: function () {
        let swiperConfig ={
            pagination: {
                el: ".suggestions-box .swiper-pagination",
                // type: "fraction",
                dynamicBullets: true,
            },
            navigation: {
                nextEl: ".suggestions-next-slide",
                prevEl: ".suggestions-prev-slide",
            }
        };
        if(sliderConfig.autoslide === true){
            swiperConfig.autoplay = {delay: 10000};
        }
        return new Swiper(".suggestions-box .mySwiper", swiperConfig);
    },

    /**
     * Loads suggestions, based on filters
     * @param filters
     */
    loadSuggestions: function (filters = {}) {
        $.ajax({
            type: 'POST',
            data: {filters},
            dataType: 'json',
            url: app.baseUrl+'/suggestions/members',
            success: function (result) {
                if(result.success){
                    // launchToast('success',trans('Success'),'Setting saved');
                    SuggestionsSlider.appendSuggestionsResults(result.data);
                    launchToast('success',trans('Success'),trans('Suggestions list refreshed'));
                }
                else{
                    launchToast('danger',trans('Error'),trans('Error fetching suggestions'));
                }
            },
            error: function () {
                launchToast('danger',trans('Error'),trans('Error fetching suggestions'));
            }
        });
    },

    /**
     * Appends new suggestions to the widget
     * @param posts
     */
    appendSuggestionsResults: function(posts){
        $('.suggestions-content').html('');
        $('.suggestions-content').append(posts.html).fadeIn('slow');
        SuggestionsSlider.init();
    },

};

