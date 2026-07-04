@extends('layouts.admin')
@section('page-title')
    {{ucwords($project->project_name)}}
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
@endpush
@push('script-page')
<script src="{{asset('css/summernote/summernote-bs4.js')}}"></script>
    
    <script>
        function submitForm() {
                document.getElementById('sss').submit();
            }
            /*For Task Checklist*/
            $(document).on('click', '#checklist_submitsrs', function () {
                var name = $("#form-checklistsrs input[name=name]").val();
                if (name != '') {
                    $.ajax({
                        url: $("#form-checklistsrs").data('action'),
                        data: {name: name, "_token": "{{ csrf_token() }}"},
                        type: 'POST',
                        success: function (data) {
                            data = JSON.parse(data);
                            // console.log('form-checklistsrs', data);
                            show_toastr('{{__('success')}}', '{{ __("Checklist Added Successfully!")}}');
                            var html = '<div class="card border shadow-none checklist-member">' +
                                '                    <div class="px-3 py-2 row align-items-center">' +
                                '                        <div class="col">' +
                                '                            <div class="form-check form-check-inline">' +
                                '                                <label class="form-check-label h6 text-sm" for="check-item-' + data.id + '">' + data.name + '</label>' +
                                '                            </div>' +
                                '                        </div>' +
                                '                    </div>' +
                                '                </div>'

                            $("#checklist").children().eq(1).before(html);
                            $("#form-checklistsrs input[name=name]").val('');
                            // $("#form-checklistsrs").collapse('toggle');
                        },
                        error: function (data) {
                            data = data.responseJSON;
                            show_toastr('error', data.message);
                        }
                    });
                } else {
                    show_toastr('error', '{{ __("Please write checklist name!")}}');
                }
            });
    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('projects.index')}}">{{__('Project')}}</a></li>
    <li class="breadcrumb-item">{{ucwords($project->project_name)}}</li>
@endsection
@section('action-btn')
    <div class="float-end">

    </div>
@endsection

@section('content')
{{ Form::open(['url' => 'srs/project/store', 'method' => 'post','enctype' => 'multipart/form-data' , 'id'=>'sss']) }}

<div class="modal-body">
    <input type="hidden" name="project" value="{{$project->id}}">
    <div class="row">
        <div class="col-sm-12 col-md-12 mt-5">
            <div class="form-group">
                {{ Form::label('srs_details', __('SRS Details'), ['class' => 'form-label']) }}
                <textarea class="form-control summernote-simple-2" name="srs_details" id="exampleFormControlTextarea2" rows="8">{!!$project->srs_details!!}</textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12">
            {{ Form::label('srs_doc', __('SRS Document'), ['class' => 'form-label']) }}
            <span class="btn btn-sm btn-primary " style="float:right; margin-bottom: 11px;"><a href="{{ (!empty($project->srs_doc)?asset(Storage::url('uploads/document')).'/'.$project->srs_doc:'') }}" style="color: #fff" target="_blank">{{ (!empty($project->srs_doc)? 'View Document':'') }}</a></span>

            <div class="form-file mb-3">
                <input type="file" class="form-control file-validate" name="srs_doc" >
                <p id="" class="file-error text-danger"></p>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}"  class="btn  btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Update')}}" onclick="submitForm()" class="btn  btn-primary">
    </div>
    {{Form::close()}}
    <div class="card mt-4">
        <div class="card-body">
            <div class="row mb-4 align-items-center">
                <div class="col-6">
                    <h5> {{__('Checklist')}}</h5>
                </div>
                <div class="col-6 ">
                    <div class="float-end">
                        {{-- <a data-bs-toggle="collapsesrs" href="#form-checklistsrs" role="button" aria-expanded="false" aria-controls="form-checklistsrs" data-bs-toggle="tooltip" title="{{__('Add item')}}" class="btn btn-sm btn-primary">
                            <i class="ti ti-plus"></i>
                        </a> --}}
                    </div>
                </div>
            </div>
            <div class="checklist" id="checklist" style="max-height: 550px; overflow-y: scroll; overflow-x: hidden;">
                <form id="form-checklistsrs" class="collapsesrs pb-2" data-action="{{route('srs.task',[$project->id])}}">
                    <div class="card border shadow-none">
                        <div class="px-3 py-2 row align-items-center">
                            @csrf
                            <div class="col-11">
                                <input type="text" name="name" required class="form-control" placeholder="{{__('Checklist Name')}}"/>
                            </div>
                            <div class="col-1 card-meta d-inline-flex align-items-center">
                                <button class="btn btn-sm btn-primary" type="button" id="checklist_submitsrs" data-bs-toggle="tooltip" title="{{__('Create')}}">
                                    <i class="ti ti-check"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                @foreach($project->srsChecklist as $checklistsrs)
                    <div class="card border shadow-none checklist-member">
                        <div class="px-3 py-2 row align-items-center">
                            <div class="col">
                                <div class="form-check form-check-inline">
                                    {{-- <input type="checkbox" class="form-check-input" id="check-item-{{ $checklistsrs->id }}" @if($checklist->status) checked @endif data-url="{{route('checklist.update',[$task->project_id,$checklist->id])}}"> --}}
                                    <label class="form-check-label h6 text-sm" for="check-item-{{ $checklistsrs->id }}">{{ $checklistsrs->name }}</label>
                                </div>
                            </div>
                            {{-- <div class="col-auto">
                                <div class="action-btn bg-danger ms-2">
                                    <a href="#" class="mx-3 btn btn-sm  align-items-center delete-checklist" data-url="{{ route('checklist.destroy',[$task->project_id,$checklist->id]) }}">
                                        <i data-bs-toggle="tooltip" title="{{__('Delete')}}" class="ti ti-trash text-white"></i>
                                    </a>
                                </div>

                            </div> --}}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

