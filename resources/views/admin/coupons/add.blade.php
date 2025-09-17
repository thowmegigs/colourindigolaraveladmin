@extends('layouts.admin.app')
@section('content')
    <style>
        label {
            display: block;
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-2">
            <div class="card-header d-flex align-items-center">
                <button type="button" class="btn btn-icon btn-outline-primary">
                    <span class="tf-icons bx bx-layer-plus"></span>
                </button> &nbsp;&nbsp;<h5 class="rounded-1 mb-0 text-primary">Add {{ properSingularName($plural_lowercase) }}
                </h5>
            </div>
        </div>
        <!--modalable content-->
        {!! Form::open()->route(route_prefix().$plural_lowercase . '.store')->id(strtolower($module) . '_form')->multipart()->attrs(['data-module' => $module]) !!}
        @if ($has_image && count($image_field_names) > 0)
            <div class="row">
                <div class="col-md-8">
                    <div class="card">

                        <div class="card-body">
                            <div class="card-text">
                                <x-forms :data="$data" column='2' />
                                @if (count($repeating_group_inputs) > 0)
                                    @foreach ($repeating_group_inputs as $grp)
                                        <x-repeatable :data="$grp['inputs']" :label="$grp['label']" values="" :index="$loop->index"
                                            :hide="$grp['hide']" :indexWithModal="$grp['index_with_modal']" :modalInputBoxIdWhoseValueToSetInSelect="$grp['modalInputBoxIdWhoseValueToSetInSelect']" />
                                    @endforeach
                                @endif

                            </div>
                        </div>
                    </div>



                </div>
                <div class="col-md-4 mt-xs-2">
                    <div class="card">

                        <div class="card-body">

                            <h5 class="px-0 py-1 rounded-1 mb-0 text-primary">Upload Documents</h5>
                            <br>

                            <div class="card-text">
                                <x-imageform :data="$data" column='1' />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card">

                <div class="card-body">
                    <div class="card-text">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <p style="font-weight:450;font-size: 14px;">Coupon Discount Type</p>
                                <div class="align-items-center">
                                    <div class="form-check form-check-inline"><input onchange="toggleDiscountRuleDiv(this.value)" type="radio"
                                            name="type" id="inp-type-Bulk" value="Bulk" checked="" class="form-check-input"><label
                                            for="inp-type-Bulk" class="form-check-label">Normal Discount</label></div>
                                    <div class="form-check form-check-inline"><input onchange="toggleDiscountRuleDiv(this.value)" type="radio"
                                            name="type" id="inp-type-Individual Quantity" value="Individual Quantity"
                                            class="form-check-input"><label for="inp-type-Individual Quantity" class="form-check-label">Quantity
                                            Based</label></div>
                                    <div class="form-check form-check-inline"><input onchange="toggleDiscountRuleDiv(this.value)" type="radio"
                                            name="type" id="inp-type-Cart" value="Cart" class="form-check-input"><label for="inp-type-Cart"
                                            class="form-check-label">Cart Amount Based</label></div>
                                    <div class="form-check form-check-inline"><input onchange="toggleDiscountRuleDiv(this.value)" type="radio"
                                            name="type" id="inp-type-BOGO" value="BOGO" class="form-check-input"><label for="inp-type-BOGO"
                                            class="form-check-label">Buy X Get Y</label></div>
                                    <div class="form-check form-check-inline"><input onchange="toggleDiscountRuleDiv(this.value)" type="radio"
                                            name="type" id="inp-type-Shipping" value="Shipping" class="form-check-input"><label
                                            for="inp-type-Shipping" class="form-check-label">Shipping Discount</label></div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="product-title-input">Category <span
                                        class="text-danger">*</span></label>

                                <select id="category_id" class="form-select" multiple id="choices-category-input"
                                    name="category_id[]" onChange="showProductsonMultiCategorySelect()">
                                    {!! $category_options !!}
                                </select>

                            </div>
                        </div>
                        <x-forms :data="$data" column='2' />
                        @if (count($repeating_group_inputs) > 0)
                            @foreach ($repeating_group_inputs as $grp)
                                <x-repeatable :data="$grp['inputs']" :label="$grp['label']" values="" :index="$loop->index"
                                    :hide="$grp['hide']" :indexWithModal="$grp['index_with_modal']" :modalInputBoxIdWhoseValueToSetInSelect="$grp['modalInputBoxIdWhoseValueToSetInSelect']" />
                            @endforeach
                        @endif

                    </div>
                </div>
            </div>
        @endif
        <div class="row mt-2">
            <div class="col-sm-12 pull-right" style="text-align:right">

                @php
                    $r = 'Submit';
                @endphp
                {!! Form::submit($r)->id(strtolower($module) . '_btn')->primary() !!}

            </div>
        </div>
        {!! Form::close() !!}



    </div>
@endsection
