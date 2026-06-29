<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\Layanan;
use App\Models\Lead;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $quotations = Quotation::with(['lead', 'sales', 'items.layanan'])->orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $quotations]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'lead_id' => 'required|uuid',
            'items' => 'required|array|min:1',
            'items.*.layanan_id' => 'required|uuid',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga_jual_input' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $isOutsideLimit = false;
            $totalAmount = 0;
            $processedItems = [];

            foreach ($validated['items'] as $item) {
                $layanan = Layanan::findOrFail($item['layanan_id']);
                
                $margin = $item['harga_jual_input'] - $layanan->harga_pokok;
                
                // Rule A / B Logic
                if ($item['harga_jual_input'] < $layanan->harga_pokok || $margin > 500000) {
                    $isOutsideLimit = true;
                }

                $subtotal = $item['harga_jual_input'] * $item['qty'];
                $totalAmount += $subtotal;

                $processedItems[] = [
                    'layanan_id' => $item['layanan_id'],
                    'qty' => $item['qty'],
                    'harga_jual_input' => $item['harga_jual_input'],
                    'refund_sales_margin' => $margin * $item['qty'],
                ];
            }

            $statusApproval = $isOutsideLimit ? 'PENDING_FINANCE' : 'APPROVED';

            $noQuotation = 'Q-' . date('Ymd') . '-' . str_pad(Quotation::count() + 1, 3, '0', STR_PAD_LEFT);

            $quotation = Quotation::create([
                'lead_id' => $validated['lead_id'],
                'sales_id' => $user->id,
                'no_quotation' => $noQuotation,
                'total_amount' => $totalAmount,
                'status_approval' => $statusApproval,
            ]);

            foreach ($processedItems as $pItem) {
                $quotation->items()->create($pItem);
            }

            Lead::where('id', $validated['lead_id'])->update(['status_leads' => 'QUOTATION']);

            DB::commit();

            $msg = $statusApproval === 'APPROVED' 
                ? 'Quotation berhasil dibuat (Auto-Approved)' 
                : 'Quotation berhasil dibuat namun menunggu Approval Finance';

            return response()->json([
                'message' => $msg,
                'data' => $quotation->load('items')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function downloadPdf($id)
    {
        $quotation = Quotation::with(['lead', 'sales', 'items.layanan'])->findOrFail($id);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.quotation', compact('quotation'));
        
        return $pdf->download('Quotation-' . $quotation->no_quotation . '.pdf');
    }
}
