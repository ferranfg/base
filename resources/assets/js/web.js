window.feather = {
    replace: function () {
        //
    }
};

// Inits owlCarousel
if (typeof $.fn.owlCarousel == 'function') require('./../../template/js/owl.init.js');

// Inits Lazyload
if (typeof LazyLoad == 'function') new LazyLoad({
    elements_selector: ".lazy"
});

require("./../../template/js/app");

// Main functions
$("#ajax-modal").on("shown.bs.modal", function(e) {
    $(this).load($(e.relatedTarget).attr("href"));
});

$(document).on({
    mouseover: function(event) {
        $(this).find('.far').addClass('star-over');
        $(this).prevAll().find('.far').addClass('star-over');
    },
    mouseleave: function(event) {
        $(this).find('.far').removeClass('star-over');
        $(this).prevAll().find('.far').removeClass('star-over');
    }
}, '.rate');


$(document).on('click', '.rate', function() {
    if ( !$(this).find('.star').hasClass('rate-active') ) {
        $(this).siblings().find('.star').addClass('far').removeClass('fas rate-active');
        $(this).find('.star').addClass('rate-active fas').removeClass('far star-over');
        $(this).prevAll().find('.star').addClass('fas').removeClass('far star-over');
    } else {
        console.log('has');
    }
});