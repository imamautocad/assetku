<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Consumable Stock Print</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        h1, h3 {
            margin: 0;
            padding: 0;
        }

        hr {
            margin: 6px 0 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 12px;
        }

        th, td {
            border: 1px solid #555;
            padding: 5px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .strong {
            font-weight: bold;
        }

        /* COLUMN WIDTH */
        .col-user   { width: 15%; }
        .col-dept   { width: 15%; }
        .col-notes  { width: 25%; }
        .col-small  { width: 7%;  text-align: center; }
        .col-balance{ width: 9%;  text-align: center; }

        /* WARNING */
        .warning {
            padding: 6px;
            background: #fce4e4;
            border: 1px solid #e99;
            color: #a00;
            margin-bottom: 10px;
            font-size: 10px;
        }

        /* PAGE BREAK */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

@foreach($items as $item)

    {{-- HEADER --}}
    <div>
        <h1 class="text-center">Consumable Usage Report</h1>
        <hr>
    </div>

    {{-- WARNING STOCK --}}
    @if($item['current_stock'] == 0)
        <div class="warning">
            <strong>Warning!</strong>
            Real stock for <strong>{{ $item['consumable']->name }}</strong> is <strong>0</strong>.
        </div>
    @endif

    {{-- ITEM INFO --}}
    <h3>Item Information</h3>
    <table>
        <tr>
            <th width="25%">Item Name</th>
            <td>{{ $item['consumable']->name }}</td>
        </tr>
        <tr>
            <th>Category</th>
            <td>{{ $item['consumable']->category->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Real / Current Stock</th>
            <td>{{ $item['current_stock'] }}</td>
        </tr>
    </table>

    {{-- HISTORY --}}
    <h3>Consumable Usage History</h3>
    <table>
        <thead>
            <tr>
                <th width="20%">No Req</th>
                <th width="11%">Date</th>
                <th class="col-user">Name</th>
                <th class="col-dept">Department</th>
                <th class="col-notes">Notes</th>
                <th class="col-small">In</th>
                <th class="col-small">Out</th>
                <th class="col-balance">Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($item['stockRows'] as $row)
                <tr>
                    <td>{{ $row['no_req'] }}</td>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['user'] }}</td>
                    <td>{{ $row['dept'] }}</td>
                    <td class="col-notes">{{ $row['notes'] }}</td>
                    <td class="col-small">{{ $row['in'] }}</td>
                    <td class="col-small">{{ $row['out'] }}</td>
                    <td class="col-balance strong">{{ $row['balance'] }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th colspan="5" class="text-center">Total</th>
                <th>{{ $item['totals']['in'] }}</th>
                <th>{{ $item['totals']['out'] }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    {{-- PAGE BREAK --}}
    @if(!$loop->last)
        <div class="page-break"></div>
    @endif

@endforeach

</body>
</html>
