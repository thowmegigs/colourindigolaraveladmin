@props(['row'])
    @php
        // dd($row->banner->toArray());
        $n = $row->banner->banner_images->count();
        $collection_name = $row->banner->collection != null ? '/collection/' .
        \Str::slug($row->banner->collection->name) : '#';
    @endphp
    <section class="product-list pt-5 bg-light">
        <div class="container">
            <div class="row">


                @foreach($row->banner->banner_images as $img)
                    <div class="col-{{ 12/$n }}">
                        <div class="offers-block">
                            <a href="#"><img class="img-fluid"
                                    src="{{ asset('storage/banners/' . $img->name) }}"
                                    alt></a>
                        </div>
                    </div>

                @endforeach
            </div>
