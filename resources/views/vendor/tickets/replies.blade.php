@extends('layouts.vendor.app')

@section('content')
<div class="container-xxl flex-grow-1">
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">

            <!-- Ticket Header -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ticket #{{ $ticket->id }} - {{ $ticket->subject }}</h5>
                    <span class="badge bg-{{ $ticket->status == 'resolved' ? 'success' : 'info' }}">
                        {{ ucfirst($ticket->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <p>{{ $ticket->description }}</p>
                    <small class="text-muted">Created: {{ formateDate($ticket->created_at) }}</small>
                </div>
            </div>

            <!-- Conversation Thread -->
             @if($ticket->replies)
            <div class="card mb-3 shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">Conversation</h6>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @forelse($ticket->replies as $reply)
                        <div class="d-flex mb-3 {{ $reply->user_id == auth()->id() ? 'flex-row-reverse' : '' }}">
                            <div class="me-2">
                                <img src="{{ $reply->user->avatar ?? asset('default-avatar.png') }}"
                                     class="rounded-circle" width="40" height="40" alt="User">
                            </div>
                            <div class="p-3 rounded {{ $reply->user_id == auth()->id() ? 'bg-primary text-white' : 'bg-light' }}">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $reply->user->name ?? 'User' }}</strong>
                                    <small class="text-muted">{{ formateDate($reply->created_at) }}</small>
                                </div>
                                <div class="mt-1">{{ $reply->message }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">No replies yet.</p>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Reply Form -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">Add Reply</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('tickets.reply', $ticket->id) }}">
                        @csrf
                        <div class="mb-3">
                            <textarea name="message" class="form-control" rows="4" placeholder="Type your reply..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Reply</button>
                        <a href="{{ route('tickets.index') }}" class="btn btn-secondary ms-2">Back to Tickets</a>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
