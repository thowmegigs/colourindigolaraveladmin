@extends('layouts.vendor.app')
@section('content')
<div class="container-xxl py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Help & Support</h3>
        <a href="{{ domain_route('tickets.index') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create Ticket
        </a>
    </div>

    <div class="row g-4 mb-5">
        <!-- WhatsApp Support -->
        <div class="col-md-3">
            <div class="card text-center shadow-sm border-0 h-100">
                <div class="card-body py-4">
                    <div class="mb-3 text-success" style="font-size: 3rem;">
                        <i class="bi bi-whatsapp"></i>
                    </div>
                    <h5 class="card-title">WhatsApp</h5>
                    <p class="card-text text-muted">Chat with us instantly on WhatsApp for quick assistance.</p>
                    <a href="https://wa.me/1234567890" target="_blank" class="btn btn-success w-100">
                        <i class="bi bi-whatsapp"></i> Start Chat
                    </a>
                </div>
            </div>
        </div>

        <!-- Email Support -->
        <div class="col-md-3">
            <div class="card text-center shadow-sm border-0 h-100">
                <div class="card-body py-4">
                    <div class="mb-3 text-primary" style="font-size: 3rem;">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    <h5 class="card-title">Email</h5>
                    <p class="card-text text-muted">Send us an email and we’ll respond within 24 hours.</p>
                    <a href="mailto:support@example.com" class="btn btn-primary w-100">
                        <i class="bi bi-envelope-fill"></i> Send Email
                    </a>
                </div>
            </div>
        </div>

        <!-- Phone Support -->
        <div class="col-md-3">
            <div class="card text-center shadow-sm border-0 h-100">
                <div class="card-body py-4">
                    <div class="mb-3 text-warning" style="font-size: 3rem;">
                        <i class="bi bi-telephone-fill"></i>
                    </div>
                    <h5 class="card-title">Phone Call</h5>
                    <p class="card-text text-muted">Call us directly for immediate support during business hours.</p>
                    <a href="tel:+11234567890" class="btn btn-warning text-white w-100">
                        <i class="bi bi-telephone-fill"></i> Call Now
                    </a>
                </div>
            </div>
        </div>

        <!-- Ticket Support -->
        <div class="col-md-3">
            <div class="card text-center shadow-sm border-0 h-100">
                <div class="card-body py-4">
                    <div class="mb-3 text-info" style="font-size: 3rem;">
                        <i class="bi bi-ticket-perforated"></i>
                    </div>
                    <h5 class="card-title">Support Tickets</h5>
                    <p class="card-text text-muted">Track your support tickets or create a new one for detailed help.</p>
                    <a href="{{ domain_route('tickets.index') }}" class="btn btn-info text-white w-100">
                        <i class="bi bi-list-task"></i> View Tickets
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Ticket Overview Section -->
   {{-- @if(isset($tickets) && count($tickets) > 0)
    <h4 class="mb-3">Recent Tickets</h4>
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->id }}</td>
                            <td>{{ $ticket->subject }}</td>
                            <td>
                                <span class="badge 
                                    @if($ticket->status == 'open') bg-success 
                                    @elseif($ticket->status == 'pending') bg-warning 
                                    @else bg-secondary @endif">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                            </td>
                            <td>{{ $ticket->updated_at->diffForHumans() }}</td>
                            <td>
                                <a href="{{ route('vendor.support.tickets.show', $ticket->id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif--}}

</div>
@endsection
