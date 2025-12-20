$('.payment-type .type').click(function () {
    $('.payment-type .type').removeClass('selected');
    $(this).addClass('selected');
    $('.shipping').hide(0.1);
    $('.' + $(this).attr('kind')).fadeIn();
});

$('.filter-btn').click(function(){
    $('.filter-sidebar').addClass('active');
});

$('.remove-class-link').click(function(){
    $('.filter-sidebar').removeClass('active');
});

$('.dropdown-input').on('click', function() {
    var $dropdown = $(this).closest('.dropdown');
    calculateSum($dropdown);
    $dropdown.find('.dropdown-content').toggle();
});
function calculateSum($dropdown) {
    let sum = 0;
    $dropdown.find('.dropdown-item-input').each(function() {
        let value = parseFloat($(this).val());
        if (!isNaN(value)) {
            sum += value;
        }
    });
    $dropdown.find('.dropdown-input').val(sum);
}
$('.dropdown-close-button').on('click', function(e) {
    e.stopPropagation();
    var $dropdown = $(this).closest('.dropdown');
    calculateSum($dropdown);
    $dropdown.find('.dropdown-content').hide();
});

$('#resetbtn').on('click', function (e) {
    let $el = $('input');
    $el.wrap('<form>').closest(
        'form').get(0).reset();
    $el.unwrap();
    $('input[type="checkbox"]').prop('checked', false);
    $('input[type="radio"]').prop('checked', false);
});


(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner();


    // Initiate the wowjs
    new WOW().init();


    // Sticky Navbar
    // $(window).scroll(function () {
    //     if ($(this).scrollTop() > 45) {
    //         $('.nav-bar').addClass('sticky-top');
    //         $('.nav-bar').removeClass('container');
    //     } else {
    //         $('.nav-bar').removeClass('sticky-top');
    //         $('.nav-bar').addClass('container');
    //     }
    // });


    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({ scrollTop: 0 }, 1500, 'easeInOutExpo');
        return false;
    });


    // Header carousel
    $(".header-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1500,
        items: 1,
        dots: true,
        loop: true,
        nav: true,
        navText: [
            '<i class="bi bi-arrow-left-circle-fill"></i>',
            '<i class="bi bi-arrow-right-circle-fill"></i>'
        ]
    });


    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        margin: 24,
        dots: false,
        loop: true,
        nav: true,
        navText: [
            '<i class="bi bi-chevron-left"></i>',
            '<i class="bi bi-chevron-right"></i>'
        ],
        responsive: {
            0: {
                items: 1
            },
            692: {
                items: 2
            },
            992: {
                items: 3
            }
        }
    });


    $('.like').click(function () {
        $(this).addClass('active');
        $(this).parent().find('.dislike').removeClass('active');
    });
    $('.dislike').click(function () {
        $(this).addClass('active');
        $(this).parent().find('.like').removeClass('active');
    });

    $('select').select2();

})(jQuery);

$(document).ready(function () {
    $('.increment-btn').on('click', function () {
        let value = parseInt($(this).parent().find('input').val(), 10);
        let max = parseInt($(this).parent().find('input').attr('max'), 10);
        if (value < max) {
            $(this).parent().find('input').val(value + 1);
        }
    });

    $('.decrement-btn').on('click', function () {
        let value = parseInt($(this).parent().find('input').val(), 10);
        let min = parseInt($(this).parent().find('input').attr('min'), 10);
        if (value > min) {
            $(this).parent().find('input').val(value - 1);
        }
    });
});


// slider price rang
$(".range-example-input").asRange({
    range: true,
    limit: false,
    onInit: function () {
        updateTooltip();
    },
    onChange: function () {
        updateTooltip();
    },
});

