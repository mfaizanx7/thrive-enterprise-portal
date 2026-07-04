@extends('layouts.admin')
@php
   // $profile=asset(Storage::url('uploads/avatar/'));
    $profile=\App\Models\Utility::get_file('uploads/avatar/');
@endphp
@section('page-title')
    {{__('Manage Space')}}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('All Space')}}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        <a href="#" data-size="md" data-url="{{ route('space.create') }}" data-ajax-popup="true"  data-bs-toggle="tooltip" title="{{__('Create')}}"  class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
            <div class="card-body table-border-style">
                        <div class="table-responsive">
                        <table class="table datatable">
                                <thead>
                                <tr>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Capacity')}}</th>
                                    <th>{{__('Price')}}</th>
                                    <th>{{__('Description')}}</th>
                                    <th>{{__('SpaceType')}}</th>
                                    <th>{{__('Meeting')}}</th>
                                    <th>{{__('Window')}}</th>
                                    <th width="200px">{{__('Action')}}</th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($spaces as $space)
                                    <tr>

                                        <td>
                                            {{ (!empty($space->name)) ? $space->name : '-' }}
                                        </td>
                                        <td>
                                            {{ (!empty($space->capacity)) ? $space->capacity : '-' }}
                                        </td>
                                        <td>
                                            {{ (!empty($space->price)) ? $space->price : '-' }}
                                        </td>
                                        <td>
                                            {{ (!empty($space->description)) ? $space->description : '-' }}
                                        </td>
                                        <td>
                                            {{ (!empty($space->type)) ? $space->type->name : '-' }}
                                        </td>
                                        <td>
                                            {{ (!empty($space->meeting)) ? $space->meeting : '-' }}
                                        </td>
                                        <td>
                                            {{ (!empty($space->window)) ? $space->window : '-' }}
                                        </td>
                                        @if(Gate::check('edit space') || Gate::check('delete space'))
                                            <td>
                                                @if($space->name != 'Virtual Office')
                                                    @can('edit space')
                                                    <div class="action-btn bg-primary ms-2">

                                                        <a href="#!"data-url="{{route('space.edit',$space->id)}}"  data-ajax-popup="true" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{__('Edit')}}"
                                                         data-original-title="{{__('Edit')}}"><i class="ti ti-pencil text-white"></i></a>
                                                    </div>

                                                    @endcan
                                                    @can('delete space')
                                                    <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['space.destroy', $space->id],'id'=>'delete-form-'.$space->id]) !!}

                                                        <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$space->id}}').submit();"><i class="ti ti-trash text-white"></i></a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                    @endcan
                                                @else
                                                    <i class="ti ti-lock"></i>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
