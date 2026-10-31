<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pendaftaran Seleksi BKK - #APP-{{ str_pad($application->id, 5, '0', STR_PAD_LEFT) }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #1e293b;
            background-color: #f8fafc;
            padding: 24px 0;
        }

        .paper {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 20mm 20mm;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
        }

        /* Top Toolbar */
        .toolbar {
            width: 210mm;
            margin: 0 auto 16px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #0f172a;
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .toolbar button, .toolbar a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-print {
            background: #facc15;
            color: #0f172a;
        }

        .btn-print:hover {
            background: #eab308;
        }

        .btn-close {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }

        .btn-close:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        /* Letterhead */
        .kop-surat {
            border-bottom: 3px double #0f172a;
            padding-bottom: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .kop-logo {
            width: 68px;
            height: 68px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .kop-text {
            text-align: center;
            flex: 1;
        }

        .kop-text h4 {
            font-size: 10.5pt;
            font-weight: 600;
            text-transform: uppercase;
            color: #334155;
        }

        .kop-text h3 {
            font-size: 11.5pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #1e293b;
        }

        .kop-text h2 {
            font-size: 13.5pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            margin: 2px 0;
        }

        .kop-text p {
            font-size: 8.5pt;
            color: #64748b;
            line-height: 1.3;
        }

        /* Title Box */
        .title-box {
            text-align: center;
            margin-bottom: 22px;
        }

        .title-main {
            font-size: 12.5pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            text-decoration: underline;
        }

        .title-reg {
            font-size: 10pt;
            color: #0369a1;
            font-weight: 700;
            font-family: monospace;
            margin-top: 4px;
        }

        /* Section Headings */
        .section-header {
            font-size: 10pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #0f172a;
            margin: 16px 0 8px 0;
            padding-bottom: 3px;
            border-bottom: 1.5px solid #0f172a;
        }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 14px;
        }

        .data-table td {
            padding: 5px 8px;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
        }

        .data-table td.label-col {
            width: 220px;
            font-weight: 600;
            color: #475569;
        }

        .data-table td.sep-col {
            width: 16px;
            text-align: center;
            font-weight: 600;
        }

        .data-table td.val-col {
            color: #0f172a;
            font-weight: 600;
        }

        /* Status Badge */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 8.5pt;
            font-weight: 700;
            text-transform: uppercase;
            border: 1px solid #cbd5e1;
        }

        .badge-diterima { background: #dcfce7; color: #15803d; border-color: #86efac; }
        .badge-wawancara { background: #e0e7ff; color: #4338ca; border-color: #a5b4fc; }
        .badge-ditinjau { background: #e0f2fe; color: #0369a1; border-color: #7dd3fc; }
        .badge-tertunda { background: #fef9c3; color: #854d0e; border-color: #fde047; }
        .badge-ditolak { background: #ffe4e6; color: #be123c; border-color: #fca5a5; }

        /* Security Verification Box */
        .verif-box {
            border: 1px dashed #64748b;
            border-radius: 6px;
            padding: 10px 14px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
            margin-bottom: 24px;
        }

        .verif-info {
            font-size: 8.5pt;
            color: #475569;
        }

        .verif-info strong {
            color: #0f172a;
        }

        .verif-code {
            font-family: monospace;
            font-size: 11pt;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: 1px;
            background: #ffffff;
            padding: 4px 8px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
        }

        /* Signature block */
        .signature-section {
            margin-top: 28px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .sig-box {
            width: 44%;
            text-align: center;
            font-size: 9.5pt;
        }

        .sig-space {
            height: 60px;
        }

        .sig-name {
            font-weight: 700;
            text-decoration: underline;
            color: #0f172a;
        }

        .sig-sub {
            font-size: 9pt;
            color: #475569;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .toolbar {
                display: none !important;
            }

            .paper {
                box-shadow: none;
                margin: 0;
                width: 100%;
                padding: 10mm 15mm;
            }

            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

    <!-- Print Toolbar -->
    <div class="toolbar">
        <div>
            <span style="font-size: 14px; font-weight: 700; display: block;">Bukti Registrasi & Lembar Rekomendasi Seleksi</span>
            <span style="font-size: 11px; color: #94a3b8;">#APP-{{ str_pad($application->id, 5, '0', STR_PAD_LEFT) }} &bull; {{ $application->job->judul }}</span>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="button" class="btn-print" onclick="window.print()">
                Cetak Lembar Bukti (Ctrl + P)
            </button>
            <a href="{{ route('student.applications.show', $application) }}" class="btn-close">
                Kembali
            </a>
        </div>
    </div>

    <!-- Paper Sheet -->
    <div class="paper">
        <!-- Kop Surat Resmi -->
        <div class="kop-surat">
            <img src="{{ asset('images/logos/logo-career-school.png') }}" alt="Logo SMKN 1 Cirebon" class="kop-logo">
            <div class="kop-text">
                <h4>Pemerintah Daerah Provinsi Jawa Barat</h4>
                <h3>Dinas Pendidikan &bull; Cabang Dinas Wilayah X</h3>
                <h2>SMK Negeri 1 Cirebon</h2>
                <p>
                    Jl. Perjuangan, Sunyaragi, Kec. Kesambi, Kota Cirebon, Jawa Barat 45132<br>
                    Telepon: (0231) 480202 &bull; Pos-el: info@smkn1-cirebon.sch.id<br>
                    <strong>Bursa Kerja Khusus (BKK) & Pusat Karir Kejuruan</strong>
                </p>
            </div>
        </div>

        <!-- Judul -->
        <div class="title-box">
            <h1 class="title-main">Bukti Pendaftaran & Lembar Rekomendasi Seleksi</h1>
            <p class="title-reg">NOMOR REGISTRASI: APP-{{ str_pad($application->id, 5, '0', STR_PAD_LEFT) }}-{{ $application->created_at->format('Ymd') }}</p>
        </div>

        <!-- I. Biodata Siswa Vokasi -->
        <div class="section-header">I. Data Calon Siswa / Pelamar</div>
        <table class="data-table">
            <tr>
                <td class="label-col">Nama Lengkap Siswa</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $application->student->user->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">NISN / Identitas Siswa</td>
                <td class="sep-col">:</td>
                <td class="val-col font-mono">{{ $application->student->nisn ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">Konsentrasi Keahlian (Jurusan)</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $application->student->major->nama ?? 'Pengembangan Perangkat Lunak dan Gim (PPLG)' }}</td>
            </tr>
            <tr>
                <td class="label-col">Kelas & Angkatan</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $application->student->schoolClass->nama ?? '-' }} &bull; Angkatan {{ $application->student->angkatan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">Kontak / Pos-el Siswa</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $application->student->user->email }} @if($application->student->user->profile?->telepon) &bull; {{ $application->student->user->profile->telepon }} @endif</td>
            </tr>
            <tr>
                <td class="label-col">Kompetensi Tervalidasi Guru</td>
                <td class="sep-col">:</td>
                <td class="val-col">
                    @php
                        $skillsList = $application->student->studentSkills->where('tervalidasi', true)->map(fn($s) => $s->skill->nama)->take(5)->implode(', ');
                    @endphp
                    {{ $skillsList ?: 'Kompetensi dasar kejuruan telah diverifikasi.' }}
                </td>
            </tr>
        </table>

        <!-- II. Data Lowongan & Industri Mitra -->
        <div class="section-header">II. Data Formasi & Perusahaan Mitra (DUDI)</div>
        <table class="data-table">
            <tr>
                <td class="label-col">Perusahaan Mitra (DUDI)</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $application->job->company->nama ?? 'Mitra Industri' }}</td>
            </tr>
            <tr>
                <td class="label-col">Posisi / Formasi Kebutuhan</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $application->job->judul }}</td>
            </tr>
            <tr>
                <td class="label-col">Tipe Formasi</td>
                <td class="sep-col">:</td>
                <td class="val-col">
                    {{ $application->job->jenis === 'magang' ? 'Praktik Kerja Lapangan (PKL) / Magang Industri' : 'Penuh Waktu (Penyerapan Lulusan)' }}
                </td>
            </tr>
            <tr>
                <td class="label-col">Lokasi Penempatan</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $application->job->lokasi ?? 'Kota Cirebon' }}</td>
            </tr>
            <tr>
                <td class="label-col">Skor Kesesuaian KAK 15</td>
                <td class="sep-col">:</td>
                <td class="val-col">
                    <strong>{{ number_format($application->skor_kecocokan, 1) }}%</strong> 
                    <span style="font-size: 8.5pt; color: #64748b; font-weight: normal;">(Kecocokan portofolio & kompetensi terverifikasi)</span>
                </td>
            </tr>
            <tr>
                <td class="label-col">Status Lamaran Saat Ini</td>
                <td class="sep-col">:</td>
                <td class="val-col">
                    <span class="badge badge-{{ $application->status }}">
                        {{ strtoupper($application->status) }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label-col">Waktu Pendaftaran</td>
                <td class="sep-col">:</td>
                <td class="val-col">{{ $application->created_at->translatedFormat('d F Y, H:i') }} WIB</td>
            </tr>
            @if ($application->catatan_hrd)
            <tr>
                <td class="label-col">Catatan Rekruter / HRD</td>
                <td class="sep-col">:</td>
                <td class="val-col" style="font-style: italic;">"{{ $application->catatan_hrd }}"</td>
            </tr>
            @endif
        </table>

        <!-- Security Verification Box -->
        <div class="verif-box">
            <div class="verif-info">
                <strong>Verifikasi Dokumen Digital BKK SMKN 1 Cirebon</strong><br>
                Lembar ini diterbitkan otomatis melalui sistem CareerSchool SMKN 1 Cirebon sebagai dokumen resmi bukti pendaftaran.
            </div>
            <div class="verif-code">
                BKK-{{ strtoupper(substr(md5($application->id . $application->created_at), 0, 10)) }}
            </div>
        </div>

        <!-- III. Pengesahan -->
        <div class="signature-section">
            <div class="sig-box">
                <p>Pemohon / Siswa,</p>
                <div class="sig-space"></div>
                <p class="sig-name">{{ $application->student->user->name }}</p>
                <p class="sig-sub">NISN. {{ $application->student->nisn }}</p>
            </div>

            <div class="sig-box">
                <p>Cirebon, {{ date('d F Y') }}</p>
                <p>Pengelola Bursa Kerja Khusus (BKK)</p>
                <div class="sig-space"></div>
                <p class="sig-name">Bambang Sudarsono, S.Kom.</p>
                <p class="sig-sub">NIP. 19820719 200902 1 003</p>
            </div>
        </div>
    </div>

</body>
</html>
