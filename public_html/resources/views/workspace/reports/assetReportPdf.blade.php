<style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        position: relative;
        top: -50px;
    }

    th,
    td {
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

    .rpt-name {
        text-align: center;
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 20px;
        position: relative;
        top: -50px;
    }
</style>
<h2 class="rpt-name">Client Assets Report</h2>
<table>
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
            @if ($asset->assetdetail && @$asset->assetdetail->count())
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
