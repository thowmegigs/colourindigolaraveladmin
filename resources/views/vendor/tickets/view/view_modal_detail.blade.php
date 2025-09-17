@extends('layouts.vendor.app')

@section('content')
    <div class="container-xxl flex-grow-1 py-3">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 d-flex flex-column" style="height: 80vh;">

                    <!-- Header -->
                    <div class="card-header border-bottom py-3 d-flex align-items-center"
                        style="background-color: rgba(139, 0, 0, 0.85) !important; color: #fff;">

                        <!-- Back Button -->
                        <a href="{{ domain_route('tickets.index') }}"
                            class="me-3 d-flex align-items-center text-white text-decoration-none" style="font-size: 1.2rem;">
                            <i class="bi bi-arrow-left"></i>
                        </a>

                        <!-- Avatar -->
                        <div class="me-3">
                            <div class="avatar rounded-circle bg-white bg-opacity-10 border d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;">
                                <i class="bi bi-headset text-white" style="font-size: 1.2rem;"></i>
                            </div>
                        </div>

                        <!-- Title & Subtitle -->
                        <div>
                            <h6 class="mb-0 fw-semibold text-white">Zuno</h6>
                            <small class="text-light opacity-75">Customer Support</small>
                        </div>

                        <!-- Reply Count -->
                        <div class="ms-auto">
                            <small class="text-light opacity-75">
                                {{ $row->replies->count() ?? 0 }} {{ Str::plural('reply', $row->replies->count() ?? 0) }}
                            </small>
                        </div>
                    </div>

                    <!-- Chat Messages -->
                    <div class="flex-grow-1 overflow-auto p-3" id="chat-container" style="background: #f8f9fa;">
                        @if ($row->replies && $row->replies->count() > 0)
                            @foreach ($row->replies as $reply)
                                @php $isVendor = $reply->user_id == auth()->guard('vendor')->id(); @endphp
                                <div class="d-flex mb-3 {{ $isVendor ? 'justify-content-end' : 'justify-content-start' }}">
                                    @if (!$isVendor)
                                        <!-- Zuno Support Message -->
                                        <div class="d-flex">
                                            <div class="me-2">
                                                <div class="avatar rounded-circle bg-light border d-flex align-items-center justify-content-center"
                                                    style="width: 36px; height: 36px;">
                                                    <i class="bi bi-headset text-secondary"></i>
                                                </div>
                                            </div>
                                            <div class="p-3 rounded-3 shadow-sm"
                                                style="background-color: rgba(139, 0, 0, 0.05); min-width: 250px; max-width: 75%;">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <strong class="text-dark">Zuno </strong>
                                                    <small
                                                        class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
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
                                                                $fileUrl = asset(
                                                                    'storage/tickets/' . $reply->attachment,
                                                                );
                                                                $imageExtensions = [
                                                                    'jpg',
                                                                    'jpeg',
                                                                    'png',
                                                                    'gif',
                                                                    'webp',
                                                                ];
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
                                        <!-- Vendor (You) Message -->
                                        <div class="d-flex">
                                            <div class="p-3 rounded-3 shadow-sm"
                                                style="background-color: rgba(139, 0, 0, 0.08); min-width: 250px; max-width: 75%;">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <strong class="text-dark">You</strong>
                                                    <small
                                                        class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
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
                                                                $fileUrl = asset(
                                                                    'storage/tickets/' . $reply->attachment,
                                                                );
                                                                $imageExtensions = [
                                                                    'jpg',
                                                                    'jpeg',
                                                                    'png',
                                                                    'gif',
                                                                    'webp',
                                                                ];
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
                                            <div class="ms-2">
                                                <div class="avatar rounded-circle bg-light border d-flex align-items-center justify-content-center"
                                                    style="width: 36px; height: 36px;">
                                                    <i class="bi bi-person-circle text-secondary"></i>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <!-- Empty State -->
                            <div class="text-center text-muted">
                                <i class="bi bi-chat-dots opacity-50" style="font-size: 2rem;"></i>
                                <h6 class="mt-2">No replies yet</h6>
                                <p style="font-size: 0.9rem;">Start the conversation below.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Input Area -->
                    <div class="border-top p-3 bg-white">
                        @if ($row->status === 'Closed')
                            <div class="alert alert-secondary text-center mb-3" role="alert">
                                <i class="bi bi-lock me-2"></i> This ticket is closed. You cannot send new messages.
                            </div>
                            <form class="d-flex gap-2 align-items-center">
                                <textarea class="form-control" placeholder="Ticket is closed."
                                    style="resize: none; min-height: 45px; max-height: 120px; font-size: 0.9rem; flex-grow: 1;" disabled></textarea>
                                <button type="button" class="btn btn-secondary btn-sm" disabled>
                                    <i class="bi bi-send"></i>
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ domain_route('ticket_reply', $row->id) }}"
                                class="d-flex gap-2 align-items-center" enctype="multipart/form-data">
                                @csrf
                                <label for="attachment"
                                    class="btn btn-light btn-sm d-flex align-items-center justify-content-center"
                                    style="width: 38px; height: 38px; border: 1px solid #ccc; cursor: pointer;">
                                    <i class="bi bi-paperclip"></i>
                                </label>
                                <input type="file" name="attachment" id="attachment" class="d-none"
                                    accept=".jpg,.png,.pdf,.doc,.docx,.xls,.xlsx">

                                <textarea name="message" id="message" class="form-control" placeholder="Type a message..."
                                    style="resize: none; min-height: 45px; max-height: 120px; font-size: 0.9rem; flex-grow: 1;" required></textarea>

                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-send"></i>
                                </button>
                            </form>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Auto-scroll to bottom -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const chatContainer = document.getElementById('chat-container');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        });
    </script>
@endsection
