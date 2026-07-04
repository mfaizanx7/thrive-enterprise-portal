@extends('layouts.admin')
@php
    // $profile=asset(Storage::url('uploads/avatar/'));
    $profile = \App\Models\Utility::get_file('uploads/avatar/');
@endphp
@section('page-title')
    {{ __('Manage Meeting Spaces Report') }}
@endsection
@push('script-page')
    <script>
        function submitWithPrintFlag() {
            const form = document.getElementById('meetingspacereport');
            const input = document.getElementById('is_print');
            input.value = 1;
            form.target = '_blank';
            form.submit();
            form.target = '';
            input.value = 0;
            return false;
        }

        function resetPrintFlagAndSubmit() {
            const form = document.getElementById('meetingspacereport');
            const input = document.getElementById('is_print');
            input.value = 0;
            form.submit();
        }
    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('All Meeting Spaces') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        {{ Form::open(['route' => ['meetingspacereport'], 'method' => 'GET', 'id' => 'meetingspacereport']) }}
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
                                    <th>{{ __('Capacity') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Company') }}</th>
                                    <th>{{ __('Bookings') }}</th>
                                    <th>{{ __('Total Hours') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($spaces as $space)
                                    @php
                                        $bookings = \App\Models\Booking::where('space_id', $space->id)->get();
                                        $companyBookings = [];
                                        $totalBookings = count($bookings);
                                        $totalDuration = 0;
                                        
                                        // Group bookings by company
                                        foreach($bookings as $booking) {
                                            if(!empty($booking->company_id)) {
                                                $companyId = $booking->company_id;
                                                
                                                if(!isset($companyBookings[$companyId])) {
                                                    $company = \App\Models\Company::where('id', $companyId)->first();
                                                    $companyBookings[$companyId] = [
                                                        'name' => !empty($company) ? $company->name : 'Unknown',
                                                        'count' => 0,
                                                        'duration' => 0
                                                    ];
                                                }
                                                
                                                // Calculate duration from total_min
                                                $minutes = !empty($booking->total_min) ? $booking->total_min : 0;
                                                
                                                $companyBookings[$companyId]['count']++;
                                                $companyBookings[$companyId]['duration'] += $minutes;
                                                $totalDuration += $minutes;
                                            }
                                        }
                                        
                                        $chairs = \App\Models\Chair::where('space_id', $space->id)->get();
                                        $showMainRow = true;
                                    @endphp
                                    
                                    @if(count($companyBookings) > 0)
                                        @foreach($companyBookings as $companyId => $companyData)
                                            <tr>
                                                @if($showMainRow)
                                                    <td rowspan="{{ count($companyBookings) }}">{{ !empty($space->name) ? $space->name : '-' }}</td>
                                                    <td rowspan="{{ count($companyBookings) }}">{{ !empty($space->capacity) ? $space->capacity : '-' }}</td>
                                                    
                                                    <td rowspan="{{ count($companyBookings) }}">{{ !empty($space->description) ? $space->description : '-' }}</td>
                                                    @php $showMainRow = false; @endphp
                                                @endif
                                                <td>{{ $companyData['name'] }}</td>
                                                <td>{{ $companyData['count'] > 0 ? $companyData['count'] : '' }}</td>
                                                <td>
                                                    @if($companyData['duration'] > 0)
                                                        @php
                                                            $hours = floor($companyData['duration'] / 60);
                                                            $mins = $companyData['duration'] % 60;
                                                            
                                                            if($hours > 0 && $mins > 0) {
                                                                echo "{$hours} hour" . ($hours > 1 ? 's' : '') . " {$mins} min" . ($mins > 1 ? 's' : '');
                                                            } elseif($hours > 0) {
                                                                echo "{$hours} hour" . ($hours > 1 ? 's' : '');
                                                            } else {
                                                                echo "{$mins} min" . ($mins > 1 ? 's' : '');
                                                            }
                                                        @endphp
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>{{ !empty($space->name) ? $space->name : '-' }}</td>
                                            <td>{{ !empty($space->capacity) ? $space->capacity : '-' }}</td>
                                            <td>{{ !empty($space->price) ? $space->price : '-' }}</td>
                                            <td>{{ !empty($space->description) ? $space->description : '-' }}</td>
                                            <td>{{ !empty($chairs) ? count($chairs) : '-' }}</td>
                                            <td>{{ !empty($space->window) ? $space->window : '-' }}</td>
                                            <td>-</td>
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
