<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation - {{ $quotation->no_quotation }}</title>
    <style>
        body { font-family: 'Tahoma', sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #111; }
        .details { margin-bottom: 30px; }
        .details table { width: 100%; }
        .details td { padding: 5px 0; vertical-align: top; }
        .details .label { width: 150px; font-weight: bold; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .items-table th { background-color: #f9f9f9; }
        .items-table td.number { text-align: right; }
        .total-row { font-weight: bold; background-color: #f1f1f1; }
        .footer { text-align: center; font-size: 10px; color: #777; position: absolute; bottom: 10px; width: 100%; }
        .page-break { page-break-before: always; }
        .client-logos { text-align: center; margin-top: 20px; }
        .client-logos h2 { margin-bottom: 20px; }
        .logo-grid { width: 100%; text-align: center; }
        .logo-item { display: inline-block; width: 30%; margin-bottom: 20px; }
        .logo-item img { max-width: 130px; max-height: 80px; display: block; margin: 0 auto; }
    </style>
</head>
<body>

    <div class="header">
        <h1>QUOTATION</h1>
        <p>No: {{ $quotation->no_quotation }}</p>
    </div>

    <div class="details">
        <table>
            <tr>
                <td class="label">Tanggal</td>
                <td>: {{ $quotation->created_at->format('d M Y') }}</td>
                <td class="label">Kepada</td>
                <td>: {{ $quotation->lead->nama_perusahaan }}</td>
            </tr>
            <tr>
                <td class="label">Sales Person</td>
                <td>: {{ $quotation->sales->name }}</td>
                <td class="label">Alamat</td>
                <td>: {{ $quotation->lead->alamat ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Deskripsi Layanan</th>
                <th>Qty</th>
                <th class="number">Harga Satuan</th>
                <th class="number">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->layanan->nama_layanan }}</td>
                <td>{{ $item->qty }}</td>
                <td class="number">Rp {{ number_format($item->harga_jual_input, 0, ',', '.') }}</td>
                <td class="number">Rp {{ number_format($item->harga_jual_input * $item->qty, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="number">GRAND TOTAL</td>
                <td class="number">Rp {{ number_format($quotation->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <p>Terima kasih atas kepercayaan Anda kepada PT. Esdea Assistance Management.</p>
    
    <div class="page-break"></div>
    
    <div class="client-logos">
        <h2>Mitra dan Klien Kami</h2>
        <div class="logo-grid">
            @for ($i = 1; $i <= 15; $i++)
                @php 
                    $logoPath = public_path('images/clients/logo-client-'.$i.'.png'); 
                    $base64 = '';
                    if (file_exists($logoPath)) {
                        $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                        $data = file_get_contents($logoPath);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    }
                @endphp
                @if ($base64)
                    <div class="logo-item">
                        <img src="{{ $base64 }}" alt="Client Logo {{ $i }}" style="max-width: 130px; max-height: 80px;">
                    </div>
                @endif
            @endfor
        </div>
    </div>

    <div class="footer">
        <p>PT. Esdea Assistance Management - Quotation Document Generated Automatically.</p>
    </div>

</body>
</html>
