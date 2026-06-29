<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Endpoint untuk menerima data lead baru dari webhook WhatsApp
     */
    public function waLeads(Request $request)
    {
        // 1. (Opsional) Validasi Token / Secret dari WA Provider
        // $secret = $request->header('X-Webhook-Secret');
        // if ($secret !== env('WA_WEBHOOK_SECRET')) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }

        $validated = $request->validate([
            'nama_perusahaan' => 'required|string',
            'no_telepon' => 'required|string',
            'nama_pic' => 'nullable|string',
            'sumber' => 'nullable|string', // WA, Web, dll
        ]);

        try {
            // Menerima leads dari bot WA, default status adalah NEW
            $lead = Lead::create([
                'nama_perusahaan' => $validated['nama_perusahaan'],
                'no_telepon' => $validated['no_telepon'],
                'nama_pic' => $validated['nama_pic'] ?? 'User WA',
                'status_leads' => 'NEW',
                'kualifikasi' => 'UNQUALIFIED',
                'sso_id' => null, // Biarkan SSO memproses/assign ini nanti
                'sales_id' => null,
            ]);

            Log::info('Webhook WA Lead diterima: ' . $lead->id);

            return response()->json([
                'message' => 'Lead berhasil ditambahkan via Webhook',
                'data' => $lead
            ], 201);

        } catch (\Exception $e) {
            Log::error('Webhook WA Lead Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memproses webhook'], 500);
        }
    }
}
