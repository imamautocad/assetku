<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Verifikasi Berita Acara</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body {
    font-family: Arial, Helvetica, sans-serif;
    background: #f4f6f9;
    padding: 30px 15px;
}
.card {
    max-width: 620px;
    margin: auto;
    background: #fff;
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(0,0,0,.1);
}
.header {
    background: #28a745;
    color: #fff;
    padding: 16px;
    font-size: 18px;
    font-weight: bold;
    text-align: center;
}
.body {
    padding: 20px;
}
.row {
    display: flex;
    margin-bottom: 10px;
}
.label {
    width: 180px;
    font-weight: bold;
    color: #555;
}
.value {
    flex: 1;
}
.footer {
    background: #fafafa;
    font-size: 11px;
    color: #777;
    text-align: center;
    padding: 12px;
}
.valid {
    text-align: center;
    font-size: 16px;
    font-weight: bold;
    color: #28a745;
    margin-bottom: 15px;
}
</style> 
</head>

<body>

<div class="card">
    <div class="header">✔ Dokumen Terverifikasi</div>

    <div class="body">
        <div class="valid">Berita Acara Sah & Valid</div>

        <div class="row">
            <div class="label">Asset Perusahaan</div>
            <div class="value">
                :
                 {{ $asset->company->name ?? '-' }}</div>
        </div>

        <div class="row">
            <div class="label">Asset Tag</div>
            <div class="value">
                :
                 {{ $asset->asset_tag }}</div>
        </div>

        <div class="row">
            <div class="label">Model / Serial</div>
            <div class="value">
                :
                 {{ $asset->model->name ?? '-' }}<br>
                SN: {{ $asset->serial ?? '-' }}
            </div>
        </div>

        <div class="row">
            <div class="label">Jenis Aksi</div>
            <div class="value">
                : 
                {{ strtoupper($log->action_type) }}</div>
        </div>

        <div class="row">
            <div class="label">Tanggal Aksi</div>
            <div class="value">
                :
                {{ \Carbon\Carbon::parse($log->action_date ?? $log->created_at)->format('d F Y, H:i') }}
            </div>
        </div>

        <div class="row">
            <div class="label">Digunakan Oleh</div>
            <div class="value">
                :
                 {{ optional($user)->first_name }} {{ optional($user)->last_name }}
            </div>
        </div>

        <div class="row">
            <div class="label">Catatan</div>
            @if($log->note)
            <div class="value">
            :
                {{ $log->note }}</p>
            </div>
            @endif
        </div>
    </div>

    <div class="footer">
        Verifikasi dilakukan melalui QR Code sebagai tanda tangan digital resmi.<br>
        Sistem Asset Management
    </div>
</div>

</body>
</html>

{{-- <h2>✔ Dokumen Terverifikasi</h2>

<p><b>Perusahaan:</b> {{ $asset->company->name }}</p>
<p><b>Asset Tag:</b> {{ $asset->asset_tag }}</p>
<p><b>Model / Serial:</b>
    {{ optional($asset->model->manufacturer)->name }}
    {{ $asset->model->name }} /
    {{ $asset->serial ?? '-' }}
</p>

<p><b>Jenis Aksi:</b> {{ strtoupper($log->action_type) }}</p>
<p><b>Tanggal Aksi:</b> {{ $log->created_at->translatedFormat('d F Y, H:i') }}</p>

<p><b>Digunakan Oleh:</b>
    {{ optional($user)->first_name }}
    {{ optional($user)->last_name }}
</p>

@if($log->note)
<p><b>Catatan:</b> {{ $log->note }}</p>
@endif --}}