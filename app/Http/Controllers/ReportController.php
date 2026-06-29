<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Quotation;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');

        if ($type === 'revenue') {
            $invoices = Invoice::whereIn('status_pembayaran', ['PAID_DP', 'FULL_PAID'])
                ->with('quotation.sales')
                ->get();

            $revenueBySales = [];
            foreach ($invoices as $inv) {
                if ($inv->quotation && $inv->quotation->sales) {
                    $salesName = $inv->quotation->sales->name;
                    if (!isset($revenueBySales[$salesName])) {
                        $revenueBySales[$salesName] = 0;
                    }
                    $revenueBySales[$salesName] += $inv->total_amount;
                }
            }

            arsort($revenueBySales);

            $leaderboard = [];
            foreach ($revenueBySales as $sales => $revenue) {
                $leaderboard[] = ['sales' => $sales, 'revenue' => $revenue];
            }

            return response()->json(['data' => $leaderboard]);
        }

        if ($type === 'quotation') {
            $quotations = Quotation::with('sales')->get();

            $quotesBySales = [];
            foreach ($quotations as $q) {
                if ($q->sales) {
                    $salesName = $q->sales->name;
                    if (!isset($quotesBySales[$salesName])) {
                        $quotesBySales[$salesName] = 0;
                    }
                    $quotesBySales[$salesName] += 1;
                }
            }

            arsort($quotesBySales);

            $leaderboard = [];
            foreach ($quotesBySales as $sales => $total) {
                $leaderboard[] = ['sales' => $sales, 'totalQuotations' => $total];
            }

            return response()->json(['data' => $leaderboard]);
        }

        return response()->json(['error' => 'Invalid report type'], 400);
    }
}
