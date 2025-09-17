@extends('layouts.admin.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card">
           
            <div class="card-body">
              

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="card">
                            <div class="card-header font-bold">
                           <h5> {{ $bankDetail && $bankDetail->exists ? 'Edit Bank Details' : 'Add Bank Details' }}</h5>
                            </div>
                            <div class="card-body">
                            <form method="POST" action="/bank">
                                @csrf

                                <div class="mb-3">
                                <label class="form-label">Account Holder Name</label>
                                <input type="text" name="account_holder" class="form-control @error('account_holder') is-invalid @enderror"
                                    value="{{ old('account_holder', $bankDetail->account_holder ?? '') }}" required>
                                @error('account_holder')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                <label class="form-label">Account Number</label>
                                <input type="number" name="account_number" class="form-control @error('account_number') is-invalid @enderror"
                                    value="{{ old('account_number', $bankDetail->account_number ?? '') }}" required>
                                @error('account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                <label class="form-label">IFSC Code</label>
                                <input type="text" name="ifsc_code" class="form-control @error('ifsc_code') is-invalid @enderror"
                                    value="{{ old('ifsc_code', $bankDetail->ifsc_code ?? '') }}" required>
                                @error('ifsc_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                <label class="form-label">Bank Name</label>
                                <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror"
                                    value="{{ old('bank_name', $bankDetail->bank_name ?? '') }}" required>
                                @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                <label class="form-label">Branch</label>
                                <input type="text" name="branch_name" class="form-control @error('branch') is-invalid @enderror"
                                    value="{{ old('branch_name', $bankDetail->branch_name ?? '') }}">
                                @error('branch_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                {{ $bankDetail && $bankDetail->exists ? 'Update' : 'Save' }} Bank Details
                                </button>

                            </form>
                            </div>
                        </div>
               ..
            </div>
        </div>
    </div>
@endsection
