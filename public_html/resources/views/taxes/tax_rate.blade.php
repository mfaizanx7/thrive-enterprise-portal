@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Tax Rate') }}
@endsection
@push('script-page')
    <script src="{{ asset('js/jquery-searchbox.js') }}"></script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Taxes') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        {{-- @can('create constant tax')
            <a href="#" data-url="{{ route('taxes.create') }}" data-ajax-popup="true" data-title="{{__('Create Tax Rate')}}" data-bs-toggle="tooltip" title="{{__('Create')}}"  class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan --}}
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-3">
            @include('layouts.account_setup')
        </div>
        <div class="col-9">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th> {{ __('Product Services') }}</th>
                                    <th> {{ __('Tax Rate %') }}</th>
                                    {{-- <th> {{__('chart of Account')}}</th> --}}
                                    <th width="10%"> {{ __('Action') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($ps as $products)
                                    <tr class="font-style">
                                        <td>{{ $products->name }}</td>
                                        <td>{{ $products->tax_name }}</td>
                                        <td class="Action">
                                            <span>
                                                @can('edit constant tax')
                                                    <div class="action-btn bg-primary ms-2">
                                                        <a href="#"
                                                            class="editProductServiceBtn mx-3 btn btn-sm align-items-center"
                                                            data-id="{{ $products->id }}" data-tax="{{ $products->id }}"
                                                            title="{{ __('Edit') }}">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ajaxModal" tabindex="-1" aria-labelledby="ajaxModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ajaxModalLabel">Edit Product Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form Starts Here -->
                    {{ Form::open(['url' => '', 'method' => 'PUT', 'id' => 'modalForm']) }}
                    @csrf
                    <div class="mb-3 col-12 form-group">
                        {{ Form::label('name', 'Product Name', ['class' => 'form-label']) }}
                        {{ Form::text('name', null, ['class' => 'form-control', 'id' => 'textInput__01', 'required']) }}
                    </div>

                    <div class="row">
                        <div class="form-group col-6">
                            {{ Form::label('tax_name', __('Tax Rate Name'), ['class' => 'form-label']) }}
                            <select name="tax_id" class="form-control select select_123" id="tax">
                                <option value="" disabled selected>Select Tax</option>
                                @foreach ($taxes as $tax)
                                    <option value="{{ $tax->id }}" data-per="{{ $tax->rate }}">
                                        {{ $tax->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-6">
                            {{ Form::label('rate', __('Tax Rate %'), ['class' => 'form-label']) }}
                            {{ Form::number('rate', '', ['class' => 'form-control', 'readonly' => 'readonly', 'required' => 'required', 'id' => 'rate']) }}
                        </div>
                    </div>


                    <button type="submit" class="btn btn-primary">Save changes</button>
                    {{ Form::close() }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>




    {{-- <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="ajaxModalLabel">Edit Product Service</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <!-- Form Starts Here -->
              <form id="modalForm" method="POST" action="{{url('product-services/update/2')}}">
                @csrf
                @method('PUT')
      
                <!-- Text Input Field Pre-filled with Existing Data -->
                <div class="mb-3">
                  <label for="textInput__01" class="form-label">Product Name</label>
                  <input type="text" class="form-control" id="textInput__01" name="name" placeholder="Enter product name">
                </div>
      
                <!-- Select Input Pre-filled with Existing Tax ID -->
                <div class="mb-3">
                  <label for="selectInput__01" class="form-label">Select Tax</label>
                  <select class="form-select" id="selectInput__01" name="tax_id">
                    <option value="1">Option 1</option>
                    <option value="2">Option 2</option>
                    <option value="3">Option 3</option>
                  </select>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" form="modalForm">Save changes</button>
            </div>
          </div>
        </div> --}}
    {{-- </div> --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script><!-- jQuery -->
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script>
        $('#tax').on('change', function() {
            var tax_id = $(this).val();
            var selectedOption = $(this).find('option:selected');
            var taxRate = selectedOption.data('per');
            $('#rate').val(taxRate);
        });
    </script>
@endsection
