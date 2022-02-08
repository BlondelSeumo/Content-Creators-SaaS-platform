/*
	MediaSwipe version 1.0.0
	Description: Extension of PhotoSwipe allowing support of other medias like videos.
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
/* global debug, YT, PhotoSwipe */

// MediaSwipe extension
// eslint-disable-next-line no-unused-vars
function MediaSwipe(element, options, items, params) {
    // debug
    var log = function(img, str, param) {
        if (debug) {
            // eslint-disable-next-line no-console
            if (param) console.log(img.id+' '+str, param);
            // eslint-disable-next-line no-console
            else console.log(img.id+' '+str);
        }
    };

    var createElement = function(item, type, prefix, classes, params) {
        // create element and set id
        var img = pswp.framework.createEl(classes, type);
        img.id = prefix+'-'+(1+pswp.items.indexOf(item));

        // set params
        if (params) {
            for (var prop in params) {
                img[prop] = params[prop];
            }
        }

        // return element
        return img;
    };

    // New createImage
    var createImage = function(item, classes, onload, onerror) {
        // create element
        var img = createElement(item, 'img', 'image', classes);

        // get image size after loading
        if ((item.w < 1) || (item.h < 1)) {
            var photo = null;
            photo = new Image();
            photo.onload = function() {
                // set image width/height
                item.w = photo.width;
                item.h = photo.height;
                log(img, 'Dynamic size '+item.w+'x'+item.h);

                // reinitialize current item
                pswp.updateSize();
            };
            photo.onerror = function() {
                log(img, 'Load error');
            };
            photo.src = item.src;
        } else {
            log(img, 'Size '+item.w+'x'+item.h);
        }

        // set callbacks
        if (onload) img.onload = onload;
        if (onerror) img.onerror = onerror;

        // start image loading
        img.src = item.src;

        // export image
        this.img = img;
    };

    // Suppot of YouTube videos
    var createYouTubeVideo = function(item, classes, onload, onerror) {
        // YouTube API Reference:
        // https://developers.google.com/youtube/iframe_api_reference?hl=en

        // create element
        var img = createElement(item, 'iframe', 'youtube', classes, {type: 'text/html', frameborder: 0, height: item.h, width: item.w});

        // clear player and set local callbacks
        var player = null;
        img.onload = function() {
            // create player
            // eslint-disable-next-line no-unused-vars
            var onPlayerReady = function(event) {
                log(img, 'Ready');
                if ((pswp.currItem.content) && (item === pswp.currItem)) pswp.currItem.content.start();
            };
            var onPlayerStateChange = function(event) {
                var label = "Unknown state";
                switch (event.data) {
                case -1 :
                    label = "Not started";
                    break;
                case 0 :
                    label = "Stopped";
                    break;
                case 1 :
                    label = "Reading";
                    break;
                case 2 :
                    label = "Paused";
                    break;
                case 3 :
                    label = "Loaded";
                    break;
                case 5 :
                    label = "In the queue";
                    break;
                }
                log(img, 'State Change ('+label+')');
            };
            // eslint-disable-next-line no-unused-vars
            var onPlayerError = function(event) {
                log(img, 'Error');
                if (onerror) onerror();
            };
            // create player
            player = new YT.Player(img.id, {
                events: {
                    'onReady': onPlayerReady,
                    'onStateChange': onPlayerStateChange,
                    'onError': onPlayerError
                }
            });
            // callback
            if (onload) onload();
        };

        // define start and stop methods
        this.start = function() {
            if ((player) && (player.playVideo)) {
                try {
                    player.playVideo();
                } catch (exception) {
                    // in some case ifram has been removed and so player no more attached
                    //log(img, 'Exception:', exception);
                }
            }
        };
        this.stop = function() {
            if ((player) && (player.pauseVideo)) {
                try {
                    player.pauseVideo();
                } catch (exception) {
                    // in some case ifram has been removed and so player no more attached
                    //log(img, 'Exception:', exception);
                }
            }
        };

        // add https protocol if not there
        // eslint-disable-next-line no-useless-escape
        if (/^(http|https)\:\/\//i.test(item.src) === false) {
            item.src = 'https://'+item.src;
        }

        // start video loading
        img.src = item.src+'?enablejsapi=1&origin='+window.location.origin+'&autohide=1&controls=1&modestbranding=1';

        // export image
        this.img = img;
    };

    // Support of standard videos
    var createVideo = function(item, classes, onload, onerror) {
        // HTML5 API Reference:
        // https://www.w3schools.com/tags/ref_av_dom.asp
        classes += ' pswp_video';
        // create element
        var img = createElement(item, 'video', 'video', classes, {preload: 'auto', controls: 1});

        // clear player and set local callbacks
        img.oncanplay = function() {
            // log event
            log(img, 'Player ready');

            // get image size after loading
            if ((item.w < 1) || (item.h < 1)) {
                // set image width/height
                item.w = img.videoWidth;
                item.h = img.videoHeight;
                log(img, 'Dynamic size '+item.w+'x'+item.h);

                // reinitialize current item
                pswp.updateSize();
            } else {
                // log size
                log(img, 'Size '+item.w+'x'+item.h);
            }

            // workaround (delay onload else video doesn't start)
            if (onload) setTimeout(function() { onload(); }, 500);
        };
        img.onerror = function() {
            log(img, 'Error');
            if (onerror) onerror();
        };

        // define start and stop methods
        this.start = function() { img.play(); };
        this.stop = function() { img.pause(); };

        // start video loading
        img.src = item.src;
        this.img = img;

    };

    var createAudio = function(item, classes, onload, onerror) {
        // HTML5 API Reference:
        // https://www.w3schools.com/tags/ref_av_dom.asp
        classes += ' pswp_audio';

        // create element
        var img = createElement(item, 'audio', 'audio', classes, {preload: 'auto', controls: 1});

        // clear player and set local callbacks
        img.oncanplay = function() {
            // log event
            log(img, 'Player ready');

            // get image size after loading
            // if ((item.w < 1) || (item.h < 1)) {
            // set image width/height
            item.w = '200';
            item.h = '50';
            log(img, 'Dynamic size '+item.w+'x'+item.h);

            // reinitialize current item
            pswp.updateSize();
            // } else {
            // log size
            log(img, 'Size '+item.w+'x'+item.h);
            // }

            // workaround (delay onload else video doesn't start)
            if (onload) setTimeout(function() { onload(); }, 500);
        };
        img.onerror = function() {
            log(img, 'Error');
            if (onerror) onerror();
        };

        // define start and stop methods
        this.start = function() { img.play(); };
        this.stop = function() { img.pause(); };

        // start video loading
        img.src = item.src;

        // export image
        this.img = img;
    };

    // ececute a function on items at current index +/- pos
    var execute = function(pos, fct) {
        if (pswp.items.length > (2*(pos-1)+1)) {
            var id = pswp.items.indexOf(pswp.currItem);
            var idx = [((id+pos) % pswp.items.length), ((id+pswp.items.length-pos) % pswp.items.length)];
            idx.forEach(function(id) {
                if (pswp.items[id].content) fct(id);
            });
        }
    };

    // stop, start and clear methods
    var start = function(item) {
        if (item.content) {
            log(item.content.img, 'Start');
            item.content.start();
        }
    };

    var stop = function(item) {
        if (item.content) {
            log(item.content.img, 'Stop');
            item.content.stop();
        }
    };

    var clear = function(item) {
        if (item.content) {
            log(item.content.img, 'Cleared');
            item.content = null;
        }
    };

    // generic create media
    var _createMedia = function(item, classes, onload, onerror) {
        var media = item.content || null;
        if (media) {
            // return created media
            log(media.img, 'Returned');
            // set callback
            if (onload) setTimeout(function() { onload(); }, 500);
        } else {
            if (/\.(mp4|ogg|webm)$/i.test(item.src)) {
                // YouTube video
                media = new createVideo(item, classes, onload, onerror);
            }
            else if(/\.(mp3|wav)$/i.test(item.src)){
                media = new createAudio(item, classes, onload, onerror);
            }
            else{
                if (/www\.youtube\.com\/embed\//i.test(item.src)) {
                    // Standard video
                    media = new createYouTubeVideo(item, classes, onload, onerror);
                } else {
                    // Standard image
                    media = new createImage(item, classes, onload, onerror);
                }
            }


            // set default start and stop methods
            if (!media.start) media.start = function(){};
            if (!media.stop) media.stop = function(){};

            // log creation and save media
            log(media.img, 'Created'+(onload ? ' with callback' : ' without callback'));

            // Alternative way of wrapping up video/audio elements > Breaks autoplay and swiper behaviour
            // if(/\.(mp3|wav)$/i.test(item.src) || /\.(mp4|ogg|webm)$/i.test(item.src)){
            //     var cln = media.img.cloneNode(true);
            //     var wrapper = document.createElement('div')
            //     wrapper.setAttribute('class','h-100 w-100 d-flex justify-content-center align-items-center video-wrapper')
            //     wrapper.appendChild(cln)
            // }
            // media.img = wrapper;
            item.content = media;
        }

        // return media
        return media.img;
    };

    // listeners
    this.listen = function(evt, fct) {
        return pswp.listen(evt, fct);
    };

    // MediaSwipe initialisation
    this.init = function() {
        return pswp.init();
    };

    // log start
    // eslint-disable-next-line no-console
    if (debug) console.log('MediaSwipe Starts');

    // create instance
    var pswp = new PhotoSwipe(element, options, items, params);

    // redefine media creation (non standard API)
    pswp.setCustomMedia(_createMedia);

    // connect listeners
    pswp.listen('close', function() {
        // stop current media
        stop(pswp.currItem);
        clear(pswp.currItem);
        execute(1, function(id) {
            clear(pswp.items[id]);
        });
        // release PhotoSwipe
        pswp = null;
        // log
        // eslint-disable-next-line no-console
        if (debug) console.log('MediaSwipe Closed');
    });
    pswp.listen('afterChange', function(param) {
        // stop and clear uneeded videos
        execute(2, function(id) {
            clear(pswp.items[id]);
        });
        execute(1, function(id) {
            stop(pswp.items[id]);
        });
        // switch media
        if (!param) {
            // eslint-disable-next-line no-unused-vars
            let a = pswp.currItem.container.childNodes;
            start(pswp.currItem);
        }
        // Alternative way of wrapping up video/audio holders
        // $('.pswp_video, .pswp_audio').wrap('<div class="h-100 w-100 d-flex justify-content-center align-items-center"></div>');
    });

}
