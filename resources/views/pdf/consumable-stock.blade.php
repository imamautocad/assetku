<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>PDF Penambahan Stock Consumable</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #f0f0f0; }
        .header { text-align: center; margin-bottom: 20px; }
        .info-table td { border: none; padding: 3px 0; }
        .info-table { width: 100%; margin-bottom: 15px; }
        .label { width: 150px; }
        .separator { width: 10px; }
        .signature-table { width: 100%; margin-top: 40px; text-align: center; }
        .signature-table td { border: none; height: 100px; vertical-align: bottom; }
    </style>
</head>
<body>
    <p></p>
        <div class="header">@if (!empty($logo)) <img style="width: 120px;" src="{{ $logo }}" /> @endif
        <h3>Penambahan Stock Consumable</h3>
    <hr /></div>

    <!-- Informasi utama dibuat tabel agar titik dua sejajar -->
    <table class="info-table">
        <tr>
            <td class="label"><strong>No. Request</strong></td>
            <td class="separator">:</td>
            <td>{{ $order->no_req }}</td>
        </tr>
        <tr>
            <td class="label"><strong>No. PO</strong></td>
            <td class="separator">:</td>
            <td>{{ $order->no_po }}</td>
        </tr>
        <tr> 
            <td class="label"><strong>Nama Pembuat</strong></td>
            <td class="separator">:</td>
            <td>{{ $order->user->name }}</td>
        </tr>
        <tr>
            <td class="label"><strong>Departemen</strong></td>
            <td class="separator">:</td>
            <td>{{ $order->department->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label"><strong>Tanggal</strong></td>
            <td class="separator">:</td>
            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label"><strong>Notes</strong></td>
            <td class="separator">:</td>
            <td>{{ $order->notes ?? '-' }}</td>
        </tr>
    </table>

    <h4>Detail Item</h4>
    <table>
        <thead>
            <tr>
                <th>Nama Item</th>
                <th>Qty</th>
                <th>Kategori</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->details as $detail)
            <tr>
                <td>{{ $detail->consumable->name }}</td>
                <td>{{ $detail->qty }}</td>
                <td>{{ $detail->consumable->category->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td><strong>Tim GA</strong></td>
            <td><strong>Leader</strong></td>
        </tr>
        <tr>
            <td>___________________________<br>{{ $order->user->name }}</td>
            <td>___________________________<br>Leader</td>
        </tr>
    </table>
</body>
</html>
