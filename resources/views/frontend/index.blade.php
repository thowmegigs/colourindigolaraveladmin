@extends('layouts.frontend.app')
@section('content')

 @php
       
        $images_info = json_decode($slider->images_meta, true);
        
    @endphp


<div  x-data="{
        content_sections: [],
        html: '',
        page: 1,
        is_in_view: false,
        reached_end: false,
       
        init() {
            this.fetchContent();
            let me = this;
            $(window).scroll(function() {
    
                const elementToCheck = document.getElementById('end');
    
                if (isInView(elementToCheck)) {
    
                    if (!me.is_in_view && !me.reached_end) {
                        me.is_in_view = true;
                        me.page++;
                        me.fetchContent()
                    }
                }
            });
        },
        fetchContent() {
    $('#end').removeClass('d-none');
            this.loading = true;
            showLoader('end', 'Loading...')
    
            fetch('/?page=' + this.page, {
    
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
    
            }).then(response => response.json()).then(res => {
               // this.loading = false;
               // hideLoader('end')
                this.is_in_view = false;
               
                if (res['success']) {
                $('#end').addClass('d-none');
                    if (res['current_count'] > 0) {
                        $('#here').append(res['view']);
                       
                        
                           setTimeout(function(){
                            const swiper_slider = new Swiper('.swiper_slide', {
                          
                             slidesPerView: 1, autoplay: {
                               delay: 5000,
                             }, pagination: {
                                el: '.swiper-pagination',
                                clickable: true,
                              },
                             
                        });
                          const swiper = new Swiper('.swiper_cat', {
                                slidesPerView: 3,
                              breakpoints: {
                                    640: {
                                      slidesPerView: 3,
                                      spaceBetween: 10,
                                    },
                                    768: {
                                      slidesPerView: 6,
                                     
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
       
                        var objowlcarousel = $('.owl-carousel-category');
                        if (objowlcarousel.length > 0) {
                            objowlcarousel.owlCarousel({
                                items: 4,
                                lazyLoad: true,
                                pagination: false,
                                loop: true,
                                autoPlay: 2000,
                                navigation: true,
                                stopOnHover: true,
                                 responsiveRefreshRate: 200,
                                 navigationText: ['<i class=\'icofont icofont-thin-left\'></i>', '<i class=\'icofont icofont-thin-right\'></i>'],
                                   responsive: {
                            0: {
                                items: 2, // Show 2 items on mobile
                            },768: {
                                items: 4, // Show 1 item on larger screens
                            },
                            1200: {
                                items: 4, // Optionally, keep it at 1 for very large screens
                            }
                        
                        }
                            });
                            $('.owl-carousel').trigger('refresh.owl.carousel');
                        }
                        var onesC = $('#owl-carousel-real-category');
                        
                      },2000)

                       
                        this.page++;
                        this.fetchContent();
    
                    } else {
                        this.reached_end = true;
                    }
    
    
    
                } else {
                    vNotify.error({ text: res['message'], title: 'Error' });
                }
    
            })
        }
    
    
    }">
    <div x-html="html" id="here">

</div>
<div id="end" style="height:100vh"></div>
</div>
   

@endsection
