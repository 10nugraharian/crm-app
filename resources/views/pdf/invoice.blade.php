<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $invoice->id }}</title>
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
        .footer { text-align: center; font-size: 12px; color: #777; position: absolute; bottom: 30px; width: 100%; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; color: #fff; }
        .badge-unpaid { background-color: #f44336; }
        .badge-paid { background-color: #4CAF50; }
    </style>
</head>
<body>

    <div class="header">
        <h1>INVOICE</h1>
        <p>Ref Quotation: {{ $invoice->quotation->no_quotation }}</p>
    </div>

    <div class="details">
        <table>
            <tr>
                <td class="label">Tanggal Terbit</td>
                <td>: {{ $invoice->created_at->format('d M Y') }}</td>
                <td class="label">Kepada</td>
                <td>: {{ $invoice->quotation->lead->nama_perusahaan }}</td>
            </tr>
            <tr>
                <td class="label">Status Pembayaran</td>
                <td>: 
                    <span class="badge {{ $invoice->status_pembayaran === 'UNPAID' ? 'badge-unpaid' : 'badge-paid' }}">
                        {{ $invoice->status_pembayaran }}
                    </span>
                </td>
                <td class="label">Persentase DP</td>
                <td>: {{ number_format($invoice->persentase_dp, 0) }}%</td>
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
            @foreach($invoice->quotation->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->layanan->nama_layanan }}</td>
                <td>{{ $item->qty }}</td>
                <td class="number">Rp {{ number_format($item->harga_jual_input, 0, ',', '.') }}</td>
                <td class="number">Rp {{ number_format($item->harga_jual_input * $item->qty, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="number">TOTAL KESELURUHAN</td>
                <td class="number">Rp {{ number_format($invoice->quotation->total_amount, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row" style="background-color: #e8f5e9;">
                <td colspan="4" class="number">TAGIHAN SAAT INI ({{ number_format($invoice->persentase_dp, 0) }}%)</td>
                <td class="number">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <p>Mohon segera melakukan pembayaran sesuai dengan jumlah tagihan saat ini. Terima kasih.</p>

    <div class="footer">
        <p>PT. Esdea Assistance Management - Invoice Document Generated Automatically.</p>
    </div>

</body>
</html>
