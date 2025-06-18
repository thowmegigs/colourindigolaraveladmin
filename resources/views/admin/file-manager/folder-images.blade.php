@extends('layouts.admin.app')
@section('content')

<div class="container  ">
    <h4 class="mb-4" >{{ $folderName }}</h4>

   @if(count($images)>0)
    <form method="POST" action="{{ url('/admin/file-manager/delete') }}" id="deleteForm">
        @csrf
        <input type="hidden" name="folder" value="{{ $folderName }}">
       

        <div class="row row-cols-1 row-cols-md-4 g-3">
            @foreach ($images as $img)
                <div class="col">
                    <div class="card h-100">
                        <img src="{{ asset('storage/' . $img) }}" class="card-img-top" style="object-fit: cover; height: 150px;">
                        <div class="card-body">
                          
                            <button class="btn btn-info btn-sm" onclick="copyToClipboard('{{ asset('storage/' . $img) }}')">Copy </button>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="images[]" value="{{ $img }}" id="img_{{ md5($img) }}">
                                <label class="form-check-label" for="img_{{ md5($img) }}">Delete</label>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="my-3">
            <button type="submit" class="btn btn-danger" id="bulkDeleteBtn">Delete Selected</button>
        </div>
    </form>
    @else 
    <center><h3>Folder is empty</h3>
    @endif

   
</div>
@endsection
@push('scripts')
<script>
        // Function to copy image path to clipboard
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Path copied!',
                    text: 'The public URL of the image has been copied to the clipboard.'
                });
            }).catch(function(err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to copy',
                    text: 'There was an issue copying the path.'
                });
            });
        }

        // Handle the bulk delete form submission
        $('#deleteForm').on('submit', function(e) {
            e.preventDefault();

            // Check if at least one image is selected
            if ($("input[name='images[]']:checked").length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No images selected!',
                    text: 'Please select at least one image to delete.'
                });
                return;
            }

            // Confirm before deleting
            Swal.fire({
                title: 'Are you sure?',
                text: 'This action will delete the selected images permanently!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete them',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

    </script>
@endpush