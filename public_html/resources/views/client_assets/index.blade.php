@extends('layouts.admin')
@section('page-title')
    {{__('Assets')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Assets')}}</li>
@endsection
@php
    $profile=\App\Models\Utility::get_file('uploads/avatar/');
@endphp


@section('action-btn')
    <div class="float-end">
        @can('create assets')
            <a href="#" data-url="{{ route('account-assets-client.create') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Create New Assets')}}" data-bs-toggle="tooltip" title="{{__('Create')}}"  class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    @if(\Auth::user()->type == 'company')
    <div class="row">
        <div class="col-sm-12">
            <div class="mt-2 " id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['account-assets-client.index'], 'method' => 'GET', 'id' => 'account-assets_submit']) }}
                        <div class="row d-flex justify-content-end ">
        
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                <div class="btn-box">
                                    {{ Form::label('branches', __('Branches'),['class'=>'form-label'])}}
                                    {{ Form::select('branches', $branches, isset($_GET['branches']) ? $_GET['branches'] : '', ['class' => 'form-control select' ]) }}
                                </div>                               
                            </div>

                            <div class="col-auto float-end ms-2 mt-4">
                                <a href="#" class="btn btn-sm btn-primary"
                                    onclick="document.getElementById('account-assets_submit').submit(); return false;"
                                    data-toggle="tooltip" data-original-title="{{ __('apply') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>
                                <a href="{{ route('account-assets-client.index') }}" class="btn btn-sm btn-danger" data-toggle="tooltip"
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
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                {{-- <th>{{__('Name')}}</th> --}}
                                <th>{{__('Company')}}</th>
                                <th>{{__('Issue Date')}}</th>
                                <th>{{__('End Date')}}</th>
                                {{-- <th>{{__('Amount')}}</th> --}}
                                <th>{{__('Description')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($assets as $asset)
                                <tr>
                                    {{-- <td class="font-style">{{ $asset->name }}</td> --}}
                                    <td class="font-style">{{ @$asset->company->name }}</td>
                                    {{-- <td>
                                        <div class="avatar-group">
                                            @foreach($asset->users($asset->employee_id) as $user)
                                                <a href="#" class="avatar rounded-circle avatar-sm avatar-group">
                                                    <img alt="" @if(!empty($user->avatar)) src="{{$profile.'/'.$user->avatar}}"
                                                         @else src="{{asset('/storage/uploads/avatar/avatar.png')}}"
                                                         @endif data-original-title="{{(!empty($user)?$user->name:'')}}"
                                                         data-bs-toggle="tooltip" data-original-title="{{(!empty($user)?$user->name:'')}}" class="">
                                                </a>
                                            @endforeach
                                        </div>

                                    </td> --}}

                                    <td class="font-style">{{ \Auth::user()->dateFormat($asset->purchase_date) }}</td>
                                    <td class="font-style">{{ \Auth::user()->dateFormat($asset->supported_date) }}</td>
                                    {{-- <td class="font-style">{{ \Auth::user()->priceFormat($asset->amount) }}</td> --}}
                                    <td class="font-style">{{ !empty($asset->description)?$asset->description:'-' }}</td>
                                    <td class="Action">
                                        <span>
                                            @can('edit assets')
                                                <div class="action-btn bg-primary ms-2">
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center" data-url="{{ route('account-assets-client.edit',$asset->id) }}" data-ajax-popup="true" data-size="lg" data-title="{{__('Edit Assets')}}" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}">
                                                    <i class="ti ti-pencil text-white"></i>
                                                </a>
                                                </div>
                                            @endcan
                                            @can('delete assets')
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['account-assets-client.destroy', $asset->id],'id'=>'delete-form-'.$asset->id]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$asset->id}}').submit();">
                                                        <i class="ti ti-trash text-white text-white"></i>
                                                    </a>
                                                    {!! Form::close() !!}
                                                </div>
                                            @endcan
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
