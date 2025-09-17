@extends('layouts.admin.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y pt-5">
       

        <div class="row">
            <!-- Basic Layout -->
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">View {{ properSingularName($plural_lowercase) }}</h5>
                    </div>

                    <div class="card-body">
                          <x-displayViewData :module="$module" :row1="$row" :modelRelations="$model_relations" :viewColumns="$view_columns"
                            :imageFieldNames="$image_field_names" :storageFolder="$storage_folder" />
                      <b class="ms-2 text-info">Variants Detail</b>
                      @php 
                    $fol="products/".$row->id."/variants";
                    @endphp
                      @forelse($row->variants ?? [] as $index => $variant)
    <div class="card mb-3">
        <div class="card-header ">
          <b>( {{ $index + 1 }} )</b><span class="text-bold text-danger"> {{ $variant->name ?? 'Unnamed' }}</span>
        </div>
        <div class="card-body p-3">
            <div class="row mb-2">
                <div class="col-sm-4 fw-bold">Price</div>
                <div class="col-sm-8">{{ $variant->price }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-4 fw-bold">Sale Price</div>
                <div class="col-sm-8">{{ $variant->sale_price }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-4 fw-bold">Quantity</div>
                <div class="col-sm-8">{{ $variant->quantity }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-4 fw-bold">Max Quantity Allowed</div>
                <div class="col-sm-8">{{ $variant->max_quantity_allowed }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-4 fw-bold">Primary Image</div>
                <div class="col-sm-8">
                    @if(!empty($variant->image))
                    <x-singleFile :fileName="$variant->image" modelName="ProductVariant" 
                    :folderName="$fol" fieldName="image"  :rowid="$variant->id" />
                    @else
                        N/A
                    @endif
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-sm-4 fw-bold">Additional Images</div>
                <div class="col-sm-8">
               
                    <!-- @foreach($variant->images ?? [] as $index =>$image)
                   
                    {{$image->name}},
                    

                    @endforeach -->
                     <x-showImages inline="true" :row="$variant" fieldName='images' 
                    :storageFolder="$fol"
                    tableName="product_variants" />
                </div>
            </div>
        </div>
    </div>
@empty
    <p>No variants available.</p>
@endforelse

                        

                    </div><br>
                </div>
            </div>
        </div>
    </div>
@endsection
