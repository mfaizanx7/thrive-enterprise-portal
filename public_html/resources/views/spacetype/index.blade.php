@extends('layouts.admin')
@php
   // $profile=asset(Storage::url('uploads/avatar/'));
    $profile=\App\Models\Utility::get_file('uploads/avatar/');
@endphp
@section('page-title')
    {{__('Manage Spacetype')}}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('All Spaceytype')}}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        <a href="#" data-size="md" data-url="{{ route('spacetype.create') }}" data-ajax-popup="true"  data-bs-toggle="tooltip" title="{{__('Create')}}"  class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
    </div>
@endsection
@section('content')
    @if(\Auth::user()->type == 'company')
    <div class="row">
        <div class="col-sm-12">
            <div class="mt-2 " id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['spacetype.index'], 'method' => 'GET', 'id' => 'spacetype_submit']) }}
                        <div class="row d-flex justify-content-end ">

                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                <div class="btn-box">
                                    {{ Form::label('branches', __('Branches'),['class'=>'form-label'])}}
                                    {{ Form::select('branches', $branches, isset($_GET['branches']) ? $_GET['branches'] : '', ['class' => 'form-control select' , 'onchange' => 'branchtype(this.value)']) }}
                                </div>
                            </div>

                            <div class="col-auto float-end ms-2 mt-4">
                                <a href="#" class="btn btn-sm btn-primary"
                                    onclick="document.getElementById('spacetype_submit').submit(); return false;"
                                    data-toggle="tooltip" data-original-title="{{ __('apply') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>
                                <a href="{{ route('spacetype.index') }}" class="btn btn-sm btn-danger" data-toggle="tooltip"
                                    data-original-title="{{ __('Reset') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off"></i></span>
                                </a>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
            <div class="card-body table-border-style">
                        <div class="table-responsive">
                        <table class="table datatable">
                                <thead>
                                <tr>
                                    <th>{{__('Name')}}</th>
                                    {{--<th>{{__('Tax')}}</th>--}}
                                    <th width="200px">{{__('Action')}}</th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($spacetype as $type)
                                    <tr>

                                        <td>
                                            {{ (!empty($type->name)) ? $type->name : '-' }}
                                        </td>
                                        {{--<td>
                                            {{ (!empty($type->tax)) ? $type->tax->name : '-' }}
                                        </td>--}}
                                        @if(Gate::check('edit spacetype') || Gate::check('delete spacetype'))
                                            <td>
                                                @if($type->name != 'Virtual Office')
                                                    @can('edit spacetype')
                                                    <div class="action-btn bg-primary ms-2">

                                                        <a href="#!"data-url="{{route('spacetype.edit',$type->id)}}"  data-ajax-popup="true" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{__('Edit')}}"
                                                         data-original-title="{{__('Edit')}}"><i class="ti ti-pencil text-white"></i></a>
                                                    </div>

                                                    @endcan
                                                    @can('delete spacetype')
                                                    <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['spacetype.destroy', $type->id],'id'=>'delete-form-'.$type->id]) !!}

                                                        <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$type->id}}').submit();"><i class="ti ti-trash text-white"></i></a>
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
