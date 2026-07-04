{{ Form::model($lead, array('route' => array('leads.update', $lead->id), 'method' => 'PUT')) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $plan= \App\Models\Utility::getChatGPTSettings();
    @endphp
    @if($plan->chatgpt == 1)
    <div class="text-end">
        <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['lead']) }}"
           data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
            <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
        </a>
    </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="col-6 form-group">
            {{ Form::label('subject', __('Subject'),['class'=>'form-label']) }}
            {{ Form::text('subject', null, array('class' => 'form-control')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('user_id', __('User'),['class'=>'form-label']) }}
            {{ Form::select('user_id', $users,null, array('class' => 'form-control select')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('name', __('Name'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::text('name', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('email', __('Email'),['class'=>'form-label']) }}<span class="text-danger">*</span>
            {{ Form::email('email', null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('phone', __('Phone'),['class'=>'form-label']) }}
            {{ Form::text('phone', null, array('class' => 'form-control')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('company_name', __('Company Name'),['class'=>'form-label']) }}
            {{ Form::text('company_name', null, array('class' => 'form-control', 'placeholder' => __('Enter Company Name'))) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('sector', __('Sector'),['class'=>'form-label']) }}
            {{ Form::text('sector', null, array('class' => 'form-control', 'placeholder' => __('Enter Sector'))) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('number_of_employees', __('Number of Employees'),['class'=>'form-label']) }}
            {{ Form::text('number_of_employees', null, array('class' => 'form-control', 'placeholder' => __('Enter Number of Employees'))) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('revenue', __('Revenue'),['class'=>'form-label']) }}
            {{ Form::text('revenue', null, array('class' => 'form-control', 'placeholder' => __('Enter Revenue'))) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('contact_person', __('Contact Person'),['class'=>'form-label']) }}
            {{ Form::text('contact_person', null, array('class' => 'form-control', 'placeholder' => __('Enter Contact Person'))) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('region', __('Region'),['class'=>'form-label']) }}
            {{ Form::text('region', null, array('class' => 'form-control', 'placeholder' => __('Enter Region'))) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('address', __('Address'),['class'=>'form-label']) }}
            {{ Form::textarea('address', null, array('class' => 'form-control', 'rows' => '3', 'placeholder' => __('Enter Address'))) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('inbox_url', __('Inbox URL'),['class'=>'form-label']) }}
            {{ Form::text('inbox_url', null, array('class' => 'form-control')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('team_members', __('Team Members'),['class'=>'form-label']) }}
            {{ Form::text('team_members', null, array('class' => 'form-control')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('sr_no', __('Sr.No'),['class'=>'form-label']) }}
            {{ Form::text('sr_no', null, array('class' => 'form-control')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('update_value', __('Update'),['class'=>'form-label']) }}
            {{ Form::text('update_value', null, array('class' => 'form-control')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('follow_up_1', __('Follow Up 1'),['class'=>'form-label']) }}
            {{ Form::text('follow_up_1', null, array('class' => 'form-control')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('update_2_0', __('Update 2.0'),['class'=>'form-label']) }}
            {{ Form::text('update_2_0', null, array('class' => 'form-control')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('follow_up_2', __('Follow Up 2'),['class'=>'form-label']) }}
            {{ Form::text('follow_up_2', null, array('class' => 'form-control')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('follow_up_3', __('Follow Up 3'),['class'=>'form-label']) }}
            {{ Form::text('follow_up_3', null, array('class' => 'form-control')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('pipeline_id', __('Pipeline'),['class'=>'form-label']) }}
            {{ Form::select('pipeline_id', $pipelines,null, array('class' => 'form-control select')) }}
        </div>
        <div class="col-6 form-group">
            {{ Form::label('stage_id', __('Stage'),['class'=>'form-label']) }}
            {{ Form::select('stage_id', [''=>__('Select Stage')],null, array('class' => 'form-control select')) }}
        </div>
        @if(!$customFields->isEmpty())
            {{-- <div class="col-6 form-group">
                <div class="tab-pane fade show" id="tab-2" role="tabpanel"> --}}
                    @include('customFields.formBuilder')
                {{-- </div>
            </div> --}}
        @endif
        <div class="col-12 form-group">
            {{ Form::label('sources', __('Sources'),['class'=>'form-label']) }}
            {{ Form::select('sources[]', $sources,null, array('class' => 'form-control select2','id'=>'choices-multiple2','multiple'=>'')) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('products', __('Products'),['class'=>'form-label']) }}
            {{ Form::select('products[]', $products,null, array('class' => 'form-control select2','id'=>'choices-multiple1','multiple'=>'')) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('notes', __('Notes'),['class'=>'form-label']) }}
            {{ Form::textarea('notes',null, array('class' => 'summernote-simple','row'=>'4')) }}
        </div>

    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>

{{Form::close()}}



<script>
    var stage_id = '{{$lead->stage_id}}';

    $(document).ready(function () {
        var pipeline_id = $('[name=pipeline_id]').val();
        getStages(pipeline_id);
    });

    $(document).on("change", "#commonModal select[name=pipeline_id]", function () {
        var currVal = $(this).val();
        console.log('current val ', currVal);
        getStages(currVal);
    });

    function getStages(id) {
        $.ajax({
            url: '{{route('leads.json')}}',
            data: {pipeline_id: id, _token: $('meta[name="csrf-token"]').attr('content')},
            type: 'POST',
            success: function (data) {
                var stage_cnt = Object.keys(data).length;
                $("#stage_id").empty();
                if (stage_cnt > 0) {
                    $.each(data, function (key, data1) {
                        var select = '';
                        if (key == '{{ $lead->stage_id }}') {
                            select = 'selected';
                        }
                        $("#stage_id").append('<option value="' + key + '" ' + select + '>' + data1 + '</option>');
                    });
                }
                $("#stage_id").val(stage_id);
                $('#stage_id').select2({
                    placeholder: "{{__('Select Stage')}}"
                });
            }
        })
    }
</script>
