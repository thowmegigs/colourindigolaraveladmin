@extends('layouts.vendor.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Offcanvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEnd" aria-labelledby="offcanvasEndLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasEndLabel" class="offcanvas-title text-capitalize">
                {{ properPluralName($plural_lowercase) }}
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0">
            <p class="text-center">
                Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web
                designs...
            </p>
            <button type="button" class="btn btn-primary mb-2 d-grid w-100">Continue</button>
            <button type="button" class="btn btn-label-secondary d-grid w-100" data-bs-dismiss="offcanvas">Cancel</button>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between flex-wrap">
                <h5>All {{ properPluralName($plural_lowercase) }}</h5>
            </div>
            <br>
            <div class="d-flex justify-content-between flex-wrap mt-3">
                <x-filter :data="$filterable_fields" />
                <x-search :searchableFields="$searchable_fields" />
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th># <input class="form-check-input" type="checkbox" id="check_all" /></th>
                            @foreach ($table_columns as $t)
                                @if ($t['sortable'] === 'Yes')
                                    <x-row column="{{ $t['column'] }}" label="{{ $t['label'] }}" />
                                @else
                                    <th>{{ $t['label'] }}</th>
                                @endif
                            @endforeach
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0" id="tbody">
                        @include('vendor.' . $plural_lowercase . '.page')
                    </tbody>
                </table>
            </div>

            <!-- Hidden Fields -->
            <input type="hidden" name="hidden_page" id="hidden_page"
                value="{{ request('page', 1) }}" />
            <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="" />
            <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
            <input type="hidden" name="search_by" id="search_by" value="" />
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $(document).on('click', '.approve-btn', function () {
            const id = $(this).data('id');
            const status = $(this).data('status');

            if (status === 'Approved') {
                Swal.fire({
                    title: 'Loading...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '/update_return_status',
                    method: 'POST',
                    data: {
                        id: id,
                        status: status,
                        reason: '',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function () {
                        Swal.close();
                        const modal = new bootstrap.Modal(document.getElementById('exampleModal' + id));
                        modal.hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Succefully '+status,
                            confirmButtonText: 'Great!'
                            });
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'Error approving item'
                        });
                    }
                });
            } else {
                $('#exampleModal' + id).modal('hide');
                Swal.fire({
                    title: 'Reject Reason',
                    input: 'textarea',
                    inputLabel: 'Please provide a reason for rejection',
                    inputPlaceholder: 'Type your reason here...',
                    inputAttributes: {
                        'aria-label': 'Type your reason here'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    cancelButtonText: 'Cancel',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'You need to provide a reason!';
                        }
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        const reason = result.value;
                        Swal.fire({
                            title: 'Loading...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: '/update_return_status',
                            method: 'POST',
                            data: {
                                id: id,
                                status: status,
                                reason: reason,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function () {
                                Swal.close();
                                  Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Succefully '+status,
                                    confirmButtonText: 'Great!'
                                    });
                              
                            },
                            error: function () {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed',
                                    text: 'Error rejecting item'
                                });
                            }
                        });
                    }
                });
            }
        });
    });
</script>
@endpush
