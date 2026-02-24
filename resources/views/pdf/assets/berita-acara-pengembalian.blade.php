<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara Pengembalian Asset</title>

    <style>
        /* HEADER */
        .header-wrapper {
            position: relative;
            text-align: center;
            margin-bottom: 10px;
        }

        .header-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header-logo {
            margin-bottom: 500px;
            position: absolute;
            top: 0;
            right: 0;
        }

        .header-logo img {
            max-width: 120px;
            max-height: 120px;
        }
         body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .frame {
            border: 1px solid #000;
        }

        .frame td {
            padding: 14px;
        }

        .header {
            text-align: center;
            font-weight: bold;
        }

        .header h2 {
            margin: 0;
            font-size: 14px;
        }

        .header h3 {
            margin: 8px 0 15px 0;
            font-size: 13px;
            text-decoration: underline;
        }

        .table-bordered td {
            border: 1px solid #000;
            padding: 6px;
        }

        .label {
            width: 170px;
            font-weight: bold;
        }

        .section-title {
            text-align: center;
            font-weight: bold;
            border: 1px solid #000;
            padding: 6px;
            margin-top: 10px;
        }

        .notes {
            font-size: 10px;
            margin-top: 10px;
        }

        .footer {
            margin-top: 25px;
            text-align: center;
        }

        .qr-box {
            display: inline-block;
            text-align: center;
        }

        .qr-box img {
            width: 90px;
            height: 90px;
        }

        .qr-caption {
            font-size: 9px;
            margin-top: 4px;
        }
        .br-no {
            margin-top: 30px;
        }
</style>

</head>

<body>

<table class="frame">
<tr> 
<td>

    {{-- HEADER --}}
    <div class="header-wrapper"> 
        <div class="header-title"> 
            BERITA ACARA PENGEMBALIAN ASSET
        </div>

        <div class="header-logo">
            <img src="{{ public_path('img/logo-vasanta.png') }}" alt="Logo">
        </div>
    </div>    
    {{-- NO --}}
    <div class="br-no">
    <br>

            {{-- <td class="label">NO</td> --}}
        
                NO :
                {{ $asset->asset_tag }}/IT/{{ optional($asset->company)->name ?? 'COMPANY' }}/BAST/{{ now()->format('m') }}/{{ now()->format('Y') }}
    </br>
    </div>
    <br>

    {{-- PARAGRAPH --}}
    <p>
        Pada hari ini {{ now()->translatedFormat('l') }}
        tanggal {{ now()->translatedFormat('d F Y') }},
        telah dilakukan serah terima barang inventaris berupa
        <b>1 (satu) unit Notebook</b> dengan spesifikasi sebagai berikut:
    </p>

    {{-- ITEM --}}
    <table class="table-bordered">
        <tr>
            <td class="label">Notebook</td>
            <td>
                {{ optional($asset->model->manufacturer)->name ?? '-' }}
                {{ optional($asset->model)->name ?? '-' }}
            </td>
        </tr>
        <tr>
            <td class="label">Asset Tag / Serial</td>
            <td>{{ $asset->asset_tag }} / {{ $asset->serial }}</td>
        </tr>
    </table>

    {{-- ADDITIONAL --}}
    <div class="section-title">Additional</div>
    <table class="table-bordered">
        <tr>
            <td>
                @php
                    $accessories = collect();

                    if ($checkinUser) {
                        $accessories = \DB::table('accessories_checkout')
                            ->join('accessories', 'accessories.id', '=', 'accessories_checkout.accessory_id')
                            ->where('accessories_checkout.assigned_to', $checkinUser->id)
                            ->pluck('accessories.name');
                    }
                @endphp

                {{ $accessories->isNotEmpty() ? $accessories->implode(', ') : '-' }}
            </td>
        </tr>
    </table>

    {{-- AKSES USER --}}
    <div class="section-title">Akses User</div>
    <table class="table-bordered">
        <tr>
            <td class="label">IFCA User</td>
             <td>{{ optional($checkinUser)->ifca_user ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Email</td>
            <td>{{ optional($checkinUser)->email ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">VPN</td>
            <td>{{ optional($checkinUser)->vpn_user ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Intranet User</td>
            <td>{{ optional($checkinUser)->intranet_user ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">License</td>
            <td>
            @php
                $licenses = collect();
                if ($checkinUser) {
                    $licenses = \DB::table('license_seats')
                        ->join('licenses', 'licenses.id', '=', 'license_seats.license_id')
                        ->where('license_seats.assigned_to', $checkinUser->id)
                        ->pluck('licenses.name');
                }
            @endphp
            {{ $licenses->isNotEmpty() ? $licenses->implode(', ') : '-' }}
            </td>
        </tr>
    </table>

    {{-- NOTES --}}
    <div class="notes">
    @if($checkinLog && $checkinLog->note)
        <p><b>Catatan Pengembalian:</b><br>{{ $checkinLog->note }}</p>
    @endif
        <p>* Segala bentuk kerusakan dan kehilangan menjadi tanggung jawab pengguna.</p>
        <p>* Software tambahan atau tidak berlisensi tidak disediakan oleh {{ optional($asset->company)->name ?? 'COMPANY' }}.</p>
        <p>* IT hanya menerima serah terima unit secara fisik.</p>
    </div>
 
    {{-- SIGNATURE --}} 
    {{-- <table class="signature" width="100%">
    <tr>
        <td width="33%">Jakarta, {{ now()->translatedFormat('d F Y') }}</td>
        <td width="33%" align="center">

        <img
            src="{{ route('bast.qr', $checkinLog->id) }}"
            width="90"
        >
        <div style="font-size:9px;text-align:center;">
            Scan untuk verifikasi
        </div>
        </td>
        <td width="33%"></td>
    </tr>

    <tr>
        <td>Yang Menyerahkan</td>
        <td>Penerima</td>
        <td>Mengetahui</td>
    </tr>

    <tr>
        <td>{{ optional($checkinUser)->name ?? '-' }}</td>
        <td>{{ auth()->user()->name }}</td>
        <td>ICT Department</td>
    </tr>
</table> --}}
    {{-- QR VERIFICATION (BOTTOM CENTER) --}}
    <div class="footer">
        <div class="qr-box">
            {{-- <img src="{{ route('bast.qr', $checkinLog->id) }}"> --}}
            <img src="data:image/svg+xml;base64,{{ base64_encode(
                QrCode::format('svg')->size(160)->generate($verifyUrl)
            ) }}">
            <div class="qr-caption">
                Scan untuk verifikasi keaslian dokumen
            </div>
        </div>
    </div>
</body>
</html> 
