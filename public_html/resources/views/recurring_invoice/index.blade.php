@extends('layouts.admin')
@section('page-title')
    {{__('Manage Recurring Invoices')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Recurring Invoices')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('create invoice')
            <a href="{{ route('recurring-invoices.create') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{__('Create Template')}}">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th>{{ __('Template Name') }}</th>
                                <th>{{ __('Customer') }}</th>
                                <th>{{ __('Cycle') }}</th>
                                <th>{{ __('Next Invoice Date') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th width="200px">{{ __('Action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($templates as $template)
                                <tr>
                                    <td>{{ $template->name }}</td>
                                    <td>{{ !empty($template->customer) ? $template->customer->name : '-' }}</td>
                                    <td>{{ ucfirst($template->cycle) }}</td>
                                    <td>{{ Auth::user()->dateFormat($template->next_invoice_date) }}</td>
                                    <td>
                                        @if($template->status == 'active')
                                            <span class="badge bg-success p-2 px-3 rounded">{{ __('Active') }}</span>
                                        @elseif($template->status == 'paused')
                                            <span class="badge bg-warning p-2 px-3 rounded">{{ __('Paused') }}</span>
                                        @elseif($template->status == 'completed')
                                            <span class="badge bg-info p-2 px-3 rounded">{{ __('Completed') }}</span>
                                        @else
                                            <span class="badge bg-danger p-2 px-3 rounded">{{ ucfirst($template->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="Action">
                                        <span>
                                            <div class="action-btn bg-info ms-2">
                                                <a href="{{ route('recurring-invoices.show', \Crypt::encrypt($template->id)) }}" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{__('View Logs / Invoices')}}">
                                                    <i class="ti ti-eye text-white"></i>
                                                </a>
                                            </div>
                                            @can('edit invoice')
                                                <div class="action-btn bg-primary ms-2">
                                                    <a href="{{ route('recurring-invoices.edit', \Crypt::encrypt($template->id)) }}" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{__('Edit')}}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                                @if($template->status == 'active')
                                                    <div class="action-btn bg-warning ms-2">
                                                        <a href="{{ route('recurring-invoices.pause', \Crypt::encrypt($template->id)) }}" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{__('Pause')}}">
                                                            <i class="ti ti-player-pause text-white"></i>
                                                        </a>
                                                    </div>
                                                @elseif($template->status == 'paused')
                                                    <div class="action-btn bg-success ms-2">
                                                        <a href="{{ route('recurring-invoices.resume', \Crypt::encrypt($template->id)) }}" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{__('Resume')}}">
                                                            <i class="ti ti-player-play text-white"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                            @endcan
                                            @can('delete invoice')
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['recurring-invoices.destroy', \Crypt::encrypt($template->id)], 'id' => 'delete-form-' . $template->id]) !!}
                                                        <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}" data-confirm-yes="document.getElementById('delete-form-{{ $template->id }}').submit();">
                                                            <i class="ti ti-trash text-white"></i>
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
