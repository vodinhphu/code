define([
    'jquery',
    'CustomHomePage_CarouselWidget/js/carousel/owl.carousel.min',
    'CustomHomePage_CarouselWidget/js/carousel/slick.min',
], function ($) {
    'use strict';

    $('#owl-carousel-mainbanner').owlCarousel({
        loop: true,
        items: 1, 
        nav: true,
        autoplay:false,
        autoplayTimeout:5000,
        autoplayHoverPause:true,
        dots: false,
        navText : ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"]

    });
    // Fix lai style trong upsell
    $('.owl-carousel').owlCarousel({
        loop: true,
        items: 5, 
        nav: true,
        autoplay:false,
        autoplayTimeout:1000,
        autoplayHoverPause:true,
        dots: true,
        navText : ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
        itemsDesktop : [1199,3],
        itemsDesktopSmall : [900,3], // betweem 900px and 601px
        itemsTablet: [700,2], // 2 items between 600 and 480
        itemsMobile : [479,1] , // 1 item between 479 and 0
        responsiveClass:true,
        responsive:{
            0:{
                items:2,
                nav:true
            },
            600:{
                items:3,
                nav:false
            },
            1000:{
                items:5,
                nav:true,
                loop:false
            }
        }
    });
    //list vendor
    $('.customer-logos').slick({
        slidesToShow: 6,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2500,
        arrows: false,
        dots: false,
        pauseOnHover: false,
        responsive: [
            {
                breakpoint: 1024, // 800x600
                settings: { slidesToShow: 6 }
            },
            {
                breakpoint: 800, // 768x1024
                settings: { slidesToShow: 4 }
            },
            {
                breakpoint: 768, // 600x800
                settings: { slidesToShow: 3 }
            },
            {
                breakpoint: 600, // 480x320
                settings: { slidesToShow: 2 }
            },
            {
                breakpoint: 480, // 320x480
                settings: { slidesToShow: 2 }
            }
        ]
    });
});