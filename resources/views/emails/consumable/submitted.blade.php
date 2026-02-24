<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Permintaan Barang Consumable</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; font-size: 14px; line-height: 1.5;">

    <!-- Header Logo + Judul -->
    <div style="text-align: center; margin-bottom: 20px;">
        @if (!empty($logo))
            <img src="{{ $logo }}" alt="Logo" style="width: 120px; margin-bottom: 5px;">
        @endif
        <h2 style="margin: 0; font-size: 18px;">VASANTA - ICT ASSET MANAGEMENT</h2>
        <hr style="margin-top: 10px; border: 0; border-top: 1px solid #ddd;">
    </div>

    <p>Kepada Yth. <strong>Tim General Affairs</strong>,</p>
    <p>Telah dibuat permintaan barang consumable dengan detail sebagai berikut:</p>

    <!-- Informasi Utama -->
    <table style="margin-bottom: 15px; width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 150px;"><strong>Nama Pemohon</strong></td>
            <td style="width: 10px;">:</td>
            <td>{{ $order->user->name }}</td>
        </tr>
        <tr>
            <td><strong>Departemen</strong></td>
            <td>:</td>
            <td>{{ $order->department->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Permintaan</strong></td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d F Y') }}</td>
        </tr>
        <tr>
            <td><strong>No. Request</strong></td>
            <td>:</td>
            <td>{{ $order->no_req }}</td>
        </tr>
        @if ($order->notes)
        <tr>
            <td><strong>Catatan</strong></td>
            <td>:</td>
            <td>{{ $order->notes }}</td>
        </tr>
        @endif
    </table>

    <!-- Tabel Detail Item -->
    <strong>🗂 Detail Item</strong>
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <thead>
            <tr style="background: #f5f5f5;">
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Nama Item</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Qty</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Kategori</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->details as $detail)
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $detail->consumable->name }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $detail->qty }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $detail->consumable->category->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p>
        Mohon untuk ditindaklanjuti sesuai prosedur pengadaan barang consumable.<br>
        Jika barang tersedia, mohon konfirmasi jadwal pengambilan atau pengiriman.
    </p>

    <p>Terima kasih atas perhatian dan kerjasamanya.</p>

    <br>
    <p>Hormat kami,<br>
    <strong>{{ $order->user->name }}</strong><br>
    {{ $order->department->name ?? 'Departemen Terkait' }}</p>

</body>
</html>
