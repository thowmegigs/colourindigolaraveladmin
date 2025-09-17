@extends('layouts.admin.app')

@section('content')
    <div class="container-xxl flex-grow-1 py-4">
        <div class="row g-3">
            <!-- Left Column: Ticket Details -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header d-flex align-items-center py-3"
                        style="background-color: rgba(178,34,34,0.95); color: #fff;">
                        <!-- Back Button -->
                        <a href="{{ domain_route('tickets.index') }}" class="me-3 text-white text-decoration-none"
                            style="font-size: 1.2rem;">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h6 class="mb-0 fw-bold text-white">{{ $row->subject }}</h6>
                            <small class="text-light opacity-75">
                                Created {{ formateDate($row->created_at) }}
                            </small>
                        </div>
                    </div>

                    @if ($row->description)
                        <div class="card-body p-3">
                            <h6 class="text-muted mb-2 text-uppercase fw-semibold small">
                                <i class="fas fa-align-left me-1"></i>Description
                            </h6>
                            <div class="bg-light rounded-2 p-3 border-start border-3" style="border-color: #b22222;">
                                <p class="mb-0 text-dark lh-sm">{{ $row->description }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Chat & Reply -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header border-bottom py-3 d-flex align-items-center"
                        style="background-color: rgba(178,34,34,0.85); color: #fff;">
                        <i class="fas fa-comments me-2 text-white"></i>
                        <h6 class="mb-0 fw-bold text-white">Conversation</h6>
                        <small class="ms-auto text-white opacity-75">
                            {{ $row->replies->count() }} {{ Str::plural('reply', $row->replies->count()) }}
                        </small>
                    </div>

                    <div class="card-body p-3" style="background: #f8f9fa; max-height: 500px; overflow-y: auto;"
                        id="conversation-container">
                        @forelse($row->replies as $reply)
                            @php
                                $isAdmin = $reply->user_id == auth()->id();
                            @endphp

                            <div class="d-flex mb-3 {{ $isAdmin ? 'justify-content-start' : 'justify-content-end' }}">
                                @if ($isAdmin)
                                    <!-- Admin -->
                                    <div class="d-flex align-items-start">
                                        <div class="me-2 d-flex align-items-center justify-content-center"
                                            style="width: 36px; height: 36px;">
                                            <i class="mdi mdi-shield-account text-danger" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div class="p-3 rounded-3 shadow-sm"
                                            style="background-color: rgba(178,34,34,0.1); min-width: 250px; max-width: 75%;">
                                            <div class="d-flex justify-content-between mb-1">
                                                <strong class="text-dark">Admin</strong>
                                                <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div class="text-dark" style="font-size: 0.9rem;">
                                                {{ $reply->message }}
                                                @if (!empty($reply->attachment))
                                                    <div class="mt-2">
                                                        @php
                                                            $fileExtension = pathinfo(
                                                                $reply->attachment,
                                                                PATHINFO_EXTENSION,
                                                            );
                                                            $fileUrl = asset('storage/tickets/' . $reply->attachment);
                                                            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                                        @endphp

                                                        @if (in_array(strtolower($fileExtension), $imageExtensions))
                                                            <!-- Show image preview -->
                                                            <a href="{{ $fileUrl }}" target="_blank">
                                                                <img src="{{ $fileUrl }}" alt="Attachment"
                                                                    class="rounded shadow-sm mt-2"
                                                                    style="max-width: 150px; max-height: 150px; object-fit: cover;">
                                                            </a>
                                                        @else
                                                            <!-- Show as downloadable link -->
                                                            <a href="{{ $fileUrl }}" target="_blank"
                                                                class="d-inline-flex align-items-center gap-2 text-decoration-none mt-2">
                                                                <i class="mdi mdi-paperclip text-danger"></i>
                                                                <span
                                                                    class="text-primary small fw-semibold">{{ basename($reply->attachment) }}</span>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- User -->
                                    <div class="d-flex align-items-start">
                                        <div class="p-3 rounded-3 shadow-sm"
                                            style="background-color: rgba(0,0,0,0.05); min-width: 250px; max-width: 75%;">
                                            <div class="d-flex justify-content-between mb-1">
                                                <strong class="text-dark">User</strong>
                                                <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div class="text-dark" style="font-size: 0.9rem;">
                                                {{ $reply->message }}
                                                @if (!empty($reply->attachment))
                                                    <div class="mt-2">
                                                        @php
                                                            $fileExtension = pathinfo(
                                                                $reply->attachment,
                                                                PATHINFO_EXTENSION,
                                                            );
                                                            $fileUrl = asset('storage/tickets/' . $reply->attachment);
                                                            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                                        @endphp

                                                        @if (in_array(strtolower($fileExtension), $imageExtensions))
                                                            <!-- Show image preview -->
                                                            <a href="{{ $fileUrl }}" target="_blank">
                                                                <img src="{{ $fileUrl }}" alt="Attachment"
                                                                    class="rounded shadow-sm mt-2"
                                                                    style="max-width: 150px; max-height: 150px; object-fit: cover;">
                                                            </a>
                                                        @else
                                                            <!-- Show as downloadable link -->
                                                            <a href="{{ $fileUrl }}" target="_blank"
                                                                class="d-inline-flex align-items-center gap-2 text-decoration-none mt-2">
                                                                <i class="mdi mdi-paperclip text-danger"></i>
                                                                <span
                                                                    class="text-primary small fw-semibold">{{ basename($reply->attachment) }}</span>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ms-2 d-flex align-items-center justify-content-center"
                                            style="width: 36px; height: 36px;">
                                            <i class="mdi mdi-account text-secondary" style="font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center text-muted">
                                <i class="fas fa-comment-dots opacity-50" style="font-size: 2rem;"></i>
                                <h6 class="mt-2">No replies yet</h6>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Reply Form -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        @if ($row->status === 'Closed')
                            <div class="alert alert-secondary text-center mb-3" role="alert">
                                <i class="mdi mdi-lock me-2"></i> This ticket is closed. No further replies can be sent.
                            </div>
                            <form class="d-flex gap-2 align-items-center">
                                <textarea class="form-control" placeholder="Ticket is closed."
                                    style="resize: none; min-height: 45px; max-height: 120px; font-size: 0.9rem; flex-grow: 1;" disabled></textarea>
                                <button type="button" class="btn btn-secondary btn-sm" disabled>
                                    <i class="mdi mdi-send"></i>
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ domain_route('ticket_reply', $row->id) }}"
                                class="d-flex gap-2 align-items-center">
                                @csrf
                                <textarea name="message" id="message" class="form-control" placeholder="Type your reply..."
                                    style="resize: none; min-height: 45px; max-height: 120px; font-size: 0.9rem; flex-grow: 1;" required></textarea>
                                <button type="submit" class="btn btn-danger btn-sm"
                                    style="background-color: #b22222; border-color: #b22222;">
                                    <i class="mdi mdi-send"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const conversation = document.getElementById('conversation-container');
            if (conversation) {
                conversation.scrollTop = conversation.scrollHeight;
            }
        });
    </script>
@endsection
