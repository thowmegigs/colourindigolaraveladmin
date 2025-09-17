@foreach ($content_sections as $t)
@if ($t->content_type == 'Slider')
    <x-frontend.slider :row="$t" />
  
@elseif ($t->content_type == 'Banner')
    <x-frontend.banner :row="$t" />
@elseif($t->content_type == 'Categories')
    <x-frontend.categories :row="$t" />
@elseif($t->content_type == 'Collections')
    <x-frontend.collection :row="$t" />
@elseif($t->content_type == 'Products')
    @if ($t->display == 'Horizontal')
        <x-frontend.product_slider :row="$t" />
    @else
        <x-frontend.products_grid :row="$t" />
    @endif
@endif
@endforeach
