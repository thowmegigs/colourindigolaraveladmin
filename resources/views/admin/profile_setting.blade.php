@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">


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
                                <th style="width: 30%">Status</th>
                                <td>
                                    {{$me->is_verified}}
                                   {{-- @if($me->is_verified=='No')
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

                            <tr>
                                <th style="width: 30%">Gst Image</th>
                                <td> @if($me->gst_image)

                                    <img src="{{ asset('storage/vendor_documents/' .$me->id.'/'.$me->gst_image) }}"
                                        style="width:50px;height:50px;" />

                                    @endif</td>
                            </tr>
                            <tr>
                                <th style="width: 30%">PAN Image</th>
                                <td> @if($me->pan_image)

                                    <img src="{{ asset('storage/vendor_documents/' .$me->id.'/'.$me->pan_image) }}"
                                        style="width:50px;height:50px;" />

                                    @endif</td>
                            </tr>
                            <tr>
                                <th style="width: 30%">Business License Image</th>
                                <td> @if($me->business_license_image)

                                    <img src="{{ asset('storage/vendor_documents/' .$me->id.'/'.$me->business_license_image) }}"
                                        style="width:50px;height:50px;" />

                                    @endif</td>
                            </tr>
                            <tr>
                                <th style="width: 30%">Trademark Image</th>
                                <td> @if($me->trademark_image)

                                    <img src="{{ asset('storage/vendor_documents/' .$me->id.'/'.$me->trademark_image) }}"
                                        style="width:50px;height:50px;" />

                                    @endif</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
        <div class="col-md-8">
           
            <div class="card mt-xxl-n5">
                <div class="card-header">
                     <x-alert />
                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
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
                <div class="card-body p-4">
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
                                    <div class="col-md-6 mb-3">
                                        <label for="storeCategory" class="form-label">
                                            Address
                                        </label>
                                        <textarea class="form-control" id="address" name="address"
                                            required>{{ $me->address }}</textarea>


                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="storeCategory" class="form-label">
                                            Address2
                                        </label>
                                        <textarea class="form-control" id="address" name="address2" required
                                            required>{{ $me->address2 }}</textarea>


                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="storeCategory" class="form-label">
                                            State
                                        </label>
                                        <select class="form-select" id="inp-state_id" name="state_id" required>
                                            <option value="">Select State</option>
                                            @foreach($states as $st)
                                                <option value="{{ $st->id }}" @if($st->id==$me->state_id) selected @endif
                                            >{{ $st->name }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="state" class="form-label">
                                            City
                                        </label>
                                        <select class="form-select select2-ajax" id="inp-city_id" name="city_id"
                                            required>
                                            @foreach($cities as $city)
                                            <option value="{{ $city->id }}" 
                                            @if($me->city_id==$city->id)
                                                 selected
                                            @endif
                                            >{{ $city->name }}</option>
                                             @endforeach
                                        </select>

                                    </div>

                                    <!--end col-->
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label for="zipcodeInput" class="form-label">Pin Code</label>
                                            <input type="number" name="pincode" class="form-control" minlength="6"
                                                maxlength="6" id="zipcodeInput" placeholder="Enter zipcode"
                                                value="{{ $me->pincode }}">
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
                            <form action="/update-documents" enctype="multipart/form-data" method="post">
                                @csrf<div id="newlink">
                                <div id="1">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="jobTitle" class="form-label">GST No</label>
                                                <input type="text" class="form-control" id="jobTitle" name="gst"
                                                    placeholder="GST no" value="{{ $me->gst }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="jobTitle" class="form-label">GST Certificate(Image only
                                                    )</label>
                                                <input type="file" class="form-control" id="sd" name="gst_image">
                                            </div>
                                            @if($me->gst_image)
                                                <img src="{{ asset('storage/vendor_documents/' .$me->id.'/'.$me->gst_image) }}"
                                                    style="width:50px;height:50px;" />
                                            @endif
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="jobTitle" class="form-label">PAN No</label>
                                                <input type="text" class="form-control" value="{{ $me->pan }}"
                                                    id="jpan" name="pan">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="jobTitle" class="form-label">PAN Image(Image only
                                                    )</label>
                                                <input type="file" class="form-control" id="jobTidftle" name="pan_image">
                                            </div>
                                            @if($me->pan_image)
                                                <img src="{{ asset('storage/vendor_documents/' .$me->id.'/'.$me->pan_image) }}"
                                                    style="width:50px;height:50px;" />
                                            @endif
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="jobTitle" class="form-label">Business License
                                                    Certificate(Image only )</label>
                                                <input type="file" class="form-control" id="dff"
                                                    name="business_license_image">
                                            </div>
                                            @if($me->business_license_imge)
                                                <img src="{{ asset('storage/vendor_documents/' .$me->id.'/'.$me->business_license_imge) }}"
                                                    style="width:50px;height:50px;" />
                                            @endif
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="jobTitle" class="form-label">Trademark Certificate (Image Only)</label>
                                                <input type="file" class="form-control" id="dffff"
                                                    name="trademark_image">
                                            </div>
                                            @if($me->trademark_image)
                                                <img src="{{ asset('storage/vendor_documents/' .$me->id.'/'.$me->trademark_image) }}"
                                                    style="width:50px;height:50px;" />
                                            @endif
                                        </div>
                                        <!--end col-->

                                        <!--end col-->


                                    </div>
                                    <!--end row-->
                                </div>
                        </div>
                        <div id="newForm" style="display: none;">

                        </div>
                        <div class="col-lg-12">
                            <div class="hstack gap-2">
                                <button type="submit" class="btn btn-success">Update</button>
                            </div>
                        </div>
                        <!--end col-->
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
        $('#editAvatar').on('click', function () {
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
