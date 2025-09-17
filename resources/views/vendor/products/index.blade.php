@extends('layouts.vendor.app')

@section('content')
<div class="container-fluid">
  <!-- Basic Bootstrap Table -->
  <div class="card">
    <div class="card-header">
      <div class="d-flex justify-content-between flex-wrap">
        <h5>All Inventories</h5>

        <div class="d-flex">
          <div class="btn-group" role="group" aria-label="Actions">
           
              <a href="{{ domain_route($plural_lowercase . '.create') }}" class="rounded-sm btn btn-outline-primary btn-sm">
                <i class="bx bx-plus-circle" style="margin-top:-3px"></i> Add  Product
              </a>
           

            @if ($has_export)
              <button type="button" class="mx-1 btn btn-outline-warning rounded-sm btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                Upload Product
              </button>

              <!-- <button type="button" class="mx-1 btn btn-outline-danger  rounded-sm btn-sm" data-bs-toggle="modal" data-bs-target="#importVariantModal">
                Upload Variant
              </button> -->

              <button type="button" style="border-radius:8px!important" class="rounded-sm dt-button  btn-sm buttons-collection btn
               btn-outline-primary dropdown-toggle me-2" data-bs-toggle="dropdown">
                <span><i class="bx bx-export me-sm-2"></i>
                 <span class="d-none d-sm-inline-block">Download Spreadsheet</span></span>
              </button>

              <ul class="dropdown-menu">
                <li>
                  <a class="dropdown-item" href="{{ domain_route('products.export_template') }}">
                    <i class="bx bx-file me-2"></i> Product  Spreadsheet
                  </a>
                  <!-- <a class="dropdown-item" href="{{ domain_route('products.export_variant_template') }}">
                    <i class="bx bx-printer me-2"></i>  Variant Spreadhsheet
                  </a> -->
                  <a class="dropdown-item" href="{{ domain_route('products.export-category') }}">
                    <i class="bx bx-printer me-2"></i> Check Available Categories 
                  </a>
                </li>
              </ul>
            @endif
          </div>
        </div>
      </div>

      <!-- Import Discount Modal -->
      <div class="modal fade" id="discountImportModal" tabindex="-1">
        <div class="modal-dialog">
          <form id="discountImportForm" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Import Product Discounts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <input type="file" name="file" accept=".xlsx,.xls" class="form-control" required />
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-sm">Upload</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Import Modal -->
      <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <form id="importForm" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label for="excelFile" class="form-label">Choose Excel File</label>
                  <input type="file" class="form-control" name="file" id="excelFile" required accept=".xls,.xlsx">
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-success  btn-sm">Import</button>
                <button type="button" class="btn btn-secondary  btn-sm" data-bs-dismiss="modal">Cancel</button>
              </div>
            </div>
          </form>
        </div>
      </div>
      <div class="modal fade" id="importVariantModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <form id="importVariantForm" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label for="excelFile" class="form-label">Choose Excel File</label>
                  <input type="file" class="form-control" name="file" id="excelFile" required accept=".xls,.xlsx">
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-success">Import</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="d-flex justify-content-between flex-wrap mt-3 pull-end">
        <x-groupButtonIndexPage 
        :whichButtonsToHideArray="['trash','bulk']"
          :filterableFields="$filterable_fields" 
          :pluralLowercase="$plural_lowercase" 
          :bulkUpdate="$bulk_update" 
          :moduleTableName="$module_table_name" 
        />
        <x-search :searchableFields="$searchable_fields" />
      </div>
    </div> 

    <div class="card-body">
      <div class="table-responsive text-nowrap">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th># </th>
              @foreach ($table_columns as $t)
                @if ($t['sortable'] === 'Yes')
                  <x-row column="{{ $t['column'] }}" label="{{ str_replace(' Id', '', $t['label']) }}" />
                @else
                  <th>{{ str_replace(' Id', '', $t['label']) }}</th>
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

      <!-- Hidden Inputs for Pagination & Sorting -->
      <input type="hidden" name="hidden_page" id="hidden_page" value="{{ $_GET['page'] ?? '1' }}" />
      <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="" />
      <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
      <input type="hidden" name="search_by" id="search_by" value="" />
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('importForm').addEventListener('submit', function (e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);

  Swal.fire({
    title: 'Importing...',
    text: 'Please wait while the products are being imported.',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  fetch("{{ domain_route('products.import') }}", {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
    },
    body: formData
  })
  .then(response => {
    if (!response.ok) throw new Error('Import failed');
    return response.json();
  })
  .then(data => {
    if (data.success) {
      Swal.fire('Success', data.message || 'Products imported successfully!', 'success');
      form.reset();
      bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
    } else {
      Swal.fire('Error', data.message, 'error');
    }
  })
  .catch(error => {
    console.error(error);
    Swal.fire('Error', 'Something went wrong while importing.', 'error');
  });
});
document.getElementById('importVariantForm').addEventListener('submit', function (e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);

  Swal.fire({
    title: 'Importing...',
    text: 'Please wait while the products are being imported.',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  fetch("{{ domain_route('products.import_variant') }}", {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
    },
    body: formData
  })
  .then(response => {
    console.log('here ')
    if (!response.ok) throw new Error('Import failed');
    return response.json();
  })
  .then(data => {
     console.log('here ',data)
    if (data.success) {
      Swal.fire('Success', data.message || 'Products imported successfully!', 'success');
      form.reset();
      bootstrap.Modal.getInstance(document.getElementById('importVariantModal')).hide();
    } else {
      Swal.fire('Error', data.message, 'error');
    }
  })
  .catch(error => {
    console.error(error);
    Swal.fire('Error', 'Something went wrong while importing.', 'error');
  });
});

$('#discountImportForm').submit(function (e) {
  e.preventDefault();
  let formData = new FormData(this);

  Swal.fire({
    title: 'Uploading...',
    text: 'Please wait while we update discounts.',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  $.ajax({
    url: "{{ domain_route('products.import-discounts') }}",
    method: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (res) {
      Swal.close();
      if (res.success) {
        Swal.fire('Success', res.message, 'success');
        $('#discountImportModal').modal('hide');
      } else {
        Swal.fire('Error', res.message, 'error');
      }
    },
    error: function (xhr) {
      Swal.close();
      Swal.fire('Error', xhr.responseJSON?.message || 'Upload failed.', 'error');
    }
  });
});
$(document).on('change', '.visibility-toggle', function() {
    let id = $(this).data('id');
    let status = $(this).is(':checked') ? 1 : 0;

    $.ajax({
        url: "{{ route('singleFieldUpdateFromTable') }}", // define this route in your web.php
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            table: 'products',
            field: 'visibility',
            val: status?'Public':'Hidden',
            id
        },
        success: function(response) {
            console.log(response.message);
        },
        error: function(xhr) {
            alert("Failed to update visibility");
        }
    });
});
</script>
@endpush
