@extends('layouts.vendor.app')
@section('content')
    <div class="container-xxl flex-grow-1 p-3">

        <div class="card">

            <div class="card-body">


               <x-alert/>

                <div class="card">
                    <div class="card-header font-bold py-2">
                        <h5> {{ $bankDetail && $bankDetail->exists ? 'Edit Bank Details' : 'Add Bank Details' }}</h5>
                    </div>
                    <div class="card-body py-0">
                        <form method="POST" action="{{$bankDetail && $bankDetail->exists?route('vendor.bank.edit',['id'=>$bankDetail->id]):route('vendor.bank.add')}}" >
                            @csrf
                            <div class="row g-3">
                                <!-- Account Holder Name -->
                                <div class="col-md-6">
                                    <label class="form-label">Account Holder Name</label>
                                    <input type="text" name="account_holder"
                                        placeholder="Enter account holder's full name"
                                        class="form-control @error('account_holder') is-invalid @enderror"
                                        value="{{ old('account_holder', $bankDetail->account_holder ?? '') }}" required>
                                    @error('account_holder')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Account Number -->
                                <div class="col-md-6">
                                    <label class="form-label">Account Number</label>
                                    <input type="number" name="account_number" placeholder="Enter account number"
                                        class="form-control @error('account_number') is-invalid @enderror"
                                        value="{{ old('account_number', $bankDetail->account_number ?? '') }}" required>
                                    @error('account_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- IFSC Code -->
                                <div class="col-md-4">
                                    <label class="form-label">IFSC Code</label>
                                    <input type="text" name="ifsc_code" placeholder="Enter IFSC code (e.g., SBIN0001234)"
                                        class="form-control @error('ifsc_code') is-invalid @enderror"
                                        value="{{ old('ifsc_code', $bankDetail->ifsc_code ?? '') }}" required>
                                    @error('ifsc_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Bank Name -->
                                <div class="col-md-4">
                                    <label class="form-label">Bank Name</label>
                                    <input type="text" name="bank_name"
                                        placeholder="Enter bank name (e.g., State Bank of India)"
                                        class="form-control @error('bank_name') is-invalid @enderror"
                                        value="{{ old('bank_name', $bankDetail->bank_name ?? '') }}" required>
                                    @error('bank_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Branch -->
                                <div class="col-md-4">
                                    <label class="form-label">Branch</label>
                                    <input type="text" name="branch_name" placeholder="Enter branch name (optional)"
                                        class="form-control @error('branch_name') is-invalid @enderror"
                                        value="{{ old('branch_name', $bankDetail->branch_name ?? '') }}">
                                    @error('branch_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <div class="col-md-12 d-flex align-items-end">
                                    <button type="submit" class="btn btn-danger ">
                                        {{ $bankDetail && $bankDetail->exists ? 'Update' : 'Save' }} Bank Details
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
