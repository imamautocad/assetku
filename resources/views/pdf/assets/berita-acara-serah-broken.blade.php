<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Serah Terima Asset Rusak</title>

    <style>
        html,body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            margin: 10;
            padding: 10;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;

        }
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
            position: absolute;
            top: 0;
            right: 0;
        }

        .header-logo img {
            max-width: 120px;
            max-height: 120px;
        }

        .header h2 {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
        }

        hr {
            border: none;
            border-top: 1px solid #000;
            margin: 10px 0;
        }

        /* INFO */
        .info p {
            margin: 4px 0;
        }
        /* paragaph */
        .par p {
            margin: 0 0 6px 0;
            line-height: 1.4;
            text-align: justify;
        }
        .par ul,li{
            margin: 0 0 6px 0;
            line-height: 1.4;
            text-align: justify;
        }
        /* TABLE */
        table.pdf-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 20px;
        }

        .pdf-table th,
        .pdf-table td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: middle;
            word-break: break-word;
        }

        .pdf-table th {
            text-align: center;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .notes-cell {
            font-size: 10px;
            line-height: 1.4;
        }

        /* IMAGE */
        .asset-image {
            margin-top: 20px;
            text-align: center;
        }

        .asset-image img {
            max-width: 400px;
            max-height: 225px;
            border: 1px solid #000;
            padding: 4px;
        }

        /* SIGNATURE */
        table.signature {
            width: 100%;
            margin-top: 100px;
            text-align: center;
        }

        .signature td {
            padding-top: 60px;
            font-weight: bold;
        }
        .info-table {
            font-size: 11px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .info-table .label {
            font-weight: bold;
            width: 80px;
        }

        .info-table .colon {
            width: 10px;
            text-align: center;
        }


    </style>
</head>

<body>

<div class="frame">
    <div class="header-wrapper"> 
        <div class="header-title">
            BERITA ACARA SERAH TERIMA ASSET RUSAK
        </div>

        <div class="header-logo">
            <img src="{{ public_path('img/logo-vasanta.png') }}" alt="Logo">
        </div>
    </div>
    <p>
    {{-- INFO --}}
    <table class="info-table">
        <tr>
            <td class="label">Tanggal</td>
            <td class="colon">:</td>
              {{ $asset->broken_date
                ? \Carbon\Carbon::parse($asset->broken_date)->translatedFormat('d F Y')
                : '-' }}
        </tr>
        <tr>
            <td class="label">Department</td>
            <td class="colon">:</td>
            <td>ICT Department</td>
        </tr>
        <tr>
            <td class="label">Perihal</td>
            <td class="colon">:</td>
            <td>Informasi Kerusakan Laptop</td>
        </tr>
        <tr>
            <td class="label">Entity Asset</td>
            <td class="colon">:</td>
            <td>{{ optional($asset->company)->name ?? '-' }}</td>
        </tr>
    </table>

    <hr>

    {{-- PARAGRAPH --}}
    <div class="par">
    <p>
        Sehubungan dengan kegiatan evaluasi dan inventaris IT secara berkala,
        Departemen IT telah melakukan pemeriksaan terhadap perangkat laptop
        yang terdaftar sebagai asset aktif perusahaan.
        Berdasarkan hasil pemeriksaan teknis, perangkat berikut dinyatakan
        mengalami kerusakan berat dan tidak memungkinkan untuk diperbaiki
        secara ekonomis.
    </p>
    </div>
     <ul>
        <li>
            <strong>
                Hasil pemeriksaan oleh Tim ICT menunjukkan bahwa perangkat mengalami
                kerusakan dan tidak dapat diperbaiki dengan biaya yang layak.
            </strong>
        </li>
    </ul>

    {{-- TABLE --}}
    <table class="pdf-table">
        <thead>
            <tr>
                <th style="width:5%">No</th>
                <th style="width:15%">Category</th>
                <th style="width:20%">CPU</th>
                <th style="width:15%">Manufacture</th>
                <th style="width:8%">Year</th>
                <th style="width:17%">Company</th>
                <th style="width:20%">Notes</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td class="text-center">{{ $asset->model->category->name ?? '-' }}</td>
                <td class="text-center">{{ $asset->cpu ?? '-' }}</td>
                <td class="text-center">{{ $asset->model->manufacturer->name ?? '-' }}</td>
                <td class="text-center">{{ $asset->purchase_date?->format('Y') ?? '-' }}</td>
                <td class="text-center">{{ $asset->company->name ?? '-' }}</td>
                <td class="notes-cell">{{ $asset->notes ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- IMAGE --}}
    <div class="asset-image">
        @if($asset->image)
            <img src="{{ public_path('uploads/assets/'.$asset->image) }}" alt="Asset Image">
        @else
            <em></em>
        @endif
    </div>

    {{-- SIGNATURE --}}
    @php
        $createdBy = auth()->user();
        $reviewedBy = optional($createdBy->manager);
        $approvedBy = optional($reviewedBy?->manager);
    @endphp

    <table class="signature">
        <tr>
            <td width="33%">Dibuat Oleh</td>
            <td width="33%">Direview Oleh</td>
            <td width="33%">Diketahui Oleh</td>
        </tr>
        <tr>
            <td>{{ $createdBy->name }}</td>
            <td>{{ $reviewedBy->name ?? '-' }}</td>
            <td>{{ $approvedBy->name ?? '-' }}</td>
        </tr>
    </table>

</div>

</body>
</html>
