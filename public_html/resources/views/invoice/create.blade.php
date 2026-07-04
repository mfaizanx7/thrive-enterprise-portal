@extends('layouts.admin')
@section('page-title')
    {{__('Invoice Create')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('invoice.index')}}">{{__('Invoice')}}</a></li>
    <li class="breadcrumb-item">{{__('Invoice Create')}}</li>
@endsection
@push('script-page')
    <script src="{{asset('js/jquery-ui.min.js')}}"></script>
    <script src="{{asset('js/jquery.repeater.min.js')}}"></script>
    <script>
        var selector = "body";
        if ($(selector + " .repeater").length) {
            var $dragAndDrop = $("body .repeater tbody").sortable({
                handle: '.sort-handler'
            });
            var $repeater = $(selector + ' .repeater').repeater({
                initEmpty: false,
                defaultValues: {
                    'status': 1
                },
                show: function () {
                    $(this).slideDown();
                    var file_uploads = $(this).find('input.multi');
                    if (file_uploads.length) {
                        $(this).find('input.multi').MultiFile({
                            max: 3,
                            accept: 'png|jpg|jpeg',
                            max_size: 2048
                        });
                    }
                    // if($('.select2').length) {
                    //     $('.select2').select2();
                    // }

                },
                hide: function (deleteElement) {
                    if (confirm('Are you sure you want to delete this element?')) {
                        $(this).slideUp(deleteElement);
                        $(this).remove();

                        var inputs = $(".amount");
                        var subTotal = 0;
                        for (var i = 0; i < inputs.length; i++) {
                            subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                        }
                        $('.subTotal').html(subTotal.toFixed(2));
                        $('.totalAmount').html(subTotal.toFixed(2));
                    }
                },
                ready: function (setIndexes) {

                    $dragAndDrop.on('drop', setIndexes);
                },
                isFirstItemUndeletable: true
            });
            var value = $(selector + " .repeater").attr('data-value');
            if (typeof value != 'undefined' && value.length != 0) {
                value = JSON.parse(value);
                $repeater.setList(value);
            }

        }

        $(document).on('change', '#customer', function () {
            $('#customer_detail').removeClass('d-none');
            $('#customer_detail').addClass('d-block');
            $('#customer-box').removeClass('d-block');
            $('#customer-box').addClass('d-none');
            var id = $(this).val();
            var url = $(this).data('url');
            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'id': id
                },
                cache: false,
                success: function (data) {
                    if (data != '') {
                        $('#customer_detail').html(data);
                    } else {
                        $('#customer-box').removeClass('d-none');
                        $('#customer-box').addClass('d-block');
                        $('#customer_detail').removeClass('d-block');
                        $('#customer_detail').addClass('d-none');
                    }

                },

            });
        });

        $(document).on('click', '#remove', function () {
            $('#customer-box').removeClass('d-none');
            $('#customer-box').addClass('d-block');
            $('#customer_detail').removeClass('d-block');
            $('#customer_detail').addClass('d-none');
        })

        $(document).on('change', '.item', function () {

            var iteams_id = $(this).val();
            var url = $(this).data('url');
            var el = $(this);

            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'product_id': iteams_id
                },
                cache: false,
                success: function (data) {
                    var item = JSON.parse(data);
                    console.log(el.parent().parent().find('.quantity'))
                    $(el.parent().parent().find('.quantity')).val(1);
                    $(el.parent().parent().find('.price')).val(item.product.sale_price);
                    $(el.parent().parent().parent().find('.pro_description')).val(item.product.description);
                    // $('.pro_description').text(item.product.description);

                    var taxes = '';
                    var tax = [];

                    var totalItemTaxRate = 0;

                    if (item.taxes == 0) {
                        taxes += '-';
                    } else {
                        for (var i = 0; i < item.taxes.length; i++) {
                            taxes += '<span class="badge bg-primary mt-1 mr-2">' + item.taxes[i].name + ' ' + '(' + item.taxes[i].rate + '%)' + '</span>';
                            tax.push(item.taxes[i].id);
                            totalItemTaxRate += parseFloat(item.taxes[i].rate);
                        }
                    }
                    var itemTaxPrice = parseFloat((totalItemTaxRate / 100)) * parseFloat((item.product.sale_price * 1));
                    $(el.parent().parent().find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));
                    $(el.parent().parent().find('.itemTaxRate')).val(totalItemTaxRate.toFixed(2));
                    $(el.parent().parent().find('.taxes')).html(taxes);
                    $(el.parent().parent().find('.tax')).val(tax);
                    $(el.parent().parent().find('.unit')).html(item.unit);
                    $(el.parent().parent().find('.discount')).val(0);

                    var inputs = $(".amount");
                    var subTotal = 0;
                    for (var i = 0; i < inputs.length; i++) {
                        subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                    }

                    var totalItemPrice = 0;
                    var priceInput = $('.price');
                    for (var j = 0; j < priceInput.length; j++) {
                        totalItemPrice += parseFloat(priceInput[j].value);
                    }

                    var totalItemTaxPrice = 0;
                    var itemTaxPriceInput = $('.itemTaxPrice');
                    for (var j = 0; j < itemTaxPriceInput.length; j++) {
                        totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
                        $(el.parent().parent().find('.amount')).html(parseFloat(item.totalAmount)+parseFloat(itemTaxPriceInput[j].value));
                    }

                    var totalItemDiscountPrice = 0;
                    var itemDiscountPriceInput = $('.discount');

                    for (var k = 0; k < itemDiscountPriceInput.length; k++) {

                        totalItemDiscountPrice += parseFloat(itemDiscountPriceInput[k].value);
                    }

                    $('.subTotal').html(totalItemPrice.toFixed(2));
                    $('.totalTax').html(totalItemTaxPrice.toFixed(2));
                    $('.totalAmount').html((parseFloat(totalItemPrice) - parseFloat(totalItemDiscountPrice) + parseFloat(totalItemTaxPrice)).toFixed(2));


                },
            });
        });

        $(document).on('keyup', '.quantity', function () {
            var quntityTotalTaxPrice = 0;

            var el = $(this).parent().parent().parent().parent();

            var quantity = $(this).val();
            var price = $(el.find('.price')).val();
            var discount = $(el.find('.discount')).val();
            if(discount.length <= 0)
            {
                discount = 0 ;
            }

            var totalItemPrice = (quantity * price) - discount;

            var amount = (totalItemPrice);


            var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
            $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

            $(el.find('.amount')).html(parseFloat(itemTaxPrice)+parseFloat(amount));

            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
            }


            var totalItemPrice = 0;
            var inputs_quantity = $(".quantity");

            var priceInput = $('.price');
            for (var j = 0; j < priceInput.length; j++) {
                totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
            }

            var inputs = $(".amount");

            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
            }

            $('.subTotal').html(totalItemPrice.toFixed(2));
            $('.totalTax').html(totalItemTaxPrice.toFixed(2));

            $('.totalAmount').html((parseFloat(subTotal)).toFixed(2));

        })

        $(document).on('keyup change', '.price', function () {
            var el = $(this).parent().parent().parent().parent();
            var price = $(this).val();
            var quantity = $(el.find('.quantity')).val();

            var discount = $(el.find('.discount')).val();
            if(discount.length <= 0)
            {
                discount = 0 ;
            }
            var totalItemPrice = (quantity * price)-discount;

            var amount = (totalItemPrice);


            var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
            $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

            $(el.find('.amount')).html(parseFloat(itemTaxPrice)+parseFloat(amount));

            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
            }


            var totalItemPrice = 0;
            var inputs_quantity = $(".quantity");

            var priceInput = $('.price');
            for (var j = 0; j < priceInput.length; j++) {
                totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
            }

            var inputs = $(".amount");

            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
            }

            $('.subTotal').html(totalItemPrice.toFixed(2));
            $('.totalTax').html(totalItemTaxPrice.toFixed(2));

            $('.totalAmount').html((parseFloat(subTotal)).toFixed(2));


        })

        $(document).on('keyup change', '.discount', function () {
            var el = $(this).parent().parent().parent();
            var discount = $(this).val();
            if(discount.length <= 0)
            {
                discount = 0 ;
            }

            var price = $(el.find('.price')).val();
            var quantity = $(el.find('.quantity')).val();
            var totalItemPrice = (quantity * price) - discount;


            var amount = (totalItemPrice);


            var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
            $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

            $(el.find('.amount')).html(parseFloat(itemTaxPrice)+parseFloat(amount));

            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
            }


            var totalItemPrice = 0;
            var inputs_quantity = $(".quantity");

            var priceInput = $('.price');
            for (var j = 0; j < priceInput.length; j++) {
                totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
            }

            var inputs = $(".amount");

            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
            }


            var totalItemDiscountPrice = 0;
            var itemDiscountPriceInput = $('.discount');

            for (var k = 0; k < itemDiscountPriceInput.length; k++) {

                totalItemDiscountPrice += parseFloat(itemDiscountPriceInput[k].value);
            }


            $('.subTotal').html(totalItemPrice.toFixed(2));
            $('.totalTax').html(totalItemTaxPrice.toFixed(2));

            $('.totalAmount').html((parseFloat(subTotal)).toFixed(2));
            $('.totalDiscount').html(totalItemDiscountPrice.toFixed(2));




        })

        var customerId = '{{$customerId}}';
        if (customerId > 0) {
            $('#customer').val(customerId).change();
        }

    </script>
    <script>
        $(document).on('click', '[data-repeater-delete]', function () {
            $(".price").change();
            $(".discount").change();
        });

        $('#company').on('change', function () {
            var comp_id = $('#company').val();
            $.ajax({
                url: "{{ route('company_contract') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'id': comp_id
                },
                cache: false,
                success: function (data) {
                    console.log(data);
                    if (data != '') {
                        // $('#sortable-table tbody:gt(0)').remove();
                        $('tbody').remove();
                    $('.btn-primary[data-repeater-create]').click();
                        // $('#company_detail').removeClass('d-block');
                        $('#contract').empty().append(`<option selected disabled value="">Select a Contract</option>`);
                        for(var i=0; i<data.data.length; i++){
                            $('#contract').append(`<option value="` + data.data[i]['id'] + `">` + data.data[i]['subject'] +`</option>`);
                        }
                    }

                },

            });

        });

        $('#contract').on('change', function () {
            var comp_id = $('#contract').val();
            $.ajax({
                url: "{{ route('company_contract_detail') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'id': comp_id
                },
                cache: false,
                success: function (data) {
                    $('#sortable-table tbody:gt(0)').remove();
                    $('.btn-primary[data-repeater-create]').click();

                    if (data.html !== undefined) {
                        $('#sortable-table tbody:gt(0)').remove();
                        $('.ui-sortable').empty().html(data.html);
                        $('.btn-primary[data-repeater-create]').click();
                        if(data.html2 !== undefined || data.html2 !== ''){
                            $('#sortable-table tbody:eq(1)').empty().html(data.html2);
                            $('.btn-primary[data-repeater-create]').click();
                            // $('#sortable-table tbody:gt(1)').remove();
                        }
                        if(data.html3 !== undefined || data.html3 !== ''){
                            $('#sortable-table tbody:eq(2)').empty().html(data.html3);
                            $('.btn-primary[data-repeater-create]').click();
                            $('#sortable-table tbody:gt(2)').remove();
                        }else{
                            $('#sortable-table tbody:gt(1)').remove();
                        }
                    }
                    $('.quantity').trigger('keyup');
                },

            });

        });

        $('#addpropcheck').on('change', function() {
        if ($(this).is(":checked")) {
            // $('#companySelect').css('display', 'none');
            $('.new_customer').addClass('d-none');
            $('.companyText').removeClass('d-none');
            // $('#companySelect').prop('required', false);
            $('.req').prop('required', true);
            $('.req_old').prop('required', false);
        } else {
            $('.new_customer').removeClass('d-none');
            $('.companyText').addClass('d-none');
            $('.req').prop('required', false);
            $('.req_old').prop('required', true);

        }

        $('form').on('submit', function(event) {
            event.preventDefault();
            var startDate = new Date($('#issue_date').val());
            var endDate = new Date($('#due_date').val());
            var submitButton = document.getElementById('myButton');
            submitButton.disabled = true;

            if (endDate < startDate) {
                event.preventDefault();
                show_toastr('error', 'Due date must be greater than the Issue date', 'error');
                submitButton.disabled = false;
                return;
            }
            setTimeout(function() {
                        submitButton.disabled = false;
            }, 5000);
        });

    });
    $(document).ready(function() {
        $('form').on('submit', function(event) {
            var startDate = new Date($('#issue_date').val());
            var endDate = new Date($('#due_date').val());
            var submitButton = document.getElementById('myButton');
            submitButton.disabled = true;

            if (endDate < startDate) {
                event.preventDefault();
                show_toastr('error', 'Due date must be greater than the Issue date', 'error');
                submitButton.disabled = false;
                return;
            }
            setTimeout(function() {
                        submitButton.disabled = false;
            }, 5000);
        });
    });

    </script>
