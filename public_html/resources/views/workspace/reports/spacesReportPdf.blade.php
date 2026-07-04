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
<h2 class="rpt-name">Spaces Report</h2>
<table>
    <thead>
        <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Capacity') }}</th>
            <th>{{ __('Price') }}</th>
            <th>{{ __('Description') }}</th>
            <th>{{ __('SpaceType') }}</th>
            <th>{{ __('Chairs') }}</th>
            <th>{{ __('Meeting') }}</th>
            <th>{{ __('Window') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($spaces as $space)
            <tr>
                <td>
                    {{ !empty($space->name) ? $space->name : '-' }}
                </td>
                <td>
                    {{ !empty($space->capacity) ? $space->capacity : '-' }}
                </td>
                <td>
                    {{ !empty($space->price) ? $space->price : '-' }}
                </td>
                <td>
                    {{ !empty($space->description) ? $space->description : '-' }}
                </td>
                <td>
                    {{ !empty($space->type) ? $space->type->name : '-' }}
                </td>
                <td>
                    @php
                    $chairs = \App\Models\Chair::where('space_id', $space->id)->get();
                    @endphp
                    {{ !empty($chairs) ? count($chairs) : '-' }}
                </td>
                <td>
                    {{ !empty($space->meeting) ? $space->meeting : '-' }}
                </td>
                <td>
                    {{ !empty($space->window) ? $space->window : '-' }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>