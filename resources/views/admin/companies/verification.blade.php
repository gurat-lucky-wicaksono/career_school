@extends('layouts.admin')

@section('title', 'Verifikasi Mitra Perusahaan')

@section('content')
<div class="space-y-6">
    @php
        $headers = [
            ['key' => 'nama', 'label' => 'Nama Perusahaan', 'sortable' => true],
            ['key' => 'bidang', 'label' => 'Sektor Industri', 'sortable' => true],
            ['key' => 'kota', 'label' => 'Kota', 'sortable' => true],
            ['key' => 'created_at', 'label' => 'Tanggal Pengajuan', 'sortable' => true],
            ['key' => 'status', 'label' => 'Status Antrean', 'sortable' => false],
        ];
    @endphp

    <x-admin.data-table 
        title="Antrean Verifikasi Mitra Industri"
        subtitle="Daftar pengajuan registrasi kemitraan baru oleh perusahaan yang memerlukan persetujuan administrator"
        searchPlaceholder="Cari nama perusahaan atau kota..."
        :search="request('search')"
        :items="$pendingCompanies"
        :headers="$headers"
    >
        <x-slot:checkboxAll>
            <input 
                type="checkbox" 
                id="select-all-pending"
                onclick="toggleSelectAll(this)"
                aria-label="Pilih Semua Antrean"
            >
        </x-slot:checkboxAll>

        <x-slot:action>
            <a href="{{ route('admin.companies.index') }}">
                <x-ui.button variant="outline">
                    Semua Mitra Industri
                </x-ui.button>
            </a>
        </x-slot:action>

        @forelse ($pendingCompanies as $company)
            <tr class="hover:bg-slate-50/80 dark:hover:bg-neutral-800/40 transition-colors group">
                <td class="w-12 px-4 py-3.5 text-center">
                    <input 
                        type="checkbox" 
                        aria-label="Pilih antrean {{ $company->nama }}"
                    >
                </td>

                <td class="px-5 py-3.5">
                    <div>
                        <span class="font-semibold text-slate-900 dark:text-white block">
                            {{ $company->nama }}
                        </span>
                        <span class="text-xs text-slate-500 dark:text-slate-400 block font-mono">
                            {{ $company->user->email ?? '-' }}
                        </span>
                    </div>
                </td>

                <td class="px-5 py-3.5 text-slate-700 dark:text-slate-200 text-xs">
                    {{ $company->bidang }}
                </td>

                <td class="px-5 py-3.5 text-slate-700 dark:text-slate-200 text-xs">
                    {{ $company->kota ?? '-' }}
                </td>

                <td class="px-5 py-3.5 text-slate-600 dark:text-slate-300 text-xs font-mono">
                    {{ $company->created_at->format('d M Y, H:i') }}
                </td>

                <td class="px-5 py-3.5">
                    <x-ui.badge variant="warning">Menunggu Review</x-ui.badge>
                </td>

                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                    <div class="inline-flex items-center gap-1.5">
                        <x-ui.icon-button 
                            size="sm" 
                            icon="check" 
                            label="Setujui Verifikasi Mitra" 
                            onclick="approveCompany('{{ route('admin.companies.verify', $company->id) }}', '{{ addslashes($company->nama) }}')"
                        />
                        <x-ui.icon-button 
                            size="sm" 
                            variant="danger" 
                            icon="x" 
                            label="Tolak Pengajuan Mitra" 
                            onclick="rejectCompany('{{ route('admin.companies.verify', $company->id) }}', '{{ addslashes($company->nama) }}')"
                        />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-16">
                    <x-ui.empty-state 
                        title="Semua Pengajuan Telah Ditinjau"
                        description="Tidak ada antrean pendaftaran mitra perusahaan yang sedang menunggu verifikasi saat ini."
                        actionLabel="Lihat Daftar Mitra Terdaftar"
                        :actionUrl="route('admin.companies.index')"
                    />
                </td>
            </tr>
        @endforelse
    </x-admin.data-table>
</div>

<!-- Hidden Form for Verification Submission -->
<form id="form-verify-company" method="POST" action="" style="display: none;">
    @csrf
    <input type="hidden" name="status" id="verify-status-input" value="terverifikasi">
</form>

@push('scripts')
<script>
    function toggleSelectAll(master) {
        document.querySelectorAll('tbody input[type="checkbox"]').forEach(cb => cb.checked = master.checked);
    }

    function approveCompany(url, name) {
        if (window.confirmAction) {
            window.confirmAction({
                title: 'Setujui Kemitraan?',
                text: 'Perusahaan ' + name + ' akan disetujui dan dapat mempublikasikan lowongan kerja/PKL.',
                confirmButtonText: 'Ya, Setujui',
                onConfirm: function () {
                    const form = document.getElementById('form-verify-company');
                    form.action = url;
                    document.getElementById('verify-status-input').value = 'terverifikasi';
                    form.submit();
                }
            });
        }
    }

    function rejectCompany(url, name) {
        if (window.confirmAction) {
            window.confirmAction({
                title: 'Tolak Pengajuan?',
                text: 'Pengajuan pendaftaran kemitraan ' + name + ' akan ditolak.',
                confirmButtonText: 'Tolak Pengajuan',
                isDanger: true,
                onConfirm: function () {
                    const form = document.getElementById('form-verify-company');
                    form.action = url;
                    document.getElementById('verify-status-input').value = 'ditolak';
                    form.submit();
                }
            });
        }
    }
</script>
@endpush
@endsection
