@extends('layouts.admin.app')
@section('content')

    <div class="container ">
   
    <div class="row">
        <div class="col-md-4">
            <form id="uploadForm" class="mb-4 card p-3 " enctype="multipart/form-data">
                @csrf
                <h5>Add Images</h5>
                <div class="mb-3">
                    <label class="form-label">Select Folder</label>
                    <select name="folder" class="form-select" required>
                        @foreach ($data as $group)
                            <option value="{{ $group['folder'] }}">{{ $group['folder'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload Images</label>
                    <input type="file" name="images[]" class="form-control" multiple required>
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>

            <form method="POST" action="/file-manager/create-folder" class="mb-4 card p-3 ">
                @csrf
                <h5>Create Folder</h5>
                <div class="mb-3">
                    <label class="form-label">New Folder Name</label>
                    <input type="text" name="folder" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Create Folder</button>
            </form>

          
        </div>

       
        <div class="col-md-8">
            <h5>List of Folders</h5>
            <div class="row">
                @foreach ($data as $group)
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="card-body">
                                <i class="mdi mdi-folder" style="color:darkorange;font-size:60px"></i>
                                <h5 class="my-0">{{ $group['folder'] }}</h5>
                                <a href="{{ url('/admin/file-manager/folder/' . $group['folder']) }}" class="btn-sm btn-icon btn btn-primary"><i class="mdi mdi-eye"></i></a>
                                <!-- Delete Button -->
                                <button class="btn btn-danger btn-sm btn-icon" onclick="deleteFolder('{{ $group['folder'] }}')"><i class="mdi mdi-delete"></i></button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
</div>
@endsection
@push('scripts')
<script>
  function copyToClipboard(id) {
        const input = document.getElementById(id);
        navigator.clipboard.writeText(input.value).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Image URL copied to clipboard.',
                timer: 1200,
                showConfirmButton: false
            });
        }).catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: 'Failed to copy the URL.'
            });
        });
    } 
    $('#uploadForm').on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: '{{ url("/admin/file-manager/upload") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                Swal.fire({
                    title: 'Uploading...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function (res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Uploaded!',
                    text: res.message,
                }).then(() => {
                    location.reload();
                });
            },
            error: function (xhr) {
                let errMsg = 'Upload failed.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errMsg
                });
            }
        });
    });
    function deleteFolder(folderName) {
        Swal.fire({
            title: 'Are you sure?',
            text: `This will permanently delete the folder: ${folderName}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX request to delete the folder
                $.ajax({
                    url: '{{ url("/admin/file-manager/delete-folder") }}',
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        folder: folderName
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: `The folder ${folderName} has been deleted.`,
                        }).then(() => {
                            // Remove the folder card from the UI
                            $(`[data-folder="${folderName}"]`).remove();
                        });
                    },
                    error: function(xhr) {
                        let errMsg = 'Failed to delete folder.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errMsg
                        });
                    }
                });
            }
        });
    }
</script>
@endpush