@extends('layouts.vendor.app')
@section('content')
<div class="container-xxl flex-grow-1">

    <div class="card">
        <div class="card-body">

            <div class="card">
                <div class="card-header font-bold py-2 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Bank List</h5>
                  
                     @if($banks->count()==0)
                    <a href="{{route('vendor.bank.add')}}" class="btn btn-danger ">
                        <i class="bi bi-plus-circle"></i> Add Bank
</a>
@endif
                </div>

                <div class="card-body py-0">
                      <x-alert/>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Account Name</th>
                                    <th>Account Number</th>
                                    <th>IFSC Code</th>
                                    <th>Holder Name</th>
                                    <th>Branch Name</th>
                                    <th class="text-center" style="width: 120px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($banks->count()>0)
                                @foreach($banks as $bank)
                                <tr>
                                    <td>{{ucwords($bank->bank_name)}}</td>
                                    <td>{{$bank->account_number}}</td>
                                    <td>{{$bank->ifsc_code}}</td>
                                    <td>{{$bank->account_holder}}</td>
                                    <td>{{$bank->branch_name}}</td>
                                    <td class="text-center">
                                        <a href="{{route('vendor.bank.edit',['id'=>$bank->id])}}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                       
                                    </td>
                                </tr>
                                @endforeach
                             
                              @else
                                    <tr >
                                        <td  class="text-center py-5"  colspan="8" align="center">
                                            <div class="d-flex flex-column align-items-center py-10">
                                                        <div class="mb-3">
                                                            <i class="bi bi-database-x fs-1 text-muted"></i>
                                                        </div>
                                                        <h5 class="text-muted mb-2">No bank found</h5>
                                                        <a href="{{ domain_route('bank.add') }}" class="btn btn-danger">
                                                            <i class="bi bi-plus-lg"></i> Add Bank
                                                        </a>
                                                    </div>
                                        </td>
                                    </tr>
                               @endif
                                {{-- More rows can go here --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
