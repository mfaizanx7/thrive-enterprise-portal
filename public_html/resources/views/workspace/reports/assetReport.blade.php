@extends('layouts.admin')
@php
    // $profile=asset(Storage::url('uploads/avatar/'));
    $profile = \App\Models\Utility::get_file('uploads/avatar/');
@endphp
@section('page-title')
    {{ __('Manage Client Assets') }}
@endsection
@push('script-page')
    <script>
        function submitWithPrintFlag() {
            const form = document.getElementById('assetreport');
            const input = document.getElementById('is_print');
            input.value = 1;
            form.target = '_blank';
            form.submit();
            form.target = '';
            input.value = 0;
            return false;
        }

        function resetPrintFlagAndSubmit() {
            const form = document.getElementById('assetreport');
            const input = document.getElementById('is_print');
            input.value = 0;
            form.submit();
        }
    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('All Assets') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        {{ Form::open(['route' => ['assetreport'], 'method' => 'GET', 'id' => 'assetreport']) }}
        <input type="hidden" name="is_print" id="is_print" value="0">
        <a href="#" class="btn btn-sm btn-outline-primary" onclick="submitWithPrintFlag(); return false;"
            data-toggle="tooltip" data-original-title="{{ __('Print') }}">
            <span class="btn-inner--icon"><i class="ti ti-printer"></i></span>
        </a>
        {{ Form::close() }}
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
                                    <th>{{ __('Company') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Item') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assets as $asset)
                                    @if($asset->assetdetail && @$asset->assetdetail->count())
                                        @foreach ($asset->assetdetail as $detail)
                                            <tr>
                                                <td>
                                                    {{ !empty($asset->company) ? $asset->company->name : '-' }}
                                                </td>
                                                <td>
                                                    {{ !empty($asset->description) ? $asset->description : '-' }}
                                                </td>
                                                <td>
                                                    {{ $detail->name ?? '-' }}
                                                </td>
                                                <td>
                                                    {{ $detail->quantity ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>{{ $asset->company->name ?? '-' }}</td>
                                            <td>{{ $asset->description ?? '-' }}</td>
                                            <td>-</td>
                                            <td>-</td>
                                        </tr>
                                    @endif
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
