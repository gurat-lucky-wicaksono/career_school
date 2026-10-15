<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Company Partner Recruitment Dashboard.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $company = $user->company;

        if (!$company) {
            $company = Company::create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'status_verifikasi' => 'menunggu',
            ]);
        }

        $allJobIds = $company->jobs()->pluck('id');

        $stats = [
            'total_jobs' => $company->jobs()->count(),
            'active_jobs' => $company->jobs()->where('status', 'dibuka')->count(),
            'closed_jobs' => $company->jobs()->where('status', '!=', 'dibuka')->count(),
            'total_applications' => Application::whereIn('job_id', $allJobIds)->count(),
            'pending_applications' => Application::whereIn('job_id', $allJobIds)->where('status', 'tertunda')->count(),
            'reviewed_applications' => Application::whereIn('job_id', $allJobIds)->where('status', 'ditinjau')->count(),
            'accepted_applications' => Application::whereIn('job_id', $allJobIds)->where('status', 'diterima')->count(),
        ];

        $recentJobs = $company->jobs()
            ->withCount('applications')
            ->latest()
            ->take(5)
            ->get();

        $recentApplications = Application::whereIn('job_id', $allJobIds)
            ->with(['student.user', 'student.major', 'job'])
            ->latest()
            ->take(6)
            ->get();

        $breadcrumbs = [
            ['label' => 'Dashboard Mitra', 'url' => null],
        ];

        return view('company.dashboard', compact(
            'company',
            'stats',
            'recentJobs',
            'recentApplications',
            'breadcrumbs'
        ));
    }
}
