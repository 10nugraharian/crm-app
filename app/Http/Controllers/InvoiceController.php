<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Quotation;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::with('quotation.lead')->orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $invoices]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'quotation_id' => 'required|uuid',
            'persentase_dp' => 'required|numeric|min:0|max:100',
        ]);

        $quotation = Quotation::findOrFail($validated['quotation_id']);

        if ($quotation->status_approval !== 'APPROVED') {
            return response()->json(['error' => 'Quotation belum di-approve'], 400);
        }

        $statusApproval = $validated['persentase_dp'] >= 50 ? 'APPROVED' : 'PENDING_FINANCE';

        $invoice = Invoice::create([
            'quotation_id' => $quotation->id,
            'total_amount' => $quotation->total_amount,
            'persentase_dp' => $validated['persentase_dp'],
            'status_approval' => $statusApproval,
        ]);

        $msg = $statusApproval === 'APPROVED' 
            ? 'Proforma Invoice Terbit (Auto-Approved)' 
            : 'Menunggu Approval Finance';

        return response()->json([
            'message' => $msg,
            'data' => $invoice
        ], 201);
    }

    public function downloadPdf($id)
    {
        $invoice = Invoice::with(['quotation.lead', 'quotation.items.layanan'])->findOrFail($id);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', compact('invoice'));
        
        return $pdf->download('Invoice-' . $invoice->quotation->no_quotation . '.pdf');
    }
}
