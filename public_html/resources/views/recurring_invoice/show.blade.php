@extends('layouts.admin')
@section('page-title')
    {{__('Recurring Invoice Template Detail')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('recurring-invoices.index')}}">{{__('Recurring Invoices')}}</a></li>
    <li class="breadcrumb-item">{{ $template->name }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('edit invoice')
            <a href="{{ route('recurring-invoices.edit', \Crypt::encrypt($template->id)) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{__('Edit Template')}}">
                <i class="ti ti-pencil"></i>
            </a>
        @endcan
        @if($template->status == 'active')
            <a href="{{ route('recurring-invoices.pause', \Crypt::encrypt($template->id)) }}" class="btn btn-sm btn-warning ms-1" data-bs-toggle="tooltip" title="{{__('Pause')}}">
                <i class="ti ti-player-pause"></i>
            </a>
        @elseif($template->status == 'paused')
            <a href="{{ route('recurring-invoices.resume', \Crypt::encrypt($template->id)) }}" class="btn btn-sm btn-success ms-1" data-bs-toggle="tooltip" title="{{__('Resume')}}">
                <i class="ti ti-player-play"></i>
            </a>
        @endif
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Template Info') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>{{ __('Status') }}</strong>
                            @if($template->status == 'active')
                                <span class="badge bg-success">{{ __('Active') }}</span>
                            @elseif($template->status == 'paused')
                                <span class="badge bg-warning">{{ __('Paused') }}</span>
                            @else
                                <span class="badge bg-info">{{ ucfirst($template->status) }}</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>{{ __('Cycle') }}</strong>
                            <span>{{ ucfirst($template->cycle) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>{{ __('Next Generation') }}</strong>
                            <span>{{ Auth::user()->dateFormat($template->next_invoice_date) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>{{ __('Customer') }}</strong>
                            <span>{{ $template->customer->name }}</span>
                        </li>
                        {{-- <li class="list-group-item">
                            <strong>{{ __('Auto-send') }}:</strong>
                            <span>{{ $template->auto_send ? __('Yes') : __('No') }}</span>
                        </li> --}}
                    </ul>
                    @if($template->notes)
                        <div class="mt-3">
                            <strong>{{ __('Notes') }}:</strong>
                            <p class="text-muted">{{ $template->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5>{{ __('Line Items') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th style="width: 40%;">{{ __('Description') }}</th>
                                    <th class="text-end" style="width: 20%;">{{ __('Tax') }}</th>
                                    <th class="text-end" style="width: 20%;">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $total = 0; @endphp
                                @foreach($template->items as $item)
                                    @php $total += $item->total; @endphp
                                    <tr>
                                        <td>{{ $item->description }} <small class="text-muted">({{ $item->quantity }} x {{ Auth::user()->priceFormat($item->unit_price) }})</small></td>
                                        <td class="text-end">{{ Auth::user()->priceFormat($item->tax_amount) }}</td>
                                        <td class="text-end">{{ Auth::user()->priceFormat($item->total) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>{{ __('Total per Invoice') }}</th>
                                    <th class="text-end">{{ Auth::user()->priceFormat($total) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>{{ __('Generation History & Logs') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Invoice #') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Details') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($template->logs as $log)
                                    <tr>
                                        <td>{{ Auth::user()->dateFormat($log->generated_at) }}</td>
                                        <td>
                                            @if($log->invoice)
                                                <a href="{{ route('invoice.show', \Crypt::encrypt($log->invoice_id)) }}" class="btn btn-outline-primary btn-sm">
                                                    {{ Auth::user()->invoiceNumberFormat($log->invoice->invoice_id) }}
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->status == 'generated')
                                                <span class="badge bg-success">{{ __('Success') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ ucfirst($log->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->error_message)
                                                <small class="text-danger">{{ $log->error_message }}</small>
                                            @else
                                                <small class="text-muted">{{ __('Invoice auto-generated') }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->invoice)
                                                <a href="{{ route('invoice.show', \Crypt::encrypt($log->invoice_id)) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="{{__('View Invoice')}}">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                            @endif
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
