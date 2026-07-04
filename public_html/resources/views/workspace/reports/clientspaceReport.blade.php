@extends('layouts.admin')
@php
    // $profile=asset(Storage::url('uploads/avatar/'));
    $profile = \App\Models\Utility::get_file('uploads/avatar/');
@endphp
@section('page-title')
    {{ __('Manage Contracts') }}
@endsection
@push('script-page')
    <script>
        function submitWithPrintFlag() {
            const form = document.getElementById('clientspaceReport');
            const input = document.getElementById('is_print');
            input.value = 1;
            form.target = '_blank';
            form.submit();
            form.target = '';
            input.value = 0;
            return false;
        }

        function resetPrintFlagAndSubmit() {
            const form = document.getElementById('clientspaceReport');
            const input = document.getElementById('is_print');
            input.value = 0;
            form.submit();
        }
    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Contract Allocated Hours') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        {{ Form::open(['route' => ['clientspaceReport'], 'method' => 'GET', 'id' => 'clientspaceReport']) }}
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
                                    <th>{{ __('Space') }}</th>
                                    <th>{{ __('Company') }}</th>
                                    <th>{{ __('Contract Subject') }}</th>
                                    <th>{{ __('Assign Hour') }}</th>
                                    <th>{{ __('Hourly Rate') }}</th>
                                    <th>{{ __('Value') }}</th>
                                    <th>{{ __('Start Date') }}</th>
                                    <th>{{ __('End Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contracts as $contr)
                                    <tr>

                                        <td>
                                            {{ !empty($contr->space) ? $contr->space->name : '-' }}
                                        </td>
                                        <td>
                                            {{ !empty($contr->company) ? $contr->company->name : '-' }}
                                        </td>
                                        <td>
                                            {{ !empty($contr->contract) ? $contr->contract->subject : '-' }}
                                        </td>
                                        <td>
                                            {{ !empty($contr) ? $contr->assign_hour : '-' }}
                                        </td>
                                        <td>
                                            {{ !empty($contr) ? $contr->hourly_rate : '-' }}
                                        </td>
                                        <td>
                                            {{ !empty($contr->contract) ? $contr->contract->value : '-' }}
                                        </td>
                                        <td>
                                            {{ !empty($contr->contract) ? $contr->contract->start_date : '-' }}
                                        </td>
                                        <td>
                                            {{ !empty($contr->contract) ? $contr->contract->end_date : '-' }}
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
