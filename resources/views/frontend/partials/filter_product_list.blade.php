@if ($list->count() > 0)
  
   


        @foreach ($list as $p)
        <div class="col-6 col-md-4">
                <x-frontend.product :product="(object) $p" />
            </div>
        @endforeach
     
   
   

@endif
