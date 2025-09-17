@extends('layouts.admin.app')
@section('content')
<div class="container">
    <div class="row">
        <!-- Left Column: Push Notifications List -->
        <div class="col-md-6">
            <div class="card shadow p-3 mb-4">
    <h3 class="mb-4">Push Notification History</h3>
    @if(isset($notifications) && count($notifications) > 0)
        <ul class="list-group">
            @foreach($notifications as $notification)
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div>
                        <strong>{{ $notification->title }}</strong><br>
                        <small>{{ $notification->body }}</small>
                        @if($notification->image_url)
                            <div class="mt-2">
                                <img src="{{ asset($notification->image_url) }}" style="width:50px;height:50px;" alt="Notification Image">
                            </div>
                        @endif
                        <div class="text-muted mt-1">
                            Sent: {{ formateDate($notification->created_at) }}
                        </div>
                        <div class="text-muted mt-1">
                            Collection: {{ str_replace('-',' ',$notification->slug) }}
                        </div>
                    </div>

                    <!-- Delete Button -->
                    <form action="{{ route('push_notifications.destroy',['id'=>$notification->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this notification?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger ms-3">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
        {{ $notifications->links('pagination.default') }}
    @else
        <p class="text-muted">No notifications sent yet.</p>
    @endif
</div>

          
        </div>

        <!-- Right Column: Add New Notification -->
        <div class="col-md-6">
            <div class="card shadow p-3">
                <h3 class="mb-4">Send Push Notification</h3>
                
                <x-alert/>

                <form action="{{ route('send_pushnotification') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Notification Type -->
                   <input type="hidden" name="type" value="bulk"  required>
                           

                    <input type="hidden" name="fcm_token" class="form-control">

                    <!-- Title -->
                    <div class="mb-3">
                        <label class="form-label">Title*</label>
                        <input type="text" name="title" class="form-control" placeholder="Notification Title" required>
                    </div>

                    <!-- Body -->
                    <div class="mb-3">
                        <label class="form-label">Description*</label>
                        <textarea name="body" class="form-control" rows="2" placeholder="Notification Message" required></textarea>
                    </div>

                    <!-- Image -->
                    <div class="mb-3">
                        <label class="form-label">Upload Image (Optional) <small>Max Image Size (600px by 300px)</small> </label>
                        <input type="file" name="image_file" class="form-control" accept="image/*">
                         
                    </div>

                    <!-- Deep Link Data -->
                    <div class="mb-3">
                        <label class="form-label">Select Collection</label>
                        <select  type="text" name="slug" >
                            @foreach($collections as $col)
                            <option value="{{$col->slug}}">{{$col->name}}</option>
                            @endforeach
</select>
                    </div>
                    

                    <button type="submit" class="btn btn-primary w-100">Send Notification</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle FCM token input for single vs bulk
    document.querySelector('select[name="type"]').addEventListener('change', function () {
        document.getElementById('fcmTokenField').style.display = this.value === 'single' ? 'block' : 'none';
    });
</script>
@endsection
