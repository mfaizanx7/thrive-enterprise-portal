
{{ Form::model($spacetype, array('route' => array('spacetype.update', $spacetype->id), 'method' => 'PUT')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group">
            {{ Form::label('name', __('Name'),['class'=>'form-label']) }}
            {{ Form::text('name', null, array('class' => 'form-control','placeholder'=>__('Enter Spacetype Name'),'required'=>'required')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('tax_name', __('Tax Rate Name'),['class'=>'form-label']) }}
            <select name="tax_id"  class="form-control select" id="tax">
                    <option value="" disabled selected>Select Tax</option>
                @foreach ($taxes as $tax)
                    <option value="{{$tax->id}}" @if($spacetype->tax_id == $tax->id) selected @endif data-per="{{$tax->rate}}" >{{$tax->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('rate', __('Tax Rate %'),['class'=>'form-label']) }}
            {{ Form::number('rate', '', array('class' => 'form-control','readonly'=>'readonly','required'=>'required','id'=>'rate')) }}
        </div>

        @if(!$customFields->isEmpty())
            @include('custom_fields.formBuilder')
        @endif

    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>

{{Form::close()}}
<script>
    setTimeout(function() {
        $('#tax').trigger('change');
    }, 1000);

    $('#tax').on('change', function() {
            var tax_id = $(this).val();
            var selectedOption = $(this).find('option:selected');
            var taxRate = selectedOption.data('per');

            $('#rate').val(taxRate);
        });
</script>


