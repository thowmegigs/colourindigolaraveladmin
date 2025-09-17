@props(['row'])
@if ($row->header_image == null)
    @if ($row->heading_location == 'Left')
        <div class="title-link-wrapper page-title">

            @if ($row->heading_location == 'Left')
                <h2 class="title title-link">{{ $row->section_title }}
                </h2>
                @if ($row->content_type == 'Collections' || $row->content_type == 'Products')
                    <a href="{{ $row->content_type == 'Collections' ? '/collection_product_list/' . $row->id : '/more_products/' . $row->id }}"
                        class="btn btn-dark btn-link btn-icon-right">More Products<i
                            class="w-icon-long-arrow-right"></i></a>
                @endif
            @else
                <h2 class="title title-center">{{ $row->section_title }}
                </h2>
                @if ($row->content_type == 'Collections' || $row->content_type == 'Products')
                    <a href="{{ $row->content_type == 'Collections' ? '/collection_product_list/' . $row->id : '/more_products/' . $row->id }}"
                        class="btn btn-dark btn-link btn-icon-right">More Products<i
                            class="w-icon-long-arrow-right"></i></a>
                @endif
            @endif

        </div>
    @else
        <div class="title-link-wrapper title-center">
            <h2 class="title title-link ">{{ $row->section_title }}
            </h2>

            {{-- <a href="#" class="btn btn-dark btn-link btn-icon-right">More Products<i class="w-icon-long-arrow-right"></i></a> --}}


        </div>

    @endif
@else
    <img src="{{ asset('storage/content_sections/' . $row->header_image) }}"
        style="object-fit:fill;margin-bottom:30px;width:100%;height:80px;" />
@endif
