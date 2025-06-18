<div class="card mb-4">

    <div class="card-header py-1">
        <label class="form-label">Features</label>
    </div>
    <div class="card-body">
        @foreach ($features as $g)
            <div class="row">

                <div class="col-md-6">
                    <label class="form-label">{{ $g }}</label>

                </div>


                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <input class="form-control" placeholder="Enter {{strtolower($g)}}"
                            name="product_features__{{ strtolower(str_replace(' ', '_', $g)) }}" />

                    </div>




                </div>

            </div>
        @endforeach
        <!-- end card body -->
    </div>
</div>
