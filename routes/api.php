<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReportController;

use App\Http\Controllers\LayananController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\WebhookController;

Route::post('/auth/login', [AuthController::class, 'login']);

// Webhook for WA Leads (No Auth required or use Custom Header Auth)
Route::post('/webhooks/wa-leads', [WebhookController::class, 'waLeads']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('leads', LeadController::class);
    Route::post('/leads/import', [LeadController::class, 'importCsv']);
    Route::get('/leads/export', [LeadController::class, 'exportCsv']);
    
    Route::get('/layanans', [LayananController::class, 'index']);
    
    Route::apiResource('quotations', QuotationController::class);
    Route::get('/quotations/{id}/pdf', [QuotationController::class, 'downloadPdf']);
    
    Route::apiResource('invoices', InvoiceController::class);
    Route::get('/invoices/{id}/pdf', [InvoiceController::class, 'downloadPdf']);
    
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::post('/projects/{id}/spk', [ProjectController::class, 'generateSpk']);
    Route::get('/spks/{id}/pdf', [ProjectController::class, 'downloadSpkPdf']);

    Route::get('/reports', [ReportController::class, 'index']);
});
