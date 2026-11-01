<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of system activity logs with filters.
     */
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user')->latest('created_at');

        // Apply filters
        if ($role = $request->query('role')) {
            $query->filterRole($role);
        }

        if ($action = $request->query('action')) {
            $query->filterAction($action);
        }

        if ($search = $request->query('q') ?: $request->query('search')) {
            $query->search($search);
        }

        if ($request->query('start_date') || $request->query('end_date')) {
            $query->dateRange($request->query('start_date'), $request->query('end_date'));
        }

        $logs = $query->paginate(20)->withQueryString();

        // Statistical Counters
        $todayCount = ActivityLog::whereDate('created_at', today())->count();
        $authCount = ActivityLog::where('action', 'like', 'auth.%')->count();
        $companyCount = ActivityLog::where(function ($q) {
            $q->where('action', 'like', 'company.%')
              ->orWhere('action', 'like', 'application.%');
        })->count();
        $validationCount = ActivityLog::where('action', 'like', 'skill.%')->count();

        $breadcrumbs = [
            ['label' => 'Dashboard Admin', 'url' => route('admin.dashboard')],
            ['label' => 'Audit Trail & Log Aktivitas', 'url' => null],
        ];

        return view('admin.activity_logs.index', compact(
            'logs',
            'todayCount',
            'authCount',
            'companyCount',
            'validationCount',
            'breadcrumbs'
        ));
    }

    /**
     * Display JSON detail for a specific activity log.
     */
    public function show(ActivityLog $activityLog): JsonResponse
    {
        $activityLog->load('user');

        return response()->json([
            'id' => $activityLog->id,
            'actor' => [
                'name' => $activityLog->user->name ?? 'Sistem',
                'email' => $activityLog->user->email ?? '-',
                'role' => strtoupper($activityLog->role ?? 'SISTEM'),
            ],
            'action' => $activityLog->action,
            'action_label' => $activityLog->action_label,
            'description' => $activityLog->description,
            'subject_type' => $activityLog->subject_type ? class_basename($activityLog->subject_type) : null,
            'subject_id' => $activityLog->subject_id,
            'properties' => $activityLog->properties,
            'ip_address' => $activityLog->ip_address ?? '-',
            'user_agent' => $activityLog->user_agent ?? '-',
            'created_at' => $activityLog->created_at ? $activityLog->created_at->translatedFormat('d F Y, H:i:s') . ' WIB' : '-',
            'time_ago' => $activityLog->created_at ? $activityLog->created_at->diffForHumans() : '-',
        ]);
    }

    /**
     * Stream activity logs to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $filters = [
            'role' => $request->query('role'),
            'action' => $request->query('action'),
            'search' => $request->query('q') ?: $request->query('search'),
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date'),
        ];

        if (auth()->check()) {
            ActivityLogService::logReportExport(auth()->user(), 'activity_logs_csv', $filters);
        }

        return ActivityLogService::streamLogsCsv($filters);
    }
}
