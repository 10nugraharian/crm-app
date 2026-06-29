<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Spk;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::with(['invoice.quotation.lead'])->orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $projects]);
    }

    public function generateSpk(Request $request, $id)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|uuid',
            'nilai_pekerjaan' => 'required|numeric'
        ]);

        $project = Project::findOrFail($id);

        $noSpk = 'SPK-' . date('Ymd') . '-' . str_pad(Spk::count() + 1, 3, '0', STR_PAD_LEFT);

        $spk = Spk::create([
            'project_id' => $project->id,
            'vendor_id' => $validated['vendor_id'],
            'no_spk' => $noSpk,
            'nilai_pekerjaan' => $validated['nilai_pekerjaan'],
        ]);

        return response()->json([
            'message' => 'SPK berhasil dibuat',
            'data' => $spk
        ], 201);
    }

    public function downloadSpkPdf($id)
    {
        $spk = Spk::with(['project.invoice.quotation.lead', 'vendor'])->findOrFail($id);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.spk', compact('spk'));
        
        return $pdf->download($spk->no_spk . '.pdf');
    }
}
