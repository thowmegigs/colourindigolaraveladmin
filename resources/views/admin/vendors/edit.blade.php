@extends('layouts.admin.app')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm p-4 text-center">
                    <!-- Avatar and Heading -->
                    <div class="mb-3 position-relative d-inline-block">
                        <img id="profileImage"
                            src="{{ $model->logo_image ? asset('storage/vendor_logo/' . $model->logo_image) : asset('front_assets/img/placeholder.jpg') }}"
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
                                    <th style="width: 30%">is Verified?</th>
                                    <td>
                                        <x-status :status="$model->is_verified" />
                                         {{-- @if($me->is_verified=='No')
                                        <div class="mt-2 alert alert-danger">
                                            {{ $me->rejection_reason }}
                                        </div>
                                    @endif--}}
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width: 40%">Business Name</th>
                                    <td>{{ ucwords($model->name) }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 40%">Email</th>
                                    <td>{{ $model->email }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 40%">Phone</th>
                                    <td>{{ $model->phone }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 40%">Address</th>
                                    <td>{{ $model->address }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 40%">Address1</th>
                                    <td>{{ $model->address2 }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 40%">City</th>
                                    <td>{{ $model->city->name }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 40%">State</th>
                                    <td>{{ $model->state->name }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 40%">Pincode</th>
                                    <td>{{ $model->pincode }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 40%">Email</th>
                                    <td>{{ $model->email }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 40%">GST No</th>
                                    <td>{{ $model->gst }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 40%">PAN No</th>
                                    <td>{{ $model->pan }}</td>
                                </tr>

                                  @php
                                    use Illuminate\Support\Facades\Storage;
                                @endphp

                                <tr>
                                    <th style="width: 30%">Gst Document</th>
                                    <td>
                                        @if ($model->gst_image && Storage::disk('public')->exists('vendor_documents/' . $model->id . '/' . $model->gst_image))
                                            @php
                                                $gstFile = 'storage/vendor_documents/' . $model->id . '/' . $model->gst_image;
                                            @endphp
                                            @if (Str::endsWith($model->gst_image, '.pdf'))
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
                                        @if ($model->pan_image && Storage::disk('public')->exists('vendor_documents/' . $model->id . '/' . $model->pan_image))
                                            @php
                                                $panFile = 'storage/vendor_documents/' . $model->id . '/' . $model->pan_image;
                                            @endphp
                                            @if (Str::endsWith($model->pan_image, '.pdf'))
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
                                            $model->business_license_image &&
                                                Storage::disk('public')->exists('vendor_documents/' . $model->id . '/' . $model->business_license_image))
                                            @php
                                                $businessFile =
                                                    'storage/vendor_documents/' .
                                                    $model->id .
                                                    '/' .
                                                    $model->business_license_image;
                                            @endphp
                                            @if (Str::endsWith($model->business_license_image, '.pdf'))
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
                                        @if ($model->trademark_image && Storage::disk('public')->exists('vendor_documents/' . $model->id . '/' . $model->trademark_image))
                                            @php
                                                $trademarkFile =
                                                    'storage/vendor_documents/' . $model->id . '/' . $model->trademark_image;
                                            @endphp
                                            @if (Str::endsWith($model->trademark_image, '.pdf'))
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
            <div class="col-md-8 pt-5">

                <div class="card mt-xxl-n5">
                    <x-alert />
                    <div class="card-header py-2">

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
                                <form action="/update-profile/{{ $model->id }}" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label for="firstnameInput" class="form-label">Business Name</label>
                                                <input type="text" name="name" value="{{ $model->name }}"
                                                    class="form-control" id="firstnameInput"
                                                    placeholder="Enter your business name">
                                            </div>
                                        </div>
                                        <!--end col-->

                                        <!--end col-->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="phonenumberInput" class="form-label">Phone Number</label>
                                                <input type="number" name="phone" value="{{ $model->phone }}"
                                                    class="form-control" id="phonenumberInput"
                                                    placeholder="Enter your phone number">
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="emailInput" class="form-label">Email Address</label>
                                                <input type="email" name="email" value="{{ $model->email }}"
                                                    class="form-control" id="emailInput" placeholder="Enter your email">
                                            </div>
                                        </div>
                                        <!--end col-->

                                        <!--end col-->



                                        <!--end col-->
                                        <div class="col-md-6 mb-3">
                                            <label for="storeCategory" class="form-label">
                                                Address
                                            </label>
                                            <textarea class="form-control" id="address" name="address" required>{{ $model->address }}</textarea>


                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="storeCategory" class="form-label">
                                                Address2
                                            </label>
                                            <textarea class="form-control" id="address" name="address2" required required>{{ $model->address2 }}</textarea>


                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="storeCategory" class="form-label">
                                                State
                                            </label>
                                            <select class="form-select" id="inp-state_id" name="state_id" required>
                                                <option value="">Select State</option>
                                                @foreach ($states as $st)
                                                    <option value="{{ $st->id }}"
                                                        @if ($st->id == $model->state_id) selected @endif>
                                                        {{ $st->name }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="state" class="form-label">
                                                City
                                            </label>
                                            <select class="form-select select2-ajax" id="inp-city_id" name="city_id"
                                                required>
                                                @foreach ($cities as $city)
                                                    <option value="{{ $city->id }}"
                                                        @if ($model->city_id == $city->id) selected @endif>
                                                        {{ $city->name }}</option>
                                                @endforeach
                                            </select>

                                        </div>

                                        <!--end col-->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="zipcodeInput" class="form-label">Pin Code</label>
                                                <input type="number" name="pincode" class="form-control" minlength="6"
                                                    maxlength="6" id="zipcodeInput" placeholder="Enter zipcode"
                                                    value="{{ $model->pincode }}">
                                            </div>
                                        </div>
                                        <!--end col-->

                                        <!--end col-->
                                        <div class="col-lg-12">
                                            <div class="hstack gap-2 justify-content-end">
                                                <button type="submit" class="btn btn-primary">Updates</button>
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
                                <form method="post" action="/change-password/{{ $model->id }}">
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
                                <form action="/update-documents/{{ $model->id }}" enctype="multipart/form-data"
                                    method="post">
                                    @csrf
                                      <div class="row g-3">

                                        <!-- GST -->
                                        <div class="col-lg-6">
                                            <label for="gst" class="form-label">GST No*</label>
                                            <input type="text" class="form-control" id="gst" name="gst"
                                                placeholder="GST no" value="{{ $model->gst }}" required>
                                        </div>

                                        <!-- GST Certificate -->
                                        <div class="col-lg-6">
                                            <label for="gst_image" class="form-label">
                                                GST Certificate* (PDF only)
                                                @if ($model->gst_image)
                                                    <a href="{{ asset('storage/vendor_documents/' . $model->id . '/' . $model->gst_image) }}"
                                                        target="_blank" class="text-danger ms-2">View Uploaded</a>
                                                @endif
                                            </label>
                                            <input type="file" class="form-control" id="gst_image" name="gst_image"
                                                accept="application/pdf" {{ !$model->gst_image ? 'required' : '' }}>
                                        </div>

                                        <!-- PAN -->
                                        <div class="col-lg-6">
                                            <label for="pan" class="form-label">PAN No*</label>
                                            <input type="text" class="form-control" id="pan" name="pan"
                                                value="{{ $model->pan }}" required>
                                        </div>

                                        <!-- PAN Card -->
                                        <div class="col-lg-6">
                                            <label for="pan_image" class="form-label">
                                                PAN Card* (PDF only)
                                                @if ($model->pan_image)
                                                    <a href="{{ asset('storage/vendor_documents/' . $model->id . '/' . $model->pan_image) }}"
                                                        target="_blank" class="text-danger ms-2">View Uploaded</a>
                                                @endif
                                            </label>
                                            <input type="file" class="form-control" id="pan_image" name="pan_image"
                                                accept="application/pdf" {{ !$model->pan_image ? 'required' : '' }}>
                                        </div>

                                        <!-- Business License -->
                                        <div class="col-lg-6">
                                            <label for="business_license_image" class="form-label">
                                                Business License Certificate (PDF only)
                                                @if ($model->business_license_image)
                                                    <a href="{{ asset('storage/vendor_documents/' . $model->id . '/' . $model->business_license_image) }}"
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
                                                @if ($model->trademark_image)
                                                    <a href="{{ asset('storage/vendor_documents/' . $model->id . '/' . $model->trademark_image) }}"
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
                                url: '/upload-profile-picture/{{ $model->id }}', // Replace with your endpoint
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
