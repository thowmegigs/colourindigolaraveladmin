@extends('layouts.frontend.app')
@section('content')
    <style>
        .cl {
            border: 1px solid #99bae8;
            padding: 18px 17px;
            background: #dcebff;
            margin: 2px;
        }
    </style>
    <main class="main" x-data="{
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
                this.loading = false;
                hideLoader('end')
                this.is_in_view = false;
                if (res['success']) {
                    if (res['current_count'] > 0) {
                        $('#here').append(res['view']);
                        setTimeout(function() {
                            jQuery('.btn-quickview1').magnificPopup({
                                midClick: true,
                                mainClass: 'mfp-fade'
                            });
                            let s1 = new Swiper('.swiper-slider-container', {
                                'slidesPerView': 1,
                                'loop': true,
                                'effect': 'fade',
                                'autoplay': {
                                    'delay': 3000,
                                    'disableOnInteraction': false
                                }
                            });
                            let s2 = new Swiper('.swiper-products-container', {
                                'spaceBetween': 40,
                                'breakpoints': {
                                    '0': {
                                        'slidesPerView': 2
                                    },
                                    '576': {
                                        'slidesPerView': 3
                                    },
                                    '768': {
                                        'slidesPerView': 4
                                    },
                                    '992': {
                                        'slidesPerView': 6
                                    },
                                    '1200': {
                                        'slidesPerView': 8
                                    }
                                }
                            });
                            let s5 = new Swiper('.swiper-slider-bottom', {
                                'slidesPerView': 1,
                                'loop': true,
                                'breakpoints': {
                                    '576': {
                                        'slidesPerView': 2
                                    },
                                    '768': {
                                        'slidesPerView': 3
                                    },
                                    '1200': {
                                        'slidesPerView': 4
                                    }
                                }
                            });
                            let s3 = new Swiper('.swiper-category-container', {
                                'spaceBetween': 40,
                                'breakpoints': {
                                    '0': {
                                        'slidesPerView': 2
                                    },
                                    '576': {
                                        'slidesPerView': 3
                                    },
                                    '768': {
                                        'slidesPerView': 4
                                    },
                                    '992': {
                                        'slidesPerView': 6
                                    },
                                    '1200': {
                                        'slidesPerView': 8
                                    }
                                }
                            });
                            let s6 = new Swiper('.swiper-slider-bottom', {
                            'loop':true,
                                         'autoplay': {
                                    'delay': 3000,
                                    'disableOnInteraction': false
                                },
                                
                                'breakpoints': {
                                    '0': {
                                        'slidesPerView': 1,
                                        
                               
                              
                                    },
                                    '576': {
                                        'slidesPerView': 2, 
                                       
                                       
                               
                                    },
                                    '992': {
                                        'slidesPerView': 3,
                                        'loop':false,
                                         'autoplay':{'enabled':false},
                                        
                                    },
                                    '1200': {
                                        'slidesPerView': 4,
                                          'loop':false,
                                         'autoplay':{'enabled':false},
                                       
                                    }
                                }
                            })
    
                        }, 3000);
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
        <div id="end" style="height:100px"></div>


    </main>
@endsection
