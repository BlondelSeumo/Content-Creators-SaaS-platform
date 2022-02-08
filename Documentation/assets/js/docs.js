$( document ).ready(function() {

  // Binding code highlighter
  hljs.initHighlightingOnLoad();
  // Sticky bar
  bindDesktopStickyBar();
  $( window ).resize(function() {
    bindDesktopStickyBar();
  });

  // Initializing product info
  $('.versionLabel').html(productConfig.version)
    $('.appNameLabel').html(productConfig.name)
  document.title = productConfig.name + ' Documentation'

// Smooth Scrolling
$('a[href*="#"]')
  // Remove links that don't actually link to anything
  .not('[href="#"]')
  .not('[href="#0"]')
  .not('[data-type="#submenu"]')
  .not('.submenu')
  .click(function(event) {
    // On-page links
    if (
      location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') 
      && 
      location.hostname == this.hostname
    ) {
      // Figure out element to scroll to
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
      // window.location.hash = this.hash.slice(1);
      // Does a scroll target exist?
      if (target.length) {
        // Only prevent default if animation is actually gonna happen
        event.preventDefault();
        $('html, body').animate({
          scrollTop: target.offset().top
        }, 300, function() {
          // Callback after animation
          // Must change focus!
          var $target = $(target);
          $target.focus();
          if ($target.is(":focus")) { // Checking if the target was focused
            return false;
          } else {
            $target.attr('tabindex','-1'); // Adding tabindex for elements not focusable
            $target.focus(); // Set focus again
          };
        });
      }
    }
  });
});

function bindDesktopStickyBar() {
  let isMobile = window.matchMedia("only screen and (max-width: 993px)").matches;
  if (!isMobile) {
    $("#sticker").sticky({topSpacing:0});
  }
  else{
    $("#sticker").unstick();
  }
}