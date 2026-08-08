<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Terima Kas - {{ $pembayaran->kode_pembayaran }}</title>
    <style>
        @page {
            margin: 10mm 12mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10.5px;
            color: #111;
            line-height: 1.3;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .logo-img {
            height: 48px;
            width: auto;
        }
        .institution-title {
            font-size: 11px;
            font-weight: bold;
            color: #cc0000;
        }
        .institution-info {
            font-size: 8.5px;
            color: #444;
            margin-top: 2px;
        }
        .doc-title-box {
            text-align: right;
            vertical-align: top;
        }
        .doc-title {
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .doc-number {
            font-size: 9px;
            color: #555;
            margin-top: 3px;
        }
        .divider {
            border-bottom: 1.5px solid #111;
            margin-bottom: 12px;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .content-table td {
            vertical-align: top;
            padding: 3px 0;
        }
        .label {
            width: 18%;
            color: #333;
        }
        .separator {
            width: 2%;
        }
        .value {
            width: 30%;
            font-weight: bold;
        }
        .data-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            border-top: 1px solid #111;
            border-bottom: 1px solid #111;
        }
        .data-grid th, .data-grid td {
            padding: 6px 4px;
            text-align: left;
        }
        .data-grid th {
            font-size: 8.5px;
            text-transform: uppercase;
            border-bottom: 1px solid #ccc;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .signature-table td {
            text-align: center;
            vertical-align: bottom;
            height: 45px;
        }
        .footer-note {
            position: absolute;
            bottom: 0;
            left: 0;
            font-size: 8px;
            color: #555;
            font-style: italic;
        }
    </style>
</head>
<body>

    <!-- Header Kop Lembaga & Logo INSTIKI -->
    <table class="header-table">
        <tr>
            <td style="width: 12%; vertical-align: middle;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" class="logo-img" alt="Logo INSTIKI">
                @else
                    <div style="font-weight: bold; color: #cc0000; font-size: 14px;">INSTIKI</div>
                @endif
            </td>
            <td style="width: 53%; vertical-align: middle; padding-left: 8px;">
                <div class="institution-title">INSTITUT BISNIS DAN TEKNOLOGI INDONESIA</div>
                <div class="institution-info">
                    Jl. Tukad Pakerisan No. 97 Denpasar, Bali<br>
                    Telp. 0361-256995 Fax. 0361-246875
                </div>
            </td>
            <td style="width: 35%;" class="doc-title-box">
                <div class="doc-title">BUKTI TERIMA KAS</div>
                <div class="doc-number">No. Bukti: {{ $pembayaran->kode_pembayaran }}</div>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Rincian Identitas Pembayar -->
    <table class="content-table">
        <tr>
            <td class="label">Nama Penghuni</td>
            <td class="separator">:</td>
            <td class="value">{{ $pembayaran->user->nama ?? '-' }}</td>

            <td class="label">Tahun Akademik</td>
            <td class="separator">:</td>
            <td class="value">{{ date('Y') }}/{{ date('Y') + 1 }}</td>
        </tr>
        <tr>
            <td class="label">NIM / NIK</td>
            <td class="separator">:</td>
            <td class="value">{{ $pembayaran->user->nim_nik ?? '-' }}</td>

            <td class="label">Tanggal Bayar</td>
            <td class="separator">:</td>
            <td class="value">{{ \Carbon\Carbon::parse($pembayaran->updated_at)->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Kamar / Blok</td>
            <td class="separator">:</td>
            <td class="value">
                Kamar {{ $pembayaran->reservasi->kamar->nomor_kamar ?? '-' }} 
                (Blok {{ $pembayaran->reservasi->kamar->blok ?? '-' }})
            </td>

            <td class="label">Metode Bayar</td>
            <td class="separator">:</td>
            <td class="value">{{ $pembayaran->metode_pembayaran }}</td>
        </tr>
    </table>

    <!-- Rincian Jumlah Pembayaran -->
    <table class="data-grid">
        <thead>
            <tr>
                <th style="width: 45%;">JENIS PEMBAYARAN</th>
                <th style="width: 25%;">PERIODE</th>
                <th style="width: 30%; text-align: right;">JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Sewa Kamar Asrama</td>
                <td>{{ \Carbon\Carbon::parse($pembayaran->created_at)->format('F Y') }}</td>
                <td style="text-align: right; font-weight: bold;">
                    Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
                </td>
            </tr>
            <tr style="border-top: 1px solid #eee; font-weight: bold;">
                <td colspan="2" style="text-align: right; padding-top: 6px;">TOTAL PEMBAYARAN:</td>
                <td style="text-align: right; padding-top: 6px; color: #cc0000; font-size: 11px;">
                    Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Area Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td style="width: 33%;">
                Penyetor
                <br><br><br><br>
                ( {{ $pembayaran->user->nama ?? '________________' }} )
            </td>
            <td style="width: 33%;"></td>
            <td style="width: 34%;">
                Diterima Oleh Keuangan / WR II
                <br><br><br><br>
                ( ___________________ )
            </td>
        </tr>
    </table>

    <!-- Catatan Lembar Rangkap -->
    <div class="footer-note">
        Putih untuk Mahasiswa, Merah untuk WR II, Kuning untuk Keuangan.<br>
        * Uang yang sudah dibayarkan tidak dapat ditarik kembali.
    </div>

</body>
</html>