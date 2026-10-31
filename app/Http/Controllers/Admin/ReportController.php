<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Services\ReportExportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    protected ReportExportService $reportService;

    public function __construct(ReportExportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display BKK reporting hub and statistical overview.
     */
    public function index(Request $request): View
    {
        $filters = [
            'major_id' => $request->query('major_id'),
            'tahun' => $request->query('tahun'),
        ];

        $reportData = $this->reportService->getBkkSummaryData($filters);
        $majors = Major::orderBy('nama')->get();

        $breadcrumbs = [
            ['label' => 'Dashboard Admin', 'url' => route('admin.dashboard')],
            ['label' => 'Laporan & Ekspor BKK', 'url' => null],
        ];

        return view('admin.reports.index', compact('reportData', 'majors', 'breadcrumbs', 'filters'));
    }

    /**
     * Display print-ready official report of BKK SMKN 1 Cirebon.
     */
    public function printSummary(Request $request): View
    {
        $filters = [
            'major_id' => $request->query('major_id'),
            'tahun' => $request->query('tahun'),
        ];

        $reportData = $this->reportService->getBkkSummaryData($filters);

        return view('admin.reports.print-summary', compact('reportData', 'filters'));
    }

    /**
     * Download Tracer Study CSV dataset.
     */
    public function exportTracerCsv(Request $request): StreamedResponse
    {
        $filters = [
            'major_id' => $request->query('major_id'),
            'status_bmw' => $request->query('status_bmw'),
            'tahun_lulus' => $request->query('tahun_lulus'),
        ];

        return $this->reportService->streamTracerCsv($filters);
    }

    /**
     * Download Job & Internship Applications CSV dataset.
     */
    public function exportApplicationsCsv(Request $request): StreamedResponse
    {
        $filters = [
            'status' => $request->query('status'),
            'job_id' => $request->query('job_id'),
        ];

        return $this->reportService->streamApplicationsCsv($filters);
    }
}
