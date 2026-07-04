@extends('layouts.admin')
@php
    $profile = \App\Models\Utility::get_file('uploads/avatar');
@endphp
@section('page-title')
    {{ __('Manage Companies') }}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item">{{ __('Companies') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        @if (\Auth::user()->type == 'super admin')
            <a href="{{ route('user.userlog') }}" class="btn btn-primary btn-sm {{ Request::segment(1) == 'user' }}"
                data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('User Logs History') }}"><i
                    class="ti ti-user-check"></i>
            </a>
        @endif
        @can('create user')
            <a href="#" data-size="sm" data-url="{{ route('companies.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip"
                data-title="{{ \Auth::user()->type == 'super admin' ? __('Create Company') : __('Create User') }}"
                class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-xxl-12">
            <div class="row">
                @foreach ($companies as $company)
                    <div class="col-md-3 mb-4">
                        <div class="card text-center card-2">
                            <div class="card-header border-0 pb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                            <div class="badge bg-primary p-2 px-3 rounded">
                                                {{ !empty($company->name) ? $company->name : '' }}
                                            </div>
                                    </h6>
                                </div>
                                @if (Gate::check('edit user') || Gate::check('delete user'))
                                    <div class="card-header-right">
                                        <div class="btn-group card-option">
                                            <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-end">
                                                @can('edit user')
                                                    <a href="#!" data-size="sm"
                                                        data-url="{{ route('companies.edit', $company->id) }}"
                                                        data-ajax-popup="true" class="dropdown-item"
                                                        data-bs-original-title="{{ \Auth::user()->type == 'super admin' ? __('Edit Company') : __('Edit Comapny') }}">
                                                        <i class="ti ti-pencil"></i>
                                                        <span>{{ __('Edit') }}</span>
                                                    </a>
                                                @endcan

                                                @can('delete user')
                                                    {!! Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['companies.destroy', $company['id']],
                                                        'id' => 'delete-form-' . $company['id'],
                                                    ]) !!}
                                                    <a href="#!" class="dropdown-item bs-pass-para">
                                                        <i class="ti ti-archive"></i>
                                                        <span>
                                                                {{ __('Delete') }}
                                                        </span>
                                                    </a>
                                                    {!! Form::close() !!}
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body full-card">
                                <div class="img-fluid rounded-circle card-avatar">
                                    <img src="{{ asset(Storage::url('uploads/avatar/avatar.png')) }}"
                                        class="img-user wid-80 round-img rounded-circle">
                                </div>
                                <h4 class=" mt-3 text-primary">{{ $company->name }}</h4>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
