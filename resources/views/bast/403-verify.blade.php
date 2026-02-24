<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumen Tidak Dapat Diverifikasi</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #f4f6f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI",
                         Roboto, Helvetica, Arial, sans-serif;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            width: 100%;
            max-width: 520px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.08);
            padding: 28px 26px;
            text-align: center;
        }

        .icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background: #dc3545;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 36px;
        }

        h1 {
            font-size: 20px;
            margin: 10px 0;
            font-weight: 600;
        }

        p {
            font-size: 14px;
            line-height: 1.6;
            margin: 8px 0;
            color: #555;
        }

        .alert {
            margin-top: 18px;
            background: #f8f9fa;
            border-left: 4px solid #dc3545;
            padding: 14px 16px;
            text-align: left;
            font-size: 13px;
            border-radius: 4px;
        }

        .alert ul {
            padding-left: 18px;
            margin: 0;
        }

        .alert li {
            margin-bottom: 6px;
        }

        .footer {
            margin-top: 22px;
            font-size: 12px;
            color: #888;
            line-height: 1.5;
        }

        /* Desktop refinement */
        @media (min-width: 768px) {
            h1 {
                font-size: 22px;
            }

            p {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>

<div class="card">
    <div class="icon">⛔</div>

    <h1>Dokumen Tidak Dapat Diverifikasi</h1>

    <p>
        QR Code pada dokumen ini <b>sudah diverifikasi sebelumnya</b>
        atau <b>tidak lagi berlaku</b>.
    </p>

    <div class="alert">
        <ul>
            <li>QR Code BAST hanya dapat diverifikasi <b>satu kali</b></li>
            <li>Masa Berlaku QRCode hanya <b> 30 hari setelah penyerahan asset</b></li>
            <li>Setiap pemindaian tercatat secara sistem</li>
            <li>Duplikasi atau penyalahgunaan akan ditolak</li>
        </ul>
    </div>

    <p>
        Untuk klarifikasi lebih lanjut, silakan hubungi
        <b>Tim ICT</b>.
    </p>

    <div class="footer">
        Sistem Manajemen Asset Vasanta Group<br>
        {{ now()->format('Y') }}
    </div>
</div>

</body>
</html>
