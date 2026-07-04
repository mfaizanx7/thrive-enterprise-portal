@extends('layouts.admin')
@php
    // $profile=asset(Storage::url('uploads/avatar/'));
    $profile = \App\Models\Utility::get_file('uploads/avatar/');
@endphp
@section('page-title')
    {{ __('Manage Vacant Spaces') }}
@endsection
@push('script-page')
    <script>
        function submitWithPrintFlag() {
            const form = document.getElementById('vacantspaceReport');
            const input = document.getElementById('is_print');
            input.value = 1;
            form.target = '_blank';
            form.submit();
            form.target = '';
            input.value = 0;
            return false;
        }

        function resetPrintFlagAndSubmit() {
            const form = document.getElementById('vacantspaceReport');
            const input = document.getElementById('is_print');
            input.value = 0;
            form.submit();
        }
    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Vacant Spaces') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        {{ Form::open(['route' => ['vacantspaceReport'], 'method' => 'GET', 'id' => 'vacantspaceReport']) }}
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
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Capacity') }}</th>
                                    <th>{{ __('Available Space') }}</th>
                                    <th>{{ __('Meeting') }}</th>
                                    <th>{{ __('Window') }}</th>
                                    <th style="max-width:300px">{{ __('Description') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($spaces as $space)
                                    <tr>
                                        <td>
                                            {{ !empty($space->name) ? $space->name : '-' }}
                                        </td>
                                        <td>
                                            {{ !empty($space->type) ? $space->type->name : '-' }}
                                        </td>
                                        <td>
                                            @if (!empty($space->capacity) || $space->capacity === 0)
                                                {{ $space->capacity == 0 ? 'Filled' : $space->capacity }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        @php
                                            $assignedCount = $assignroome->count();
                                            $availablespace = $space->capacity - $assignedCount;
                                        @endphp
                                        <td>
                                            {{ $availablespace > 0 ? $availablespace : 'Filled' }}
                                        </td>
                                        
                                        <td>
                                            {{ !empty($space->meeting) ? $space->meeting : '-' }}
                                        </td>
                                        <td>
                                            {{ !empty($space->window) ? $space->window : '-' }}
                                        </td>
                                        <td style="max-width:300px !important; overflow-y: auto;">
                                            {{ !empty($space->description) ? $space->description : '-' }}
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
    </div>
@endsection
