<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * 1. Export Commercial Sales PDF Report
     */
    public function salesPdf(Request $request)
    {
        $filters = [
            'period' => $request->input('period', 'this_month'),
            'from_date' => $request->input('from_date'),
            'to_date' => $request->input('to_date'),
            'store_id' => $request->filled('store_id') ? (int) $request->store_id : null,
        ];

        $result = $this->reportService->generateSalesReportPdf($filters, $request->user());

        if ($request->boolean('preview')) {
            return $result['pdf']->stream($result['filename']);
        }

        return $result['pdf']->download($result['filename']);
    }

    /**
     * 2. Export Commercial Sales CSV Ledger
     */
    public function salesCsv(Request $request): StreamedResponse
    {
        $filters = [
            'period' => $request->input('period', 'this_month'),
            'from_date' => $request->input('from_date'),
            'to_date' => $request->input('to_date'),
            'store_id' => $request->filled('store_id') ? (int) $request->store_id : null,
        ];

        $result = $this->reportService->generateSalesReportCsv($filters, $request->user());

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$result['filename']}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($result) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            foreach ($result['lines'] as $line) {
                fputcsv($handle, $line);
            }
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * 3. Export Multi-Store Inventory PDF Audit
     */
    public function inventoryPdf(Request $request)
    {
        $filters = [
            'store_id' => $request->filled('store_id') ? (int) $request->store_id : null,
            'days_threshold' => $request->input('days_threshold', 60),
        ];

        $result = $this->reportService->generateInventoryReportPdf($filters, $request->user());

        if ($request->boolean('preview')) {
            return $result['pdf']->stream($result['filename']);
        }

        return $result['pdf']->download($result['filename']);
    }

    /**
     * 4. Export Inventory CSV Ledger
     */
    public function inventoryCsv(Request $request): StreamedResponse
    {
        $filters = [
            'store_id' => $request->filled('store_id') ? (int) $request->store_id : null,
        ];

        $result = $this->reportService->generateInventoryReportCsv($filters, $request->user());

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$result['filename']}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($result) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            foreach ($result['lines'] as $line) {
                fputcsv($handle, $line);
            }
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * 5. Historical Report Generation Records
     */
    public function history(Request $request): JsonResponse
    {
        $reports = $this->reportService->getHistoricalReports([
            'type' => $request->input('type'),
            'store_id' => $request->input('store_id'),
            'per_page' => $request->input('per_page', 15),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $reports->items(),
            'meta' => [
                'current_page' => $reports->currentPage(),
                'last_page' => $reports->lastPage(),
                'per_page' => $reports->perPage(),
                'total' => $reports->total(),
            ],
        ]);
    }
}
