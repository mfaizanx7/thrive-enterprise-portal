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
<h2 class="rpt-name">Contracts Report</h2>
<table>
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