/**
 * Side menu (slidable) component
 */
"use strict";
/* global WOW */

jQuery(document).ready(function() {
    // Sidebar
    $('.dismiss, .overlay').on('click', function() {
        $('.sidebar').removeClass('active');
        $('.overlay').removeClass('active');
    });

    // Menu toggled
    $('.open-menu').on('click', function(e) {
        e.preventDefault();
        $('.sidebar').addClass('active');
        $('.overlay').addClass('active');
        // close opened sub-menus
        $('.collapse.show').toggleClass('show');
        $('a[aria-expanded=true]').attr('aria-expanded', 'false');
    });
    /* replace the default browser scrollbar in the sidebar, in case the sidebar menu has a height that is bigger than the viewport */
    $('.sidebar').mCustomScrollbar({
        theme: "minimal-dark"
    });

    // Wow initiate
    new WOW().init();
});
