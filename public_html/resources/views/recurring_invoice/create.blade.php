@extends('layouts.admin')
@section('page-title')
    {{__('Create Recurring Invoice Template')}}
@endsection
@push('css-page')
    <style>
        .wizard-step { display: none; }
        .wizard-step.active { display: block; }
        .wizard-nav .nav-link { pointer-events: none; color: #ccc; }
        .wizard-nav .nav-link.active { color: #51459d; font-weight: bold; border-bottom: 2px solid #51459d; }
        .wizard-nav .nav-link.completed { color: #28a745; cursor: pointer; pointer-events: auto; }
    </style>
@endpush
@push('script-page')
    <script src="{{asset('js/jquery-ui.min.js')}}"></script>
    <script src="{{asset('js/jquery.repeater.min.js')}}"></script>
    <script>
        $(document).ready(function() {
            var currentStep = 1;
            
            function showStep(step) {
                $('.wizard-step').removeClass('active');
                $('#step-' + step).addClass('active');
                $('.wizard-nav .nav-link').removeClass('active');
                $('#nav-step-' + step).addClass('active');
            }

            $('.next-step').click(function() {
                if (validateStep(currentStep)) {
                    $('#nav-step-' + currentStep).addClass('completed');
                    currentStep++;
                    showStep(currentStep);
                    if(currentStep == 3) updateSummary();
                }
            });

            $('.prev-step').click(function() {
                currentStep--;
                showStep(currentStep);
            });

            $('.wizard-nav .nav-link').click(function() {
                if ($(this).hasClass('completed') || $(this).hasClass('active')) {
                    var step = $(this).data('step');
                    currentStep = step;
                    showStep(step);
                }
            });

            $(document).on('change', '#customer_id', function() {
                var customer_id = $(this).val();
                $.ajax({
                url: "{{ route('recurring-invoices.contracts') }}",
                    type: 'POST',
                    data: {
                        "id": customer_id,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data) {
                        $('#contract_id').empty();
                        $('#contract_id').append('<option value="">{{__("Select Contract")}}</option>');
                        $.each(data.data, function(key, value) {
                            $('#contract_id').append('<option value="' + value.id + '">' + value.subject + '</option>');
                        });
                    }
                });
            });

            $(document).on('change', '#contract_id', function() {
                var contract_id = $(this).val();
                if (contract_id) {
                    $.ajax({
                        url: "{{ route('recurring-invoices.contract.details') }}",
                        type: 'POST',
                        data: {
                            "id": contract_id,
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function(data) {
                            if (data.space_id) {
                                $('#space_id').val(data.space_id).trigger('change');
                            }
                            
                            if (data.html || data.html2 || data.html3) {
                                // Clear existing items
                                $('[data-repeater-item]').remove();
                                
                                var items_container = $('[data-repeater-list="items"]');
                                
                                function appendItem(html, index) {
                                    if (!html) return index;
                                    var processedHtml = html.replace(/__REPLACE_INDEX__/g, index);
                                    items_container.append(processedHtml);
                                    return index + 1;
                                }

                                var nextIndex = 0;
                                nextIndex = appendItem(data.html, nextIndex);
                                nextIndex = appendItem(data.html2, nextIndex);
                                nextIndex = appendItem(data.html3, nextIndex);
                                
                                calculateTotals();
                            }
                        }
                    });
                }
            });

            function validateStep(step) {
                var isValid = true;
                $('#step-' + step + ' [required]').each(function() {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                return isValid;
            }

            function updateSummary() {
                $('#summary-name').text($('#name').val());
                $('#summary-customer').text($('#customer_id option:selected').text());
                $('#summary-cycle').text($('#cycle option:selected').text());
                $('#summary-start').text($('#start_date').val());
                
                var total = $('.totalAmount').text();
                $('#summary-total').text(total);
            }

            // Repeater & Calculation Logic (Adapted from Invoice Create)
            var $repeater = $('.repeater').repeater({
                initEmpty: false,
                show: function () {
                    $(this).slideDown();
                    calculateTotals();
                },
                hide: function (deleteElement) {
                    if (confirm('Are you sure you want to delete this element?')) {
                        $(this).slideUp(deleteElement);
                        setTimeout(calculateTotals, 500);
                    }
                },
                isFirstItemUndeletable: true
            });

            $(document).on('keyup change', '.quantity, .price, .discount, .tax-select', function () {
                calculateTotals();
            });

            function calculateTotals() {
                var subTotal = 0;
                var totalDiscount = 0;
                var totalTax = 0;

                $('.repeater [data-repeater-item]').each(function() {
                    var qty = parseFloat($(this).find('.quantity').val()) || 0;
                    var price = parseFloat($(this).find('.price').val()) || 0;
                    var discount = parseFloat($(this).find('.discount').val()) || 0;
                    var taxRate = 0;
                    
                    $(this).find('.tax-select option:selected').each(function() {
                        taxRate += parseFloat($(this).data('rate')) || 0;
                    });

                    var amount = (qty * price) - discount;
                    var tax = amount * (taxRate / 100);
                    
                    $(this).find('.amount').text((amount + tax).toFixed(2));
                    
                    subTotal += (qty * price);
                    totalDiscount += discount;
                    totalTax += tax;
                });

                $('.subTotal').text(subTotal.toFixed(2));
                $('.totalDiscount').text(totalDiscount.toFixed(2));
                $('.totalTax').text(totalTax.toFixed(2));
                $('.totalAmount').text((subTotal - totalDiscount + totalTax).toFixed(2));
            }
        });
    </script>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('recurring-invoices.index')}}">{{__('Recurring Invoices')}}</a></li>
    <li class="breadcrumb-item">{{__('Create Template')}}</li>
@endsection

@section('content')
    <div class="row">
        {{ Form::open(['route' => 'recurring-invoices.store', 'class' => 'w-100']) }}
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs wizard-nav mb-4">
                        <li class="nav-item">
                            <a class="nav-link active" id="nav-step-1" data-step="1" href="#">1. {{__('General Info')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="nav-step-2" data-step="2" href="#">2. {{__('Line Items')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="nav-step-3" data-step="3" href="#">3. {{__('Review & Save')}}</a>
                        </li>
                    </ul>

                    <!-- Step 1: General Info -->
                    <div class="wizard-step active" id="step-1">
                        <div class="row">
                            <div class="form-group col-md-6">
                                {{ Form::label('name', __('Template Name'), ['class' => 'form-label']) }}
                                {{ Form::text('name', '', ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('e.g. Monthly Rent Template')]) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('customer_id', __('Customer'), ['class' => 'form-label']) }}
                                {{ Form::select('customer_id', $customers, null, ['class' => 'form-control select', 'required' => 'required', 'id' => 'customer_id']) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('contract_id', __('Contract'), ['class' => 'form-label']) }}
                                {{ Form::select('contract_id', ['' => __('Select Contract')], null, ['class' => 'form-control select', 'id' => 'contract_id']) }}
                            </div>
                            <div class="form-group col-md-3">
                                {{ Form::label('cycle', __('Billing Cycle'), ['class' => 'form-label']) }}
                                {{ Form::select('cycle', $cycles, null, ['class' => 'form-control select', 'required' => 'required']) }}
                            </div>
                            <div class="form-group col-md-3">
                                {{ Form::label('invoice_day', __('Day of Month to Generate'), ['class' => 'form-label']) }}
                                {{ Form::number('invoice_day', 1, ['class' => 'form-control', 'required' => 'required', 'min' => 1, 'max' => 31]) }}
                            </div>
                            <div class="form-group col-md-4">
                                {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
                                {{ Form::date('start_date', date('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) }}
                            </div>
                            <div class="form-group col-md-4">
                                {{ Form::label('end_date', __('End Date (Optional)'), ['class' => 'form-label']) }}
                                {{ Form::date('end_date', null, ['class' => 'form-control']) }}
                            </div>
                            <div class="form-group col-md-4">
                                {{ Form::label('space_id', __('Related Space (Optional)'), ['class' => 'form-label']) }}
                                {{ Form::select('space_id', $spaces, null, ['class' => 'form-control select', 'id' => 'space_id']) }}
                            </div>
                            {{-- <div class="form-group col-md-4 mt-4">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" name="auto_send" id="auto_send" checked>
                                    <label class="form-check-label" for="auto_send">{{__('Auto-send Invoice via Email')}}</label>
                                </div>
                            </div> --}}
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-primary next-step">{{__('Next: Add Items')}}</button>
                        </div>
                    </div>

                    <!-- Step 2: Items -->
                    <div class="wizard-step" id="step-2">
                        <div class="repeater">
                            <div class="table-responsive">
                                <table class="table" data-repeater-list="items">
                                    <thead>
                                        <tr>
                                            <th>{{__('Description')}}</th>
                                            <th width="100px">{{__('Qty')}}</th>
                                            <th width="150px">{{__('Unit Price')}}</th>
                                            <th>{{__('Tax')}}</th>
                                            <th width="120px">{{__('Discount')}}</th>
                                            <th class="text-end">{{__('Amount')}}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody data-repeater-item>
                                        <tr>
                                            <td>{{ Form::text('description', '', ['class' => 'form-control', 'required' => 'required']) }}</td>
                                            <td>{{ Form::number('quantity', 1, ['class' => 'form-control quantity', 'required' => 'required', 'min' => 1]) }}</td>
                                            <td>{{ Form::number('price', 0, ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01']) }}</td>
                                            <td>
                                                <select name="tax" class="form-control select tax-select">
                                                    @foreach($taxes as $taxId => $taxName)
                                                        @php $tax = \App\Models\Tax::find($taxId); @endphp
                                                        <option value="{{$taxId}}" data-rate="{{$tax->rate}}">{{$taxName}} ({{$tax->rate}}%)</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>{{ Form::number('discount', 0, ['class' => 'form-control discount', 'step' => '0.01']) }}</td>
                                            <td class="text-end amount">0.00</td>
                                            <td><a href="#" class="ti ti-trash text-danger" data-repeater-delete></a></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4">
                                                <a href="#" data-repeater-create class="btn btn-sm btn-primary mt-2">
                                                    <i class="ti ti-plus"></i> {{__('Add Another Item')}}
                                                </a>
                                            </td>
                                            <td colspan="3" class="text-end">
                                                <div class="mb-2"><strong>{{__('Sub Total')}}: </strong> <span class="subTotal">0.00</span></div>
                                                <div class="mb-2 text-danger"><strong>{{__('Discount')}}: </strong> <span class="totalDiscount">0.00</span></div>
                                                <div class="mb-2"><strong>{{__('Tax')}}: </strong> <span class="totalTax">0.00</span></div>
                                                <div class="h5"><strong>{{__('Total Amount')}}: </strong> <span class="totalAmount">0.00</span></div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="mt-4 d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary prev-step">{{__('Previous')}}</button>
                            <button type="button" class="btn btn-primary next-step">{{__('Next: Review')}}</button>
                        </div>
                    </div>

                    <!-- Step 3: Review -->
                    <div class="wizard-step" id="step-3">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>{{__('Template Details')}}</h5>
                                <table class="table table-bordered">
                                    <tr><td><strong>{{__('Name')}}:</strong></td> <td id="summary-name">-</td></tr>
                                    <tr><td><strong>{{__('Customer')}}:</strong></td> <td id="summary-customer">-</td></tr>
                                    <tr><td><strong>{{__('Cycle')}}:</strong></td> <td id="summary-cycle">-</td></tr>
                                    <tr><td><strong>{{__('First Invoice Date')}}:</strong></td> <td id="summary-start">-</td></tr>
                                    <tr><td><strong>{{__('Estimated Total')}}:</strong></td> <td id="summary-total" class="h5 text-primary">-</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                {{ Form::label('notes', __('Additional Notes / Internal Use'), ['class' => 'form-label']) }}
                                {{ Form::textarea('notes', '', ['class' => 'form-control', 'rows' => 4]) }}
                            </div>
                        </div>
                        <div class="mt-4 d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary prev-step">{{__('Previous')}}</button>
                            <button type="submit" class="btn btn-success">{{__('Save & Activate Template')}}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
@endsection
