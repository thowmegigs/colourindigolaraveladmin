@props(['offers'])
<div class="modal fade" id="applied_offers_modal" tabindex="-1" aria-labelledby="quickViewModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Applied Offers</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @foreach($offers as $t)
                <div class="d-flex flex-column" >
                    <h5 class="text-danger">{{ $t['name'] }}</h5>
                    <p>{!! $t['details']!!}</p>
                </div>
                @endforeach

            </div>
        </div>
    </div>
</div>
<div class="offcanvas offcanvas-bottom" id="applied_offers_offcanvas">
    <div class="offcanvas-header">
        <h1 class="offcanvas-title">Available Offers</h1>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
    @foreach($offers as $t)
                <div class="d-flex flex-column" >
                    <h4>{{ $t['name'] }}</h4>
                    <p>{{$t['details']}}</p>
                </div>
                @endforeach
    </div>
</div>
