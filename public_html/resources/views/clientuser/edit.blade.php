
{{ Form::model($clients, array('route' => array('clientuser.update', $clients->id), 'method' => 'PUT')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group">
            {{ Form::label('name', __('Name'),['class'=>'form-label']) }}
            {{ Form::text('name', null, array('class' => 'form-control','placeholder'=>__('Enter Client Name'),'required'=>'required')) }}
        </div>
        <div class="form-group">
            {{ Form::label('company', __('company'), ['class' => 'form-label']) }}
            {{ Form::select('company', $company, $clients->company_id, ['class' => 'form-control', 'placeholder' => __('Select Company'), 'required' => 'required']) }}    
        </div>
        <div class="form-group">
            {{ Form::label('email', __('E-Mail Address'),['class'=>'form-label']) }}
            {{ Form::email('email', null, array('class' => 'form-control','placeholder'=>__('Enter Client Email'),'required'=>'required')) }}
        </div>
        <div class="form-group">
            {{ Form::label('password', __('Password'), ['class' => 'form-label']) }}
            {{ Form::password('password', [
                'class' => 'form-control form-input',
                'placeholder' => __('Enter Client password'),
            ]) }}
        </div>
        {{-- @dd($clients) --}}
        <div class="form-group">
            {{ Form::label('cnic', __('CNIC'),['class'=>'form-label']) }}
            {{ Form::number('cnic', $clients->clientuser->cnic, array('class' => 'form-control','placeholder'=>__('Enter CNIC'),'required'=>'required')) }}
        </div>
        <div class="form-group">
            {{ Form::label('phone', __('Phone'),['class'=>'form-label']) }}
            {{ Form::number('phone',$clients->clientuser->phone, array('class' => 'form-control','placeholder'=>__('Enter Phone'),'required'=>'required')) }}
        </div>
         <div class="form-group col-md-6 d-flex" style="gap: 15px;">
            {{ Form::label('is_admin', __('Make User as Admin'), ['class' => 'form-label']) }}
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" name="is_admin" id="is_admin" value="1" {{ isset($clients->is_admin) && $clients->is_admin == 1 ? 'checked' : '' }}>
                <label class="custom-control-label form-check-label" for="is_admin"></label>
            </div>
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


