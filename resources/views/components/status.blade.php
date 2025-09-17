@props(['status'])

@php
    $statusClass = '';
    $borderColor = '';
    $textColor = '';

    if (in_array($status, ['Approved', 'Yes', 'Active', 'Paid','Open'])) {
        $statusClass = 'border-success';
        $textColor = 'text-success';
    } elseif (in_array($status, ['Cancelled', 'Rejected', 'No', 'In-Active', 'Closed'])) {
        $statusClass = 'border-danger';
        $textColor = 'text-danger';
    } elseif (in_array($status, ['Pending', 'Waiting', 'Under Review'])) {
        $statusClass = 'border-warning';
        $textColor = 'text-warning';
    }
@endphp

<div class="badge bg-transparent {{ $statusClass }} {{ $textColor }} px-3 py-1 border rounded-pill"
     style="border-width: 2px; font-weight: 500;">
    {{ $status }}
</div>
