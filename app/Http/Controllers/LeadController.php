<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Lead::with(['sales', 'sso'])->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status_leads', $request->status);
        }

        // RBAC
        if ($user->role === 'SALES') {
            $query->where('sales_id', $user->id);
        } elseif ($request->has('sales_id') && $user->role !== 'SALES') {
            $query->where('sales_id', $request->sales_id);
        }

        return response()->json(['data' => $query->get()]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'nama_perusahaan' => 'required|string',
            'jenis_perusahaan' => 'nullable|string',
            'kualifikasi' => 'nullable|string',
            'tingkat_kualifikasi' => 'nullable|string',
            'sub_klasifikasi' => 'nullable|string',
            'status_leads' => 'nullable|string',
            'tanggal_expired' => 'nullable|date',
            'nama_pic' => 'nullable|string',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string',
            'email' => 'nullable|email',
            'wilayah' => 'nullable|string',
            'sales_id' => 'nullable|uuid',
        ]);

        $salesId = $user->role === 'SALES' ? $user->id : ($validated['sales_id'] ?? null);
        $ssoId = $user->role === 'SSO' ? $user->id : null;

        $lead = Lead::create([
            'nama_perusahaan' => $validated['nama_perusahaan'],
            'jenis_perusahaan' => $validated['jenis_perusahaan'] ?? null,
            'kualifikasi' => $validated['kualifikasi'] ?? null,
            'tingkat_kualifikasi' => $validated['tingkat_kualifikasi'] ?? null,
            'sub_klasifikasi' => $validated['sub_klasifikasi'] ?? null,
            'status_leads' => $validated['status_leads'] ?? 'NEW',
            'tanggal_expired' => $validated['tanggal_expired'] ?? null,
            'nama_pic' => $validated['nama_pic'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'no_telepon' => $validated['no_telepon'] ?? null,
            'email' => $validated['email'] ?? null,
            'wilayah' => isset($validated['wilayah']) ? [$validated['wilayah']] : [],
            'sales_id' => $salesId,
            'sso_id' => $ssoId,
        ]);

        return response()->json(['message' => 'Lead berhasil dibuat', 'data' => $lead], 201);
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');
        
        // Skip header
        fgetcsv($handle);

        $chunkSize = 500;
        $records = [];
        $insertedCount = 0;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                // Ensure at least basic columns exist (Company Name)
                if (!isset($row[0])) continue;

                $records[] = [
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'nama_perusahaan' => $row[0],
                    'jenis_perusahaan' => $row[1] ?? null,
                    'status_leads' => $row[2] ?? 'NEW',
                    'nama_pic' => $row[3] ?? null,
                    'no_telepon' => $row[4] ?? null,
                    'email' => $row[5] ?? null,
                    'sales_id' => $request->user()->id,
                    'sso_id' => $request->user()->role === 'SSO' ? $request->user()->id : null,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                if (count($records) >= $chunkSize) {
                    Lead::insert($records);
                    $insertedCount += count($records);
                    $records = [];
                }
            }

            if (count($records) > 0) {
                Lead::insert($records);
                $insertedCount += count($records);
            }

            DB::commit();
            fclose($handle);

            return response()->json(['message' => "Berhasil import $insertedCount leads."]);
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            Log::error('Import CSV Error: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengimport data', 'error' => $e->getMessage()], 500);
        }
    }

    public function exportCsv(Request $request)
    {
        $fileName = 'leads_export_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Nama Perusahaan', 'PIC', 'Telepon', 'Email', 'Status', 'Kualifikasi', 'Harga Perkiraan'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            Lead::chunk(500, function ($leads) use ($file) {
                foreach ($leads as $lead) {
                    $row = [
                        $lead->id,
                        $lead->nama_perusahaan,
                        $lead->nama_pic,
                        $lead->no_telepon,
                        $lead->email,
                        $lead->status_leads,
                        $lead->kualifikasi,
                        // Dummy price as Number without formatting
                        15000000 
                    ];

                    fputcsv($file, $row);
                }
            });

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
