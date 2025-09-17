@extends('layouts.vendor.app')
@section('content')
    <div class="container-fluid ">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm p-4 text-center">
                    <!-- Avatar and Heading -->
                    <div class="mb-3 position-relative d-inline-block">
                        <img id="profileImage"
                            src="{{ $me->logo_image ? asset('storage/vendor_logo/' . $me->logo_image) : asset('front_assets/img/placeholder.jpg') }}"
                            alt="Avatar" class="rounded-circle mb-2" width="100" height="100">
                        <button id="editAvatar"
                            class="btn btn-sm btn-secondary position-absolute top-0 end-0 translate-middle rounded-pill">
                            Add Logo
                        </button>
                        <h5 class="card-title mt-2 mb-0">Profile Details</h5>
                    </div>

                    <!-- List Section -->
                    <div class="table-responsive">
                        <table class="table table-bordered text-start">
                            <tbody>
                                <tr>
                                    <th style="width: 30%">Is Verified?</th>
                                    <td>
                                      <x-status :status="$me->is_verified" />
                                        {{--@if ($me->status == 'Rejected')
                                            <div class="mt-2 alert alert-danger">
                                                {{ $me->rejection_reason }}
                                            </div>
                                        @endif--}}
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width: 40%">Business Name</th>
                                    <td>{{ ucwords($me->name) }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 40%">Email</th>
                                    <td>{{ $me->email }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 40%">Phone</th>
                                    <td>{{ $me->phone }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 40%">Address</th>
                                    <td>{{ $me->address }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 40%">City</th>
                                    <td>{{ $me->city->name }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 40%">State</th>
                                    <td>{{ $me->state->name }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 40%">Pincode</th>
                                    <td>{{ $me->pincode }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 40%">Email</th>
                                    <td>{{ $me->email }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 40%">GST No</th>
                                    <td>{{ $me->gst }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 40%">PAN No</th>
                                    <td>{{ $me->pan }}</td>
                                </tr>
                                @php
                                    use Illuminate\Support\Facades\Storage;
                                @endphp

                                <tr>
                                    <th style="width: 30%">Gst Document</th>
                                    <td>
                                        @if ($me->gst_image && Storage::disk('public')->exists('vendor_documents/' . $me->id . '/' . $me->gst_image))
                                            @php
                                                $gstFile = 'storage/vendor_documents/' . $me->id . '/' . $me->gst_image;
                                            @endphp
                                            @if (Str::endsWith($me->gst_image, '.pdf'))
                                                <a href="{{ asset($gstFile) }}" target="_blank">
                                                    <img src="{{ asset('pdf.png') }}" style="width:50px;height:50px;"
                                                        alt="PDF File" />
                                                </a>
                                            @else
                                                <img src="{{ asset($gstFile) }}" style="width:50px;height:50px;" />
                                            @endif
                                        @else
                                            <span class="text-muted">Not Available</span>
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th style="width: 30%">PAN Document</th>
                                    <td>
                                        @if ($me->pan_image && Storage::disk('public')->exists('vendor_documents/' . $me->id . '/' . $me->pan_image))
                                            @php
                                                $panFile = 'storage/vendor_documents/' . $me->id . '/' . $me->pan_image;
                                            @endphp
                                            @if (Str::endsWith($me->pan_image, '.pdf'))
                                                <a href="{{ asset($panFile) }}" target="_blank">
                                                    <img src="{{ asset('pdf.png') }}" style="width:50px;height:50px;"
                                                        alt="PDF File" />
                                                </a>
                                            @else
                                                <img src="{{ asset($panFile) }}" style="width:50px;height:50px;" />
                                            @endif
                                        @else
                                            <span class="text-muted">Not Available</span>
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th style="width: 30%">Business License Document</th>
                                    <td>
                                        @if (
                                            $me->business_license_image &&
                                                Storage::disk('public')->exists('vendor_documents/' . $me->id . '/' . $me->business_license_image))
                                            @php
                                                $businessFile =
                                                    'storage/vendor_documents/' .
                                                    $me->id .
                                                    '/' .
                                                    $me->business_license_image;
                                            @endphp
                                            @if (Str::endsWith($me->business_license_image, '.pdf'))
                                                <a href="{{ asset($businessFile) }}" target="_blank">
                                                    <img src="{{ asset('pdf.png') }}" style="width:50px;height:50px;"
                                                        alt="PDF File" />
                                                </a>
                                            @else
                                                <img src="{{ asset($businessFile) }}" style="width:50px;height:50px;" />
                                            @endif
                                        @else
                                            <span class="text-muted">Not Available</span>
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th style="width: 30%">Trademark Document</th>
                                    <td>
                                        @if ($me->trademark_image && Storage::disk('public')->exists('vendor_documents/' . $me->id . '/' . $me->trademark_image))
                                            @php
                                                $trademarkFile =
                                                    'storage/vendor_documents/' . $me->id . '/' . $me->trademark_image;
                                            @endphp
                                            @if (Str::endsWith($me->trademark_image, '.pdf'))
                                                <a href="{{ asset($trademarkFile) }}" target="_blank">
                                                    <img src="{{ asset('pdf.png') }}" style="width:50px;height:50px;"
                                                        alt="PDF File" />
                                                </a>
                                            @else
                                                <img src="{{ asset($trademarkFile) }}" style="width:50px;height:50px;" />
                                            @endif
                                        @else
                                            <span class="text-muted">Not Available</span>
                                        @endif
                                    </td>
                                </tr>





                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
            <div class="col-md-8">

                <div class="card">
                    <div class="card-header py-2">
                        <x-alert />
                        <ul class="nav nav-underline" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#personalDetails" role="tab">
                                    <i class="fas fa-home"></i> Business Details
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#changePassword" role="tab">
                                    <i class="far fa-user"></i> Change Password
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#experience" role="tab">
                                    <i class="far fa-envelope"></i> Documents
                                </a>
                            </li>

                        </ul>
                    </div>
                    <div class="card-body pt-2">
                        <div class="tab-content">
                            <div class="tab-pane active" id="personalDetails" role="tabpanel">
                                <form action="update-profile" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label for="firstnameInput" class="form-label">Business Name</label>
                                                <input type="text" name="name" value="{{ $me->name }}"
                                                    class="form-control" id="firstnameInput"
                                                    placeholder="Enter your business name">
                                            </div>
                                        </div>
                                        <!--end col-->

                                        <!--end col-->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="phonenumberInput" class="form-label">Phone Number</label>
                                                <input type="number" name="phone" value="{{ $me->phone }}"
                                                    class="form-control" id="phonenumberInput"
                                                    placeholder="Enter your phone number">
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="emailInput" class="form-label">Email Address</label>
                                                <input type="email" name="email" value="{{ $me->email }}"
                                                    class="form-control" id="emailInput" placeholder="Enter your email">
                                            </div>
                                        </div>
                                        <!--end col-->

                                        <!--end col-->




                                        <!--end col-->

                                        <!--end col-->
                                        <div class="col-lg-12">
                                            <div class="hstack gap-2 justify-content-end">
                                                <button type="submit" class="btn btn-primary">Update</button>
                                                <button type="button" class="btn btn-soft-success">Cancel</button>
                                            </div>
                                        </div>
                                        <!--end col-->
                                    </div>
                                    <!--end row-->
                                </form>
                            </div>
                            <!--end tab-pane-->
                            <div class="tab-pane" id="changePassword" role="tabpanel">
                                <form method="post" action="/change-password">
                                    @csrf
                                    <div class="row g-2">
                                        <div class="col-lg-4">
                                            <div>
                                                <label for="newpasswordInput" class="form-label">Current Password*</label>
                                                <input type="password" name="current_password" class="form-control"
                                                    id="newpassworddInput" placeholder="Enter current password">
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-lg-4">
                                            <div>
                                                <label for="newpasswordInput" class="form-label">New Password*</label>
                                                <input type="password" class="form-control" name="password"
                                                    id="newpasswordInput" placeholder="Enter new password">
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-lg-4">
                                            <div>
                                                <label for="confirmpasswordInput" class="form-label">Confirm
                                                    Password*</label>
                                                <input type="password" name="password_confirmation" class="form-control"
                                                    id="confirmpasswordInput" placeholder="Confirm password">
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-lg-12">
                                            <div class="text-end">
                                                <button type="submit" class="btn btn-success">Change Password</button>
                                            </div>
                                        </div>
                                        <!--end col-->
                                    </div>
                                    <!--end row-->
                                </form>




                            </div>
                            <!--end tab-pane-->
                            <div class="tab-pane" id="experience" role="tabpanel">
                                <form action="/update-documents/" enctype="multipart/form-data" method="post">
                                    @csrf
                                    <div class="row g-3">

                                        <!-- GST -->
                                        <div class="col-lg-6">
                                            <label for="gst" class="form-label">GST No*</label>
                                            <input type="text" class="form-control" id="gst" name="gst"
                                                placeholder="GST no" value="{{ $me->gst }}" required>
                                        </div>

                                        <!-- GST Certificate -->
                                        <div class="col-lg-6">
                                            <label for="gst_image" class="form-label">
                                                GST Certificate* (PDF only)
                                                @if ($me->gst_image)
                                                    <a href="{{ asset('storage/vendor_documents/' . $me->id . '/' . $me->gst_image) }}"
                                                        target="_blank" class="text-danger ms-2">View Uploaded</a>
                                                @endif
                                            </label>
                                            <input type="file" class="form-control" id="gst_image" name="gst_image"
                                                accept="application/pdf" {{ !$me->gst_image ? 'required' : '' }}>
                                        </div>

                                        <!-- PAN -->
                                        <div class="col-lg-6">
                                            <label for="pan" class="form-label">PAN No*</label>
                                            <input type="text" class="form-control" id="pan" name="pan"
                                                value="{{ $me->pan }}" required>
                                        </div>

                                        <!-- PAN Card -->
                                        <div class="col-lg-6">
                                            <label for="pan_image" class="form-label">
                                                PAN Card* (PDF only)
                                                @if ($me->pan_image)
                                                    <a href="{{ asset('storage/vendor_documents/' . $me->id . '/' . $me->pan_image) }}"
                                                        target="_blank" class="text-danger ms-2">View Uploaded</a>
                                                @endif
                                            </label>
                                            <input type="file" class="form-control" id="pan_image" name="pan_image"
                                                accept="application/pdf" {{ !$me->pan_image ? 'required' : '' }}>
                                        </div>

                                        <!-- Business License -->
                                        <div class="col-lg-6">
                                            <label for="business_license_image" class="form-label">
                                                Business License Certificate (PDF only)
                                                @if ($me->business_license_image)
                                                    <a href="{{ asset('storage/vendor_documents/' . $me->id . '/' . $me->business_license_image) }}"
                                                        target="_blank" class="text-danger ms-2">View Uploaded</a>
                                                @endif
                                            </label>
                                            <input type="file" class="form-control" id="business_license_image"
                                                name="business_license_image" accept="application/pdf">
                                        </div>

                                        <!-- Trademark -->
                                        <div class="col-lg-6">
                                            <label for="trademark_image" class="form-label">
                                                Trademark Certificate (PDF only)
                                                @if ($me->trademark_image)
                                                    <a href="{{ asset('storage/vendor_documents/' . $me->id . '/' . $me->trademark_image) }}"
                                                        target="_blank" class="text-danger ms-2">View Uploaded</a>
                                                @endif
                                            </label>
                                            <input type="file" class="form-control" id="trademark_image"
                                                name="trademark_image" accept="application/pdf">
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="col-12 text-end mt-3">
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-upload me-1"></i> Update
                                            </button>
                                        </div>

                                    </div>
                                </form>



                            </div>
                            <!--end tab-pane-->

                            <!--end tab-pane-->
                        </div>
                    </div>
                </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->

    </div>
    <!-- container-fluid -->
@endsection
@push('scripts')
    <script>
        $('#editAvatar').on('click', function() {
            Swal.fire({
                title: 'Select a new profile picture',
                input: 'file',
                inputAttributes: {
                    accept: 'image/*',
                    'aria-label': 'Upload profile picture'
                },
                showCancelButton: true,
                confirmButtonText: 'Upload',
                cancelButtonText: 'Cancel',
                preConfirm: (file) => {
                    return new Promise((resolve, reject) => {
                        if (!file) {
                            reject('Please choose a file first');
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = (e) => {
                            // Preview update
                            $('#profileImage').attr('src', e.target.result);

                            // Optional: AJAX upload
                            const formData = new FormData();
                            formData.append('avatar', file);

                            $.ajax({
                                url: '/upload-profile-picture', // Replace with your endpoint
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: (res) => {
                                    Swal.fire('Success!',
                                        'Profile picture updated.',
                                        'success');
                                    resolve();
                                },
                                error: () => {
                                    Swal.fire('Error', 'Upload failed',
                                        'error');
                                    reject();
                                }
                            });
                        };
                        reader.readAsDataURL(file);
                    });
                }
            });
        });
    </script>
@endpush