@endpush
@section('content')
    <div class="row">
        {{ Form::open(array('url' => 'invoice','class'=>'w-100',  )) }}
        <div class="col-12">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        {{-- <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group" id="customer-box">
                                {{ Form::label('customer_id', __('Customer'),['class'=>'form-label']) }}
                                {{ Form::select('customer_id', $customers,$customerId, array('class' => 'form-control select','id'=>'customer','data-url'=>route('invoice.customer'),'required'=>'required')) }}

                            </div>

                            <div id="customer_detail" class="d-none">
                            </div>
                        </div> --}}

                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">

                            <div class="form-group col-md-12 row">
                                <div class="col-md-10">
                                    {{ Form::label('customer_id', __('Customer'),['class'=>'form-label']) }}<span style="color: red"> *</span>
                                </div>
                                <div class="col-md-2 d-flex gap-2">
                                    {{ Form::label('addpropcheck', __('New'), ['class' => 'form-check-label']) }}
                                    {{ Form::checkbox('new', '1', false, ['class' => 'form-check-input', 'id' => 'addpropcheck']) }}
                                </div>
                                {{-- {{ Form::select('customer_id', $customers,$customerId, array('class' => 'form-control select','id'=>'company','required'=>'required')) }} --}}

                                <div class=" new_customer" id="company-box">
                                    {{-- {{ Form::label('customer_id', __('Customer'),['class'=>'form-label']) }} --}}
                                    {{ Form::select('customer_id', $customers,$customerId, array('class' => 'form-control select req_old','id'=>'company','required'=>'required')) }}
                                </div>

                                <div class="new_customer" id="company_detail" style="margin-top:20px;">
                                    {{ Form::label('contract', __('Contract'),['class'=>'form-label']) }}
                                    {{-- {{ Form::select('contract_id', null,null, array('class' => 'form-control select','id'=>'contract','required'=>'required')) }} --}}
                                    <select name="contract_id" id="contract" class="form-control select">
                                        <option selected disabled value="">Select a Contract</option>
                                    </select>

                                </div>
                                <div class="form-group col-md-12 d-none companyText">
                                    {{ Form::text('newcustomer', '', ['class' => 'form-control d-none companyText req' ]) }}
                                </div>
                                <div class="form-group col-md-12 d-none companyText">
                                    {{ Form::label('phone_no', __('Phone No'), ['class' => 'form-label']) }}<span style="color: red"> *</span>
                                    {{ Form::number('phone_no', '', ['class' => 'form-control req']) }}
                                </div>

                                <div class="form-group col-md-12 d-none companyText">
                                    {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}<span style="color: red"> *</span>
                                    {{ Form::email('email', '', ['class' => 'form-control req','placeholder' =>'123@gmail.com']) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('issue_date', __('Issue Date'),['class'=>'form-label']) }}<span style="color: red"> *</span>
                                        <div class="form-icon-user">
                                            {{Form::date('issue_date',null,array('class'=>'form-control','required'=>'required', 'id'=>'issue_date'))}}

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('due_date', __('Due Date'),['class'=>'form-label']) }}<span style="color: red"> *</span>
                                        <div class="form-icon-user">
                                            {{Form::date('due_date',null,array('class'=>'form-control','required'=>'required', 'id'=>'due_date'))}}

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('invoice_number', __('Invoice Number'),['class'=>'form-label']) }}<span style="color: red"> *</span>
                                        <div class="form-icon-user">
                                            <input type="text" class="form-control" value="{{$invoice_number}}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('category_id', __('Category'),['class'=>'form-label']) }}<span style="color: red"> *</span>
                                        {{ Form::select('category_id', $category,null, array('class' => 'form-control select','required'=>'required')) }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('ref_number', __('Ref Number'),['class'=>'form-label']) }}
                                        <div class="form-icon-user">
                                            <span><i class="ti ti-joint"></i></span>
                                            {{ Form::text('ref_number', '', array('class' => 'form-control')) }}
                                        </div>
                                    </div>
                                </div>
{{--                                <div class="col-md-6">--}}
{{--                                    <div class="form-check custom-checkbox mt-4">--}}
{{--                                        <input class="form-check-input" type="checkbox" name="discount_apply" id="discount_apply">--}}
{{--                                        <label class="form-check-label " for="discount_apply">{{__('Discount Apply')}}</label>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="col-md-6">--}}
{{--                                    <div class="form-group">--}}
{{--                                        {{Form::label('sku',__('SKU')) }}--}}
{{--                                        {!!Form::text('sku', null,array('class' => 'form-control','required'=>'required')) !!}--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                                @if(!$customFields->isEmpty())
                                    <div class="col-md-6">
                                        <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                                            @include('customFields.formBuilder')
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <h5 class=" d-inline-block mb-4">{{__('Product & Services')}}</h5>
            <div class="card repeater">
                <div class="item-section py-2">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">
                            <div class="all-button-box me-2">
                                <a href="#" data-repeater-create="" class="btn btn-primary" data-bs-toggle="modal" data-target="#add-bank">
                                    <i class="ti ti-plus"></i> {{__('Add item')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-border-style mt-2">
                    <div class="table-responsive">
                        <table class="table  mb-0 table-custom-style" data-repeater-list="items" id="sortable-table">
                            <thead>
                            <tr>
                                <th>{{__('Items')}}</th>
                                <th>{{__('Quantity')}}</th>
                                <th>{{__('Price')}} </th>
                                <th>{{__('Discount')}}</th>
                                <th>{{__('Tax')}} (%)</th>
                                <th class="text-end">{{__('Amount')}} <br><small class="text-danger font-weight-bold">{{__('after tax & discount')}}</small></th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody class="ui-sortable" data-repeater-item>
                            <tr>

                                <td width="25%" class="form-group pt-0">
                                    {{ Form::select('item', $product_services,'', array('class' => 'form-control select2 item','data-url'=>route('invoice.product'),'required'=>'required')) }}

                                    {{-- {{ Form::select('item', $product_services,'', array('class' => 'form-control select2 item','required'=>'required')) }} --}}
                                </td>
                                <td>
                                    <div class="form-group price-input input-group search-form">
                                        {{ Form::text('quantity','', array('class' => 'form-control quantity','required'=>'required','placeholder'=>__('Qty'),'required'=>'required')) }}
                                        <span class="unit input-group-text bg-transparent"></span>
                                    </div>
                                </td>


                                <td>
                                    <div class="form-group price-input input-group search-form">
                                        {{ Form::text('price','', array('class' => 'form-control price','required'=>'required','placeholder'=>__('Price'),'required'=>'required')) }}
                                        <span class="input-group-text bg-transparent">{{\Auth::user()->currencySymbol()}}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group price-input input-group search-form">
                                        {{ Form::text('discount','', array('class' => 'form-control discount','required'=>'required','placeholder'=>__('Discount'))) }}
                                        <span class="input-group-text bg-transparent">{{\Auth::user()->currencySymbol()}}</span>
                                    </div>
                                </td>



                                <td>
                                    <div class="form-group">
                                        <div class="input-group colorpickerinput">
                                            <div class="taxes"></div>
                                            {{ Form::hidden('tax','', array('class' => 'form-control tax text-dark')) }}
                                            {{ Form::hidden('itemTaxPrice','', array('class' => 'form-control itemTaxPrice')) }}
                                            {{ Form::hidden('itemTaxRate','', array('class' => 'form-control itemTaxRate')) }}
                                        </div>
                                    </div>
                                </td>

                                <td class="text-end amount">0.00</td>
                                <td>
                                    <a href="#" class="ti ti-trash text-white repeater-action-btn bg-danger ms-2 bs-pass-para" data-repeater-delete></a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="form-group">
                                        {{ Form::textarea('description', null, ['class'=>'form-control pro_description','rows'=>'2','placeholder'=>__('Description')]) }}
                                    </div>
                                </td>
                                <td colspan="5"></td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td></td>
                                <td><strong>{{__('Sub Total')}} ({{\Auth::user()->currencySymbol()}})</strong></td>
                                <td class="text-end subTotal">0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td></td>
                                <td><strong>{{__('Discount')}} ({{\Auth::user()->currencySymbol()}})</strong></td>
                                <td class="text-end totalDiscount">0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td></td>
                                <td><strong>{{__('Tax')}} ({{\Auth::user()->currencySymbol()}})</strong></td>
                                <td class="text-end totalTax">0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td class="blue-text"><strong>{{__('Total Amount')}} ({{\Auth::user()->currencySymbol()}})</strong></td>
                                <td class="text-end totalAmount blue-text"></td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="button" value="{{__('Cancel')}}" onclick="location.href = '{{route("invoice.index")}}';" class="btn btn-light">
            <input type="submit" value="{{__('Create')}}" id="myButton" class="btn btn-primary">
        </div>
        {{ Form::close() }}

    </div>
@endsection


