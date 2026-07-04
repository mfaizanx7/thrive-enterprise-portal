<script>
    $(document).on('keyup', '#amount_enter', function() {
       var amount = $(this).val();
       var tot = $('#tot').val();

       $('#wth_amount').val(parseFloat(tot) - parseFloat(amount));
   });

   $(document).ready(function() {
        $('form').on('submit', function(event) {
            event.preventDefault(); // Prevent form submission initially

            var wth = $('#wth_amount').val();
            var tax = $('#tax').val();
            var submitButton = document.getElementById('myButton');
            submitButton.disabled = true;

            if (wth > 0 && (tax == null || tax == "")) {
            // If WTH has a value greater than 0, ensure Tax is selected
            show_toastr('error', 'Please select a Tax value', 'error');
            submitButton.disabled = false;
            return; // Stop form submission
        }

            // Re-enable button after 5 seconds (for demonstration purposes)
            setTimeout(function() {
                submitButton.disabled = false;
            }, 5000);

            // If everything is valid, proceed with form submission
            this.submit();
        });
    });
</script>


{{ Form::open(array('route' => array('invoice.payment', $invoice->id),'method'=>'post','enctype' => 'multipart/form-data')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group  col-md-6">
            {{ Form::label('date', __('Date'),['class'=>'form-label']) }}
            {{ Form::date('date', '', array('class' => 'form-control ','required'=>'required')) }}
        </div>
        <input type="hidden" id="tot" value="{{$invoice->newgetDue()}}">
        <div class="form-group col-md-6">
            {{ Form::label('amount', __('Amount'),['class'=>'form-label']) }}
            {{ Form::number('amount',$invoice->newgetDue(), array('class' => 'form-control','id'=>'amount_enter','required'=>'required','step'=>'0.01','min'=>'0')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('tax_name', __('Tax Rate'),['class'=>'form-label']) }}
            <select name="tax_id"  class="form-control select" id="tax">
                <option value="" disabled selected>Select Tax</option>
                @foreach ($taxes as $tax)
                <option value="{{$tax->id}}" data-per="{{$tax->rate}}" >{{$tax->name}} ( {{$tax->rate}} )</option>
                @endforeach
            </select>
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('wth', __('W.T.H'),['class'=>'form-label']) }}
            {{ Form::number('wth','', array('class' => 'form-control','id'=>'wth_amount','step'=>'0.01','min'=>'0')) }}
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('account_id', __('Account'),['class'=>'form-label']) }}
            {{ Form::select('account_id',$accounts,null, array('class' => 'form-control select','required'=>'required')) }}
        </div>

        <div class="form-group  col-md-6">
            {{ Form::label('reference', __('Reference'),['class'=>'form-label']) }}
            {{ Form::text('reference', '', array('class' => 'form-control')) }}
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
            {{ Form::textarea('description', '', array('class' => 'form-control','rows'=>3)) }}
        </div>

        <div class="col-md-6 form-group">
            {{ Form::label('add_receipt', __('Payment Receipt'), ['class' => 'form-label']) }}
            <div class="choose-file form-group">
                <label for="file" class="form-label">
                    <input type="file" name="add_receipt" id="image" class="form-control" >
                </label>
                <p class="upload_file"></p>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Add')}}"  id="myButton"  class="btn  btn-primary">
    </div>

</div>
{{ Form::close() }}

