@extends('layouts.frontend.app')
@section('content')
    <main class="main mb-10 pb-1">
        <!-- Start of Breadcrumb -->
        <nav class="breadcrumb-nav container">
            <ul class="breadcrumb bb-no">
                <li><a href="/">Home</a></li>
                <li>Categories</li>
            </ul>

        </nav>
        <!-- End of Breadcrumb -->

        <!-- Start of Page Content -->
        <div class="page-content mb-3">

            <div class="container">
                <div class="row">

                    @foreach ($data as $id=>$g)
                        @php
                            $url =isset($g['subs']) ? '/get_categories/' .$g['title'] : '/products/' . $g['title'];
                        @endphp
                        <div class="col-6 col-md-2 mb-3">
                            <div class=" category category-ellipse" role="group" aria-label="1 / 8"
                                style="width: 147.375px; margin-right: 40px;">
                                <figure class="category-media">
                                    <a href="{{ $url }}">
                                        <img alt="" class="shimmer-background1"
                                            src="/category_image/{{ $g['image'] }}?width=200&height=200"
                                            srcset=" /category_image/{{ $g['image'] }}?width=50&height=50 280w,
                                                    /category_image/{{ $g['image'] }}?width=200&height=200 480w,
                                                   /category_image/{{ $g['image'] }}?width=300&height=300 860w"
                                            sizes="(max-width: 700px) 480px,860px"
                                            style="width:150px;min-height:100px;height:auto;" />
                                    </a>
                                </figure>
                                <div class="category-content">
                                    <h5 class="category-name">
                                        <a href="{{ $url }}" style="font-size:12px">{{$g['title']}}</a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    @endforeach


                </div>

                <!-- End of Main Content -->

                <!-- End of Sidebar -->
            </div>
        </div>
        </div>
        <!-- End of Page Content -->
    </main>
@endsection
