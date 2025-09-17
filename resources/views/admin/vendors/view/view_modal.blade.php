@extends('layouts.admin.app')
@section('content')
    <div class="container-fluid">
      
    
    <div class="row">
      <div class="col-md-12">
    <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
        <div class="row g-0">
            <!-- Left Column: Profile & Basic Details -->
            <div class="col-md-5 border-end">
                <div class="p-4 text-center">
                    <!-- Profile Image & Button -->
                    <div class="mb-3 position-relative d-inline-block">
                        <img id="profileImage"
                            src="{{ $row->logo_image ? asset('storage/vendor_logo/' . $row->logo_image) : asset('front_assets/img/placeholder.jpg') }}"
                            alt="Vendor Logo"
                            class="rounded-circle border border-3 border-light shadow-sm"
                            width="110" height="110">
                        <button id="editAvatar"
                            class="btn btn-sm btn-primary position-absolute top-0 end-0 translate-middle rounded-pill px-3 py-1 shadow-sm">
                            Add Logo
                        </button>
                    </div>
                    <h5 class="fw-bold text-dark">{{ ucwords($row->name) }}</h5>
                    <p class="text-muted small mb-3">Vendor Information Overview</p>

                    <!-- Status -->
                    <div class="mb-3">
                        <x-status :status="$row->is_verified" />
                       {{-- @if ($row->status == 'Rejected')
                            <div class="mt-2 alert alert-danger small p-2">
                                {{ $row->rejection_reason }}
                            </div>
                        @endif--}}
                    </div>

                    <!-- Basic Info -->
                    <table class="table table-borderless text-start small">
                        <tbody>
                            <tr>
                                <th class="text-muted w-40">Email</th>
                                <td>{{ $row->email }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Phone</th>
                                <td>{{ $row->phone }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Address</th>
                                <td>{{ $row->address }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">City</th>
                                <td>{{ $row->city->name }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">State</th>
                                <td>{{ $row->state->name }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Pincode</th>
                                <td>{{ $row->pincode }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Column: PDF Documents -->
            <div class="col-md-7">
                <div class="p-4">
                    <h5 class="fw-semibold mb-3">Business & Compliance Documents</h5>
                    <table class="table table-sm align-middle text-start">
                        <tbody>
                            <tr>
                                <th class="text-muted w-40">GST No</th>
                                <td>{{ $row->gst ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">PAN No</th>
                                <td>{{ $row->pan ?: 'N/A' }}</td>
                            </tr>

                            <!-- GST PDF -->
                            <tr>
                                <th class="text-muted">GST Document</th>
                                <td>
                                    @if ($row->gst_image)
                                        <a href="{{ asset('storage/vendor_documents/' . $row->id . '/' . $row->gst_image) }}"
                                            target="_blank" class="btn btn-outline-primary btn-sm">
                                            View GST PDF
                                        </a>
                                    @else
                                        <span class="text-muted small">Not uploaded</span>
                                    @endif
                                </td>
                            </tr>

                            <!-- PAN PDF -->
                            <tr>
                                <th class="text-muted">PAN Document</th>
                                <td>
                                    @if ($row->pan_image)
                                        <a href="{{ asset('storage/vendor_documents/' . $row->id . '/' . $row->pan_image) }}"
                                            target="_blank" class="btn btn-outline-primary btn-sm">
                                            View PAN PDF
                                        </a>
                                    @else
                                        <span class="text-muted small">Not uploaded</span>
                                    @endif
                                </td>
                            </tr>

                            <!-- Business License PDF -->
                            <tr>
                                <th class="text-muted">Business License</th>
                                <td>
                                    @if ($row->business_license_image)
                                        <a href="{{ asset('storage/vendor_documents/' . $row->id . '/' . $row->business_license_image) }}"
                                            target="_blank" class="btn btn-outline-primary btn-sm">
                                            View License PDF
                                        </a>
                                    @else
                                        <span class="text-muted small">Not uploaded</span>
                                    @endif
                                </td>
                            </tr>

                            <!-- Trademark PDF -->
                            <tr>
                                <th class="text-muted">Trademark</th>
                                <td>
                                    @if ($row->trademark_image)
                                        <a href="{{ asset('storage/vendor_documents/' . $row->id . '/' . $row->trademark_image) }}"
                                            target="_blank" class="btn btn-outline-primary btn-sm">
                                            View Trademark PDF
                                        </a>
                                    @else
                                        <span class="text-muted small">Not uploaded</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


    </div>
    </div>
@endsection