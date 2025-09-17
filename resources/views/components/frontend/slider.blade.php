@props(['row'])
    @php
        $slider_info = $row->slider;
        $images_info = json_decode($slider_info->images_meta, true);
        
    @endphp
<style>
    .swiper-pagination-bullet{
            height: 20px;
    width: 20px;
    border: 5px solid #f4eaea;
    }
     
</style>
    <div class="bg-light">
       

        <div class="p-2 btn-primary">
             <div class="swiper swiper_slide" >
  <!-- Additional required wrapper -->
  <div class="swiper-wrapper">
        @foreach($images_info as $t)

                                    <div class="swiper-slide text-center" >
                                        <img class="img-fluid mx-auto rounded shadow-sm" 
                                            src="{{ asset('storage/sliders/' . $t['file']) }}">
                                    </div>



                                @endforeach
   
  </div>
 
  <div class="swiper-pagination"></div>

 
</div>
            
        </div>
    </div>
