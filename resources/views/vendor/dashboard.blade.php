@extends('layouts.vendor.app')
@section('content')
<style>
    table th{
        font-size:12px!important;
    }
</style>
    <div class="container-fluid">

       <div class="row">
            <div class="col">

                <div class="h-100">
                    <div class="row">
                        <div class="col-12">
                            {{-- your original vendor alerts/verification code stays unchanged --}}
                            @if (!auth()->id())
                                @php
                                    $me = auth()->guard('vendor')->user();
                                    $is_uploaded_docs =
                                        !empty($me->gst) &&
                                        !empty($me->gst_image) &&
                                        !empty($me->pan) &&
                                        !empty($me->pan_image);
                                @endphp
                                @if(!$is_uploaded_docs)
                                    <div class="alert bg-warning-subtle alert-dismissible d-flex fade show" role="alert">
                                        <i class="bi bi-exclamation-triangle-fill text-warning fs-3 me-3 lh-1"></i>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-warning">Attention needed!</h6>
                                            <p>Your vendor documents are pending verification. Please upload them
                                                for verification , to avoid account deactivation or rejection. <a href="/profile" style="width:200px" class="text-danger ">Click here</a></p>
                                            
                                        </div>
                                        <button type="button" class="btn-close "  data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @else 
                                    @if($me->is_verified=='No')
                                            <div class="alert alert-warning d-flex align-items-center p-3 mb-3 rounded-3 shadow-sm" role="alert">
                                                <i class="bi bi-hourglass-split me-2 fs-5"></i>
                                                <div>
                                                    <strong>Verification Underway:</strong> Your account verification process is currently in progress. We’ll notify you once it’s complete.
                                                </div>
                                            </div>
                                    @else 
                                   @if(\Carbon\Carbon::parse($me->verified_at)->addMinutes(3) > \Carbon\Carbon::now())
                                        <div class="alert alert-success d-flex align-items-center p-3 mb-3 rounded-3 shadow-sm" role="alert">
                                            <i class="bi bi-check-circle me-2 fs-5"></i>
                                            <div>
                                                <strong>Verified:</strong> Your account is successfully verified.
                                            </div>
                                        </div>
                                    @endif
                                    @endif
                                @endif
                            @endif
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->

                    <div class="row">
                        @foreach ($widgets as $w)
                            <div class="col-xl-3 col-md-6">
                                <div class="card mb-4 card-bg">
                                    <div class="card-body text-white">
                                        <div class="d-flex align-items-center">
                                            <div class="p-2 border border-white rounded-circle me-3">
                                                <div class="icon-box md bg-white rounded-5">
                                                    <i class="bi bi-box fs-4 text-danger"></i>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <h2 class="m-0 lh-1">{{ $w['value'] }} </h2>
                                                <p class="m-0 opacity-100">{{ ucwords($w['title']) }}</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-1">
                                            <a class="text-white" href="javascript:void(0);">
                                                <span>View All</span>
                                                <i class="bi bi-arrow-right ms-2"></i>
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div> <!-- end row-->
                </div> <!-- end .h-100-->
            </div> <!-- end col -->
        </div>

        {{-- 📊 Chart Section --}}
        <div class="row g-4">
            <!-- Sales Overview -->
            <div class="col-md-6">
                <div class="card p-3">
                    <h5>Sales Overview</h5>
                    @if($salesOverview->isEmpty())
                        <div class="d-flex flex-column align-items-center justify-content-center py-5 text-muted">
                            <i class="bi bi-bar-chart-line fs-1 mb-2"></i>
                            <span>No sales data available</span>
                        </div>
                    @else
                        <div id="sales-overview"></div>
                    @endif
                </div>
            </div>

            <!-- Top Products -->
            <div class="col-md-6">
                <div class="card p-3">
                    <h5>Top Selling Products</h5>
                    @if($topProducts->isEmpty())
                        <div class="d-flex flex-column align-items-center justify-content-center py-5 text-muted">
                            <i class="bi bi-grid fs-1 mb-2"></i>
                            <span>No product data available</span>
                        </div>
                    @else
                        <div id="top-products"></div>
                    @endif
                </div>
            </div>

            <!-- Monthly Orders -->
            <div class="col-md-6">
                <div class="card p-3">
                    <h5>Monthly Orders</h5>
                    @if($monthlyOrders->isEmpty())
                        <div class="d-flex flex-column align-items-center justify-content-center py-5 text-muted">
                            <i class="bi bi-calendar3 fs-1 mb-2"></i>
                            <span>No order data available</span>
                        </div>
                    @else
                        <div id="monthly-orders"></div>
                    @endif
                </div>
            </div>
        </div>

    </div>
    <!-- container-fluid -->
@endsection

@push('scripts')
<script>
    // Base brand color
    const brandColor = "#ba1654";
    const brandTint = "#e75480"; // lighter tint for gradients

    // Sales Overview (Area Chart)
    @if(!$salesOverview->isEmpty())
    new ApexCharts(document.querySelector("#sales-overview"), {
        chart: { type: 'area', height: 300, background: 'transparent', foreColor: '#666' },
        series: [{ name: 'Revenue', data: @json($salesOverview->pluck('total_sales')) }],
        xaxis: { categories: @json($salesOverview->pluck('date')) },
        colors: [brandColor],
        fill: {
            type: "gradient",
            gradient: {
                shadeIntensity: 1,
                gradientToColors: [brandTint],
                opacityFrom: 0.7,
                opacityTo: 0.2,
                stops: [0, 90, 100]
            }
        },
        stroke: { curve: 'smooth', width: 3 }
    }).render();
    @endif

    // Top Products (Horizontal Bar)
    @if(!$topProducts->isEmpty())
    new ApexCharts(document.querySelector("#top-products"), {
        chart: { type: 'bar', height: 300, background: 'transparent', foreColor: '#666' },
        plotOptions: { bar: { horizontal: true, borderRadius: 6 } },
        series: [{ name: 'Units Sold', data: @json($topProducts->pluck('total_qty')) }],
        xaxis: {
            categories: @json($topProducts->pluck('name')->map(fn($name) => \Illuminate\Support\Str::limit($name, 20)))
        },
        colors: [brandColor, brandTint, "#f28da0", "#f5b7c2"] // palette of tints
    }).render();
    @endif

    // Monthly Orders (Donut Chart)
    @if(!$monthlyOrders->isEmpty())
    new ApexCharts(document.querySelector("#monthly-orders"), {
        chart: { type: 'donut', height: 300, background: 'transparent', foreColor: '#666' },
        series: @json($monthlyOrders->pluck('total_orders')),
        labels: @json($monthlyOrders->pluck('month')),
        colors: [brandColor, brandTint, "#f28da0", "#f5b7c2"], // same tint palette
        legend: { position: 'bottom' }
    }).render();
    @endif
</script>
@endpush
