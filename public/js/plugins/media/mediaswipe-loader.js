/*
	MediaSwipe Loader version 1.0.0
	Description: Simple loader aimed to create items lists and start MediaSwipe.
	License    : MIT license

	Copyright 2017 Joël VALLIER (joel.vallier@gmail.com)

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
	of the Software, and to permit persons to whom the Software is furnished to do so,
	subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
	INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
	PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
	HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
	OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
	SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
"use strict";
/* global PhotoSwipeUI_Default, MediaSwipe, appSettings */

// read debug level from URL
// eslint-disable-next-line no-unused-vars
var url = new URL(location.href);
var debug = 0;
// eslint-disable-next-line no-console
if (debug) console.log('Debug level '+debug);

// Scan page to load links
var mswpScanPage = function(selector,tag = 'mswp') {
    // create list and build DOM
    // eslint-disable-next-line no-console
    if (debug) console.log('Scan page using tag "'+tag+'"');
    var items = new Array();
    jQuery(function($) {
        // extract items
        $(selector).find(" a[rel*='"+tag+"']").each(function() {

            // eslint-disable-next-line no-useless-escape
            var exp = new RegExp('('+tag+'|\[|\])', 'g');
            var rel = $(this).attr('rel').replace(exp, '') || 0;

            if (!items[rel]) {
                // eslint-disable-next-line no-console
                if (debug) console.log('Create list '+rel);
                items[rel] = new Array();
            }
            let width = $(this).attr('width') || 0;
            let height = $(this).attr('height') || 0;

            // set item list
            var idx = items[rel].length;
            items[rel][idx] = { src: this.href, title: this.title, w: width, h: height, html:""};

            // bind click to PhotoSwipe
            $(this).unbind("click").on('click', function() {
                // log list of items
                // eslint-disable-next-line no-console
                if (debug) console.log('Items from list '+rel, items[rel]);

                // get element
                var element = document.querySelectorAll('.pswp')[0];

                // console.warn(items);
                // add support of videos

                let swiperSettings = {
                    index: idx,
                    bgOpacity: 0.8,
                    showHideOpacity: true,
                    shareEl: false,
                    fullscreenEl: false,
                    captionEl: false,
                    counterEl: true,
                    barsSize: {top:0, bottom:'auto'},
                    // scaleMode: 'fit',
                    maxSpreadZoom: 1,
                };

                if(appSettings.feed.allow_gallery_zoom === true){
                    swiperSettings.zoomEl = true;
                }
                else{
                    swiperSettings.zoomEl = false;
                    swiperSettings.getDoubleTapZoom = function (isMouseClick, item) {
                        return item.initialZoomLevel;
                    };
                }

                var mswp = new MediaSwipe(element, PhotoSwipeUI_Default, items[rel], swiperSettings);

                // start MediaSwipe
                mswp.init();

                // prevent event propagation
                return false;
            });
        });

    });
};

mswpScanPage('.a-swiper','mswp');

