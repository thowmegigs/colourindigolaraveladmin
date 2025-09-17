$(document).ready(function() {
    "use strict";

     document.querySelectorAll('.navbar-custom .nav-item').forEach(item => {
            if (item.getAttribute('href') === window.location.pathname.split('/').pop()) {
                item.classList.add('active');
            }
        });
     const swiper = new Swiper('.swiper_cat', {
                                slidesPerView: 3,
                              breakpoints: {
                                    640: {
                                      slidesPerView: 3,
                                      spaceBetween: 10,
                                    },
                                    768: {
                                      slidesPerView: 5,
                                     
                                    },
                                    1024: {
                                      slidesPerView: 10,
                                     
                                    },
                                  },
                            
                              pagination: {
                                el: '.swiper-pagination',
                                clickable: true,
                              },
                        });
     $('#menu-toggle1').click(function() {
        
            $('#offcanvasMenu1').toggleClass('open');
        });
         $('#menu-toggle2').click(function() {
        
            $('#offcanvasMenu1').toggleClass('open');
        });
        
        // Close the offcanvas menu
        $('#menu-close').click(function() {
            $('#offcanvasMenu1').removeClass('open');
        });
    
    var objowlcarousel = $(".owl-carousel-category");
    if (objowlcarousel.length > 0) {
        objowlcarousel.owlCarousel({
            items: 4,
            lazyLoad: true,
            pagination: false,
            loop: true,
            autoPlay: 2000,
            navigation: true,
            stopOnHover: true,
            navigationText: ["<i class='icofont icofont-thin-left'></i>", "<i class='icofont icofont-thin-right'></i>"]
        });
    }
    $('[data-toggle="offcanvas"]').on('click', function() {
        $('body').toggleClass('toggled');
    });
    $('.navbar-nav li.dropdown').on('mouseenter', function() {
        $(this).find('.dropdown-menu').stop(true, true).delay(100).fadeIn(500);
    })
    $('.navbar-nav li.dropdown').on('mouseleave', function() {
        $(this).find('.dropdown-menu').stop(true, true).delay(100).fadeOut(500);
    })
    //$('select').select2();
    $('[data-toggle="tooltip"]').tooltip();
    var sync1 = $("#sync1");
    var sync2 = $("#sync2");
    sync1.owlCarousel({
        singleItem: true,
        items: 1,
        slideSpeed: 1000,
        pagination: false,
        navigation: true,
        autoPlay: 2500,
        dots: false,
        nav: true,
        navigationText: ["<i class='icofont icofont-thin-left'></i>", "<i class='icofont icofont-thin-right'></i>"],
        afterAction: syncPosition,
        responsiveRefreshRate: 200,
    });
    sync2.owlCarousel({
        items: 5,
        navigation: true,
        dots: false,
        pagination: false,
        nav: true,
        navigationText: ["<i class='icofont icofont-thin-left'></i>", "<i class='icofont icofont-thin-right'></i>"],
        responsiveRefreshRate: 100,
        afterInit: function(el) {
            el.find(".owl-item").eq(0).addClass("synced");
        }
    });

    function syncPosition(el) {
        var current = this.currentItem;
        $("#sync2").find(".owl-item").removeClass("synced").eq(current).addClass("synced")
        if ($("#sync2").data("owlCarousel") !== undefined) {
            center(current)
        }
    }
    $("#sync2").on("click", ".owl-item", function(e) {
        e.preventDefault();
        var number = $(this).data("owlItem");
        sync1.trigger("owl.goTo", number);
    });

    function center(number) {
        var sync2visible = sync2.data("owlCarousel").owl.visibleItems;
        var num = number;
        var found = false;
        for (var i in sync2visible) {
            if (num === sync2visible[i]) {
                var found = true;
            }
        }
        if (found === false) {
            if (num > sync2visible[sync2visible.length - 1]) {
                sync2.trigger("owl.goTo", num - sync2visible.length + 2)
            } else {
                if (num - 1 === -1) {
                    num = 0;
                }
                sync2.trigger("owl.goTo", num);
            }
        } else if (num === sync2visible[sync2visible.length - 1]) {
            sync2.trigger("owl.goTo", sync2visible[1])
        } else if (num === sync2visible[0]) {
            sync2.trigger("owl.goTo", num - 1)
        }
    }
    //$('.datatabel').DataTable();
});