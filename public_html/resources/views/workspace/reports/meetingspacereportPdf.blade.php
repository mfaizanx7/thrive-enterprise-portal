<style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        position: relative;
        top: -50px;
    }

    th, td {
        border: 1px solid #aaa;
        padding: 4px;
        text-align: center;
    }

    th {
        background-color: #dad9d9;
    }

    .branch-title {
        font-weight: bold;
        text-align: left;
        /* background-color: #ddd; */
    }

    .total-row {
        font-weight: bold;
        background-color: #f9f9f9;
    }
    .rpt-name{
        text-align: center;
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 20px;
        position: relative;
        top:-50px;
    }
</style>
<h2 class="rpt-name">Meeting Spaces Report</h2>
<table>
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