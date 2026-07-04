{{ Form::open(['url'=>'job-application','method'=>'post','enctype'=>"multipart/form-data"]) }}
  <div class="modal-body">
    <div class="row">
      {{-- Job select --}}
      <div class="form-group col-md-12">
        {{ Form::label('job', __('Job'), ['class'=>'form-label']) }}
        {{ Form::select('job', $jobs, null, ['class'=>'form-control select2','id'=>'jobs','required']) }}
      </div>

      {{-- Always‑visible fields --}}
      <div class="form-group col-md-6">
        {{ Form::label('name', __('Name'), ['class'=>'form-label']) }}
        {{ Form::text('name', null, ['class'=>'form-control','placeholder'=>__('Enter Name'),'required']) }}
      </div>
      <div class="form-group col-md-6">
        {{ Form::label('email', __('Email'), ['class'=>'form-label']) }}
        {{ Form::email('email', null, ['class'=>'form-control','placeholder'=>__('Enter Email'),'required']) }}
      </div>
      <div class="form-group col-md-6">
        {{ Form::label('phone', __('Phone'), ['class'=>'form-label']) }}
        {{ Form::text('phone', null, ['class'=>'form-control','placeholder'=>__('Enter Phone'),'required']) }}
      </div>

      {{-- Conditionally‑shown fields --}}
      <div class="form-group col-md-6 dob d-none">
        {{ Form::label('dob', __('Date of Birth'), ['class'=>'form-label']) }}
        {{ Form::date('dob', old('dob'), ['class'=>'form-control']) }}
      </div>
      <div class="form-group col-md-6 gender ">
        {{ Form::label('gender', __('Gender'), ['class'=>'form-label']) }}
        <div class="d-flex radio-check">
          <div class="form-check form-check-inline">
            <input type="radio" id="g_male" name="gender" value="Male" class="form-check-input">
            <label class="form-check-label" for="g_male">{{ __('Male') }}</label>
          </div>
          <div class="form-check form-check-inline">
            <input type="radio" id="g_female" name="gender" value="Female" class="form-check-input">
            <label class="form-check-label" for="g_female">{{ __('Female') }}</label>
          </div>
        </div>
      </div>

      {{-- Country/State/City --}}
      <div class="form-group col-md-6 location d-none">
        {{ Form::label('country', __('Country'), ['class'=>'form-label']) }}
        {{ Form::text('country', null, ['class'=>'form-control']) }}
      </div>
      <div class="form-group col-md-6 location d-none">
        {{ Form::label('state', __('State'), ['class'=>'form-label']) }}
        {{ Form::text('state', null, ['class'=>'form-control']) }}
      </div>
      <div class="form-group col-md-6 location ">
        {{ Form::label('city', __('City'), ['class'=>'form-label']) }}
        {{ Form::text('city', null, ['class'=>'form-control']) }}
      </div>

      {{-- File uploads --}}
      <div class="form-group col-md-6 profile d-none ">
        {{ Form::label('profile', __('Profile'), ['class'=>'form-label']) }}
        <div class="choose-file">
          <label for="profile" class="form-label">
            <div>{{ __('Choose file here') }}</div>
            <input type="file" name="profile" id="profile" class="form-control" data-filename="profile_create">
          </label>
          <p class="profile_create"></p>
        </div>
      </div>
      <div class="form-group col-md-6 resume">
        {{ Form::label('resume', __('CV / Resume'), ['class'=>'form-label']) }}
        <div class="choose-file">
          <label for="resume" class="form-label">
            <div>{{ __('Choose file here') }}</div>
            <input type="file" name="resume" id="resume" class="form-control" data-filename="resume_create">
          </label>
          <p class="resume_create"></p>
        </div>
      </div>

      {{-- Cover letter --}}
      <div class="form-group col-md-12 letter">
        {{ Form::label('cover_letter', __('Cover Letter'), ['class'=>'form-label']) }}
        {{ Form::textarea('cover_letter', null, ['class'=>'form-control']) }}
      </div>

      {{-- Dynamic questions --}}
      @foreach($questions as $question)
        <div class="form-group col-md-12 question question_{{ $question->id }}">
          {{ Form::label("question_{$question->id}", $question->question, ['class'=>'form-label']) }}
          {{ Form::text("question[{$question->id}]", null, [
              'class'       => 'form-control',
              $question->is_required === 'yes' ? 'required' : ''  => true
          ]) }}
        </div>
      @endforeach

    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
  </div>
{{ Form::close() }}
