<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userRole = auth()->user()->role?->nama;

        if (!$userRole || !in_array($userRole, $roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Anda tidak memiliki hak akses ke sumber daya ini.',
                ], 403);
            }

            if ($userRole === 'admin') {
                return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak. Anda dialihkan ke dasbor administrator.');
            }
            if ($userRole === 'perusahaan' && \Illuminate\Support\Facades\Route::has('company.dashboard')) {
                return redirect()->route('company.dashboard')->with('error', 'Akses ditolak.');
            }
            if ($userRole === 'siswa' && \Illuminate\Support\Facades\Route::has('student.dashboard')) {
                return redirect()->route('student.dashboard')->with('error', 'Akses ditolak. Anda dialihkan ke portal siswa.');
            }

            return redirect()->route('landing')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman tersebut.');
        }

        return $next($request);
    }
}
