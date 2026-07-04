

<div class="modal-body">
    <input type="hidden" name="project" value="{{$project->id}}">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                <div style="padding-bottom: 15px;">
                    <span class="btn btn-sm btn-primary ">{{ Form::label('srs_details', __('SRS Details'), ['class' => 'form-label']) }}  </span><span><a href="{{ (!empty($project->srs_doc)?asset(Storage::url('uploads/document')).'/'.$project->srs_doc:'') }}" class="btn btn-sm btn-primary " style="float: right;" target="_blank">{{ (!empty($project->srs_doc)? 'View Document':'') }}</a></span>

                </div>
                <textarea class="form-control summernote-simple-2" name="srs_details" id="exampleFormControlTextarea2" rows="8" disabled readonly>{!!$project->srs_details!!}</textarea>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Initialize Summernote
        $('#exampleFormControlTextarea2').summernote({
            height: 150, // Set height of editor
            toolbar: false, // Disable toolbar
            disableDragAndDrop: true // Disable drag and drop
        });

        // Disable Summernote editor
        $('#exampleFormControlTextarea2').summernote('disable');
    });
</script>