function updateTooltip() {
    var range = $('.range-example-input').asRange('val');

    var min = range[0];
    var max = range[1];

    var minTip = $('.asRange-pointer-1 .asRange-tip');
    var maxTip = $('.asRange-pointer-2 .asRange-tip');

    minTip.text(min + ' ' + $(".range-example-input").attr('currency') + ' ');
    maxTip.text(max + ' ' + $(".range-example-input").attr('currency') + ' ');
}
$('.asRange-tip').append('<span>' + ' ' + $(".range-example-input").attr('currency') + ' ' + '</span>');



$(document).ready(function () {
    $("input[name$='type']").click(function () {
        var show_div = $(this).attr('show');
        $(".radio-section").hide();
        $("." + show_div).fadeIn();
    });
    var show_div = $("input[name$='type']").attr('show');
    $(".radio-section").hide();
    $("." + show_div).fadeIn();

});

// $('.date').daterangepicker({
//     singleDatePicker: true,
//     locale: {
//         format: 'DD MMMM, YYYY'
//     },
//     minDate: moment().startOf('day'),
//     // maxDate:,
// });

// Change the type of input to date
$('.date').attr('type', 'date');

// Get today's date in YYYY-MM-DD format
const today = new Date().toISOString().split('T')[0];

// Set the min attribute to today's date
$('.date').attr('min', today);

$('.save').click(function () {
    $(this).toggleClass('active');
});


$(document).ready(function () {
    $('.see-more-btn').click(function () {
        $(this).parent().find('.hidden').removeClass('hidden');
        if ($(this).parent().find('.hidden').length === 0) {
            $('.see-more-btn').hide();
        }
    });
});

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});

if ($("#show_price").is(':checked')) {
    $('.price-section').fadeIn();
}
$("#show_price").change(function () {
    if (this.checked) {
        $('.price-section').fadeIn();
    } else {
        $('.price-section').fadeOut();
    }
});


function toggleSidebar(ref) {
    document.getElementById("sidebar").classList.toggle('active');
    $('.search-page').addClass('hide');
    $('#sidebar').fadeIn();
}

$('.close-sidebar').click(function(){
    document.getElementById("sidebar").classList.toggle('active');
    $('.search-page').removeClass('hide');
    $('#sidebar').fadeOut();
});


var headerHeight = $('.header').outerHeight();
var pageHeight = $('.search-page').outerHeight();
// $('#sidebar').css('margin-top', headerHeight);
// $('#sidebar').css('height', pageHeight);

$(".hotel-image-carousel").owlCarousel({
    autoplay: true,
    smartSpeed: 1500,
    items: 1,
    dots: true,
    loop: true,
    nav: true,
    navText: [
        '<i class="bi bi-chevron-left"></i>',
        '<i class="bi bi-chevron-right"></i>'
    ]
});

$(".hotels-carousel").owlCarousel({
    autoplay: true,
    smartSpeed: 1500,
    items: 1,
    dots: true,
    loop: false,
    nav: true,
    navText: [
        '<i class="bi bi-chevron-left"></i>',
        '<i class="bi bi-chevron-right"></i>'
    ]
});

var owl = $('.owl-hotels').owlCarousel({
    loop: true,
    margin: 10,
    center: true,
    nav: false,
    responsive: {
        0: {
            items: 1
        },
        600: {
            items: 3
        },
        1000: {
            items: 4
        }
    }
});
$('.owl-filter-bar').on('click', '.item', function () {
    var $item = $(this);
    var filter = $item.data('owl-filter');
    $('.owl-filter-bar').find('.item').removeClass('active');
    $item.addClass('active');
    owl.owlcarousel2_filter(filter);
});

$('.fav').click(function () {
    $(this).toggleClass('active');
});

$(function ($) {
    $.autofilter();
});

$('.nav-filter-rooms').find('span').click(function () {
    $('.nav-filter-rooms').find('span').removeClass('active');
    $(this).addClass('active');
});


const input = document.querySelector(".phone-input");
window.intlTelInput(input, {
    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.9.3/build/js/utils.js",
});


