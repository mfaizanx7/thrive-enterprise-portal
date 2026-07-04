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
<h2 class="rpt-name">Vacant spaces Report</h2>
<table>
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