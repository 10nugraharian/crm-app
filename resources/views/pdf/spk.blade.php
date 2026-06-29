<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SPK - {{ $spk->no_spk }}</title>
    <style>
        body { font-family: 'Tahoma', sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #111; }
        .details { margin-bottom: 30px; }
        .details table { width: 100%; }
        .details td { padding: 5px 0; vertical-align: top; }
        .details .label { width: 150px; font-weight: bold; }
        .content-box { border: 1px solid #ddd; padding: 20px; margin-bottom: 30px; background-color: #fcfcfc; }
        .footer { text-align: center; font-size: 12px; color: #777; position: absolute; bottom: 30px; width: 100%; }
        .signature-box { width: 100%; margin-top: 50px; }
        .signature-box td { text-align: center; width: 50%; padding-top: 80px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>SURAT PERINTAH KERJA (SPK)</h1>
        <p>No: {{ $spk->no_spk }}</p>
    </div>

    <div class="details">
        <table>
            <tr>
                <td class="label">Tanggal SPK</td>
                <td>: {{ $spk->created_at->format('d M Y') }}</td>
                <td class="label">Vendor</td>
                <td>: {{ $spk->vendor->nama_vendor }}</td>
            </tr>
            <tr>
                <td class="label">Project Ref.</td>
                <td>: {{ $spk->project->nama_project ?? '-' }}</td>
                <td class="label">Client Akhir</td>
                <td>: {{ $spk->project->invoice->quotation->lead->nama_perusahaan ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="content-box">
        <h3>Detail Pekerjaan:</h3>
        <p>Dengan ini PT. Esdea Assistance Management memberikan perintah kerja kepada <strong>{{ $spk->vendor->nama_vendor }}</strong> untuk melaksanakan pekerjaan terkait project di atas.</p>
        
        <h3>Nilai Pekerjaan:</h3>
        <p style="font-size: 18px; font-weight: bold;">Rp {{ number_format($spk->nilai_pekerjaan, 0, ',', '.') }}</p>

        <p>Pekerjaan harus dilaksanakan sesuai dengan standar operasional dan kualitas yang telah disepakati bersama.</p>
    </div>

    <table class="signature-box">
        <tr>
            <td>
                <strong>Pemberi Kerja</strong><br>
                PT. Esdea Assistance Management
                <br><br><br><br>
                ( .................................... )
            </td>
            <td>
                <strong>Penerima Kerja</strong><br>
                {{ $spk->vendor->nama_vendor }}
                <br><br><br><br>
                ( .................................... )
            </td>
        </tr>
    </table>

    <div class="footer">
        <p>PT. Esdea Assistance Management - SPK Document Generated Automatically.</p>
    </div>

</body>
</html>
