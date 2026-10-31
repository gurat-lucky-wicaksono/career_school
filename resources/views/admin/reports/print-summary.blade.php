<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Resmi BKK SMKN 1 Cirebon - {{ date('Ymd') }}</title>
    
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

        /* Official Letterhead (Kop Surat) */
        .kop-surat {
            border-bottom: 3px double #0f172a;
            padding-bottom: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .kop-logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .kop-text {
            text-align: center;
            flex: 1;
        }

        .kop-text h4 {
            font-size: 11pt;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #334155;
        }

        .kop-text h3 {
            font-size: 12pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1e293b;
        }

        .kop-text h2 {
            font-size: 14pt;
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

        /* Title of Report */
        .report-title-box {
            text-align: center;
            margin-bottom: 24px;
        }

        .report-title {
            font-size: 13pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
            text-decoration: underline;
        }

        .report-meta {
            font-size: 9.5pt;
            color: #475569;
            margin-top: 4px;
        }

        /* Section Headings */
        .section-header {
            font-size: 10.5pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #0f172a;
            margin: 18px 0 8px 0;
            padding-bottom: 4px;
            border-bottom: 1px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 16px;
        }

        table th, table td {
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            text-align: left;
        }

        table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
            text-align: center;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }

        /* Metric Grid */
        .metric-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }

        .metric-card {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 10px;
            background: #f8fafc;
        }

        .metric-card-label {
            font-size: 8pt;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            display: block;
        }

        .metric-card-val {
            font-size: 14pt;
            font-weight: 800;
            color: #0f172a;
            display: block;
            margin-top: 2px;
        }

        /* Signatures block */
        .signature-section {
            margin-top: 36px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .sig-box {
            width: 45%;
            text-align: center;
            font-size: 9.5pt;
        }

        .sig-space {
            height: 64px;
        }

        .sig-name {
            font-weight: 700;
            text-decoration: underline;
            color: #0f172a;
        }

        .sig-nip {
            font-size: 9pt;
            color: #475569;
        }

        /* Print Media Styles */
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
            <span style="font-size: 14px; font-weight: 700; display: block;">Dokumen Resmi Laporan BKK</span>
            <span style="font-size: 11px; color: #94a3b8;">Format Standar Siap Cetak & Ekspor PDF</span>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="button" class="btn-print" onclick="window.print()">
                Cetak Dokumen Sekarang (Ctrl + P)
            </button>
            <a href="{{ route('admin.reports.index') }}" class="btn-close">
                Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Printable Paper Sheet -->
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
                    Telepon: (0231) 480202 &bull; Faksimile: (0231) 481234 &bull; Laman: https://smkn1-cirebon.sch.id<br>
                    Pos-el: info@smkn1-cirebon.sch.id &bull; <strong>Bursa Kerja Khusus (BKK) Mitra Industri</strong>
                </p>
            </div>
        </div>

        <!-- Judul Laporan -->
        <div class="report-title-box">
            <h1 class="report-title">
                Laporan Rekapitulasi Penyerapan Lulusan & Kemitraan DUDI
            </h1>
            <p class="report-meta">
                Periode: {{ $reportData['selected_year'] ?? 'Seluruh Angkatan' }} &bull; 
                Jurusan: {{ $reportData['selected_major']->nama ?? 'Semua Konsentrasi Keahlian' }} &bull; 
                Dicetak pada: {{ $reportData['generated_at']->translatedFormat('d F Y, H:i') }} WIB
            </p>
        </div>

        <!-- 1. Ringkasan Eksekutif -->
        <div class="section-header">
            <span>I. Indikator Kinerja Utama (IKU) BKK</span>
        </div>
        <div class="metric-grid">
            <div class="metric-card">
                <span class="metric-card-label">Penyerapan Kerja</span>
                <span class="metric-card-val">{{ $reportData['metrics']['absorption_rate'] }}%</span>
            </div>
            <div class="metric-card">
                <span class="metric-card-label">Mitra Industri (DUDI)</span>
                <span class="metric-card-val">{{ $reportData['metrics']['verified_companies'] }} Mitra</span>
            </div>
            <div class="metric-card">
                <span class="metric-card-label">Lowongan Kerja/PKL</span>
                <span class="metric-card-val">{{ $reportData['metrics']['active_jobs'] }} Posisi</span>
            </div>
            <div class="metric-card">
                <span class="metric-card-label">Masa Tunggu Kerja</span>
                <span class="metric-card-val">{{ $reportData['metrics']['avg_wait_time'] }} Bulan</span>
            </div>
        </div>

        <!-- 2. Data Penyerapan Tracer Study Alumni (BMW) -->
        <div class="section-header">
            <span>II. Data Penelusuran Tamatan (Tracer Study & BMW)</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Status Lulusan (BMW)</th>
                    <th style="width: 140px;">Jumlah Tamatan</th>
                    <th style="width: 140px;">Persentase (%)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>Bekerja pada Dunia Usaha & Industri (DUDI)</td>
                    <td class="text-center font-bold">{{ $reportData['bmw_stats']['bekerja']['count'] }} orang</td>
                    <td class="text-center">{{ $reportData['bmw_stats']['bekerja']['pct'] }}%</td>
                </tr>
                <tr>
                    <td class="text-center">2</td>
                    <td>Melanjutkan Pendidikan ke Perguruan Tinggi (Kuliah)</td>
                    <td class="text-center font-bold">{{ $reportData['bmw_stats']['kuliah']['count'] }} orang</td>
                    <td class="text-center">{{ $reportData['bmw_stats']['kuliah']['pct'] }}%</td>
                </tr>
                <tr>
                    <td class="text-center">3</td>
                    <td>Wirausaha Mandiri / Rintisan Usaha</td>
                    <td class="text-center font-bold">{{ $reportData['bmw_stats']['wirausaha']['count'] }} orang</td>
                    <td class="text-center">{{ $reportData['bmw_stats']['wirausaha']['pct'] }}%</td>
                </tr>
                <tr>
                    <td class="text-center">4</td>
                    <td>Masa Tunggu / Menyiapkan Diri Mencari Kerja</td>
                    <td class="text-center font-bold">{{ $reportData['bmw_stats']['mencari_kerja']['count'] }} orang</td>
                    <td class="text-center">{{ $reportData['bmw_stats']['mencari_kerja']['pct'] }}%</td>
                </tr>
            </tbody>
            <tfoot>
                <tr style="background-color: #f8fafc; font-weight: 700;">
                    <td colspan="2" class="text-center">Total Alumni Terdata</td>
                    <td class="text-center">{{ $reportData['bmw_stats']['total'] }} orang</td>
                    <td class="text-center">100.0%</td>
                </tr>
            </tfoot>
        </table>

        <!-- 3. Keselarasan Kompetensi Kejuruan -->
        <div class="section-header">
            <span>III. Relevansi Kurikulum Kejuruan dengan Dunia Industri</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Tingkat Keselarasan</th>
                    <th>Sangat Sesuai</th>
                    <th>Sesuai</th>
                    <th>Kurang Sesuai</th>
                    <th>Tidak Sesuai</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="font-bold">Jumlah Responden</td>
                    <td class="text-center">{{ $reportData['keselarasan_stats']['sangat_sesuai'] }} alumni</td>
                    <td class="text-center">{{ $reportData['keselarasan_stats']['sesuai'] }} alumni</td>
                    <td class="text-center">{{ $reportData['keselarasan_stats']['kurang_sesuai'] }} alumni</td>
                    <td class="text-center">{{ $reportData['keselarasan_stats']['tidak_sesuai'] }} alumni</td>
                </tr>
            </tbody>
        </table>

        <!-- 4. Mitra Industri Teraktif -->
        <div class="section-header">
            <span>IV. Mitra Industri Teraktif dalam Penyerapan Siswa</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Mitra Industri (DUDI)</th>
                    <th>Bidang Industri</th>
                    <th style="width: 150px;">Lokasi</th>
                    <th style="width: 140px;">Siswa Diterima</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reportData['top_companies'] as $idx => $comp)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="font-bold">{{ $comp->nama }}</td>
                        <td>{{ $comp->bidang_usaha ?? '-' }}</td>
                        <td>{{ $comp->lokasi ?? 'Cirebon' }}</td>
                        <td class="text-center font-bold">{{ $comp->accepted_count }} orang</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="color: #94a3b8;">Belum ada data penyerapan mitra industri.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Bagian Pengesahan & Tanda Tangan -->
        <div class="signature-section">
            <div class="sig-box">
                <p>Mengetahui,</p>
                <p>{{ $reportData['signatories']['koordinator_bkk']['jabatan'] }}</p>
                <div class="sig-space"></div>
                <p class="sig-name">{{ $reportData['signatories']['koordinator_bkk']['nama'] }}</p>
                <p class="sig-nip">NIP. {{ $reportData['signatories']['koordinator_bkk']['nip'] }}</p>
            </div>

            <div class="sig-box">
                <p>Cirebon, {{ $reportData['generated_at']->translatedFormat('d F Y') }}</p>
                <p>{{ $reportData['signatories']['kepala_sekolah']['jabatan'] }}</p>
                <div class="sig-space"></div>
                <p class="sig-name">{{ $reportData['signatories']['kepala_sekolah']['nama'] }}</p>
                <p class="sig-nip">NIP. {{ $reportData['signatories']['kepala_sekolah']['nip'] }}</p>
            </div>
        </div>
    </div>

</body>
</html>
