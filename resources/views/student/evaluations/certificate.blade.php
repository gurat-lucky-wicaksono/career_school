<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat PKL - {{ $evaluation->nomor_sertifikat ?? 'SMKN1-PKL' }} - {{ $evaluation->student->user->name ?? 'Siswa' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Cinzel:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', Arial, Helvetica, sans-serif;
            font-size: 10.5pt;
            line-height: 1.5;
            color: #0f172a;
            background-color: #f1f5f9;
            padding: 24px 0;
        }

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
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
        }

        .toolbar-info {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 600;
        }

        .toolbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
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
            background: #0284c7;
            color: #ffffff;
        }

        .btn-print:hover {
            background: #0369a1;
        }

        .btn-close {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
        }

        .btn-close:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .paper {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 18mm 20mm;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
            position: relative;
        }

        .certificate-border {
            border: 2px solid #0f172a;
            outline: 1px solid #cbd5e1;
            outline-offset: -5px;
            padding: 14mm 16mm;
            min-height: 261mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Kop Surat Header */
        .kop-header {
            text-align: center;
            border-bottom: 3px double #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .kop-header h4 {
            font-size: 10pt;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #475569;
        }

        .kop-header h2 {
            font-size: 15pt;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #0f172a;
            margin: 2px 0;
        }

        .kop-header p {
            font-size: 8.5pt;
            color: #64748b;
        }

        /* Certificate Title */
        .cert-title-section {
            text-align: center;
            margin: 12px 0 16px 0;
        }

        .cert-title {
            font-family: 'Cinzel', serif, 'Plus Jakarta Sans';
            font-size: 16pt;
            font-weight: 800;
            letter-spacing: 1px;
            color: #0f172a;
            text-transform: uppercase;
        }

        .cert-subtitle {
            font-size: 10pt;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #0284c7;
            margin-top: 2px;
        }

        .cert-number {
            font-size: 9pt;
            font-family: monospace;
            font-weight: 700;
            color: #475569;
            margin-top: 4px;
        }

        .intro-text {
            font-size: 10pt;
            color: #334155;
            margin-bottom: 12px;
            text-align: justify;
            line-height: 1.6;
        }

        /* Student Identity Table */
        .table-identity {
            width: 100%;
            margin-bottom: 14px;
            border-collapse: collapse;
        }

        .table-identity td {
            padding: 3px 0;
            font-size: 10pt;
        }

        .table-identity td.label {
            width: 175px;
            color: #64748b;
            font-weight: 500;
        }

        .table-identity td.sep {
            width: 15px;
            text-align: center;
            color: #64748b;
        }

        .table-identity td.val {
            font-weight: 700;
            color: #0f172a;
        }

        /* Score Table */
        .score-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0 14px 0;
        }

        .score-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 9pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            border: 1px solid #0f172a;
        }

        .score-table td {
            padding: 7px 10px;
            font-size: 9.5pt;
            border: 1px solid #cbd5e1;
        }

        .score-table tr.total-row {
            background-color: #f8fafc;
            font-weight: 800;
        }

        .score-table tr.predicate-row {
            background-color: #f0fdf4;
            font-weight: 800;
            color: #166534;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* Notes Box */
        .notes-box {
            background-color: #f8fafc;
            border-left: 3px solid #0284c7;
            padding: 8px 12px;
            font-size: 8.5pt;
            color: #334155;
            margin-bottom: 16px;
            border-radius: 0 4px 4px 0;
        }

        .notes-box strong {
            color: #0f172a;
            display: block;
            margin-bottom: 2px;
        }

        /* Signatures Grid */
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 16px;
            page-break-inside: avoid;
        }

        .sig-col {
            text-align: center;
            font-size: 9.5pt;
        }

        .sig-col .sig-date {
            margin-bottom: 4px;
            color: #475569;
        }

        .sig-col .sig-title {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 55px;
        }

        .sig-col .sig-name {
            font-weight: 800;
            text-decoration: underline;
            color: #0f172a;
        }

        .sig-col .sig-id {
            font-size: 8.5pt;
            color: #64748b;
            margin-top: 2px;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }

            .toolbar {
                display: none !important;
            }

            .paper {
                box-shadow: none;
                border-radius: 0;
                margin: 0;
                padding: 12mm 15mm;
                width: 100%;
                min-height: auto;
            }

            .certificate-border {
                min-height: auto;
                padding: 10mm 12mm;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <div class="toolbar-info">
            <span>Sertifikat & Transkrip Selesai PKL</span>
            <span>&bull;</span>
            <span style="color: #94a3b8; font-family: monospace;">{{ $evaluation->nomor_sertifikat }}</span>
        </div>
        <div class="toolbar-actions">
            <button type="button" class="btn-print" onclick="window.print()">
                Cetak Dokumen
            </button>
            <a href="{{ route('student.evaluations.index') }}" class="btn-close">
                Tutup
            </a>
        </div>
    </div>

    <div class="paper">
        <div class="certificate-border">
            <div>
                <!-- Kop Surat Sekolah -->
                <div class="kop-header">
                    <h4>Pemerintah Daerah Provinsi Jawa Barat</h4>
                    <h2>SMK Negeri 1 Cirebon</h2>
                    <p>Bursa Kerja Khusus (BKK) & Program Praktik Kerja Lapangan (PKL)</p>
                    <p>Jl. Perjuangan No. 1, Karyamulya, Kec. Kesambi, Kota Cirebon, Jawa Barat 45131</p>
                </div>

                <!-- Judul Dokumen -->
                <div class="cert-title-section">
                    <h1 class="cert-title">Surat Keterangan Selesai PKL</h1>
                    <div class="cert-subtitle">Transkrip Evaluasi Capaian Kinerja Siswa</div>
                    <div class="cert-number">Nomor: {{ $evaluation->nomor_sertifikat ?? 'SMKN1-PKL/' . date('Y') . '/' . str_pad($evaluation->id, 4, '0', STR_PAD_LEFT) }}</div>
                </div>

                <p class="intro-text">
                    Kepala SMK Negeri 1 Cirebon bersama Pimpinan Mitra Industri menyatakan bahwa siswa yang tercantum di bawah ini telah menyelesaikan seluruh rangkaian kegiatan Praktik Kerja Lapangan (PKL) dengan hasil evaluasi sebagai berikut:
                </p>

                <!-- Data Siswa & Tempat PKL -->
                <table class="table-identity">
                    <tr>
                        <td class="label">Nama Lengkap</td>
                        <td class="sep">:</td>
                        <td class="val">{{ strtoupper($evaluation->student->user->name ?? 'Siswa') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nomor Induk Siswa Nasional</td>
                        <td class="sep">:</td>
                        <td class="val font-sans font-semibold">{{ $evaluation->student->nisn }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kompetensi Keahlian</td>
                        <td class="sep">:</td>
                        <td class="val">{{ $evaluation->student->major->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kelas / Angkatan</td>
                        <td class="sep">:</td>
                        <td class="val">{{ $evaluation->student->schoolClass->nama ?? '-' }} / {{ $evaluation->student->angkatan }}</td>
                    </tr>
                    <tr>
                        <td class="label">Mitra Industri (DUDI)</td>
                        <td class="sep">:</td>
                        <td class="val">{{ $evaluation->company->nama ?? 'Mitra Industri' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Formasi / Posisi PKL</td>
                        <td class="sep">:</td>
                        <td class="val">{{ $evaluation->application->job->judul ?? 'Praktik Kerja Lapangan' }}</td>
                    </tr>
                </table>

                <!-- Tabel Nilai Rubrik -->
                <table class="score-table">
                    <thead>
                        <tr>
                            <th style="width: 38px;" class="text-center">No</th>
                            <th>Aspek Kompetensi yang Dinilai</th>
                            <th style="width: 90px;" class="text-center">Bobot</th>
                            <th style="width: 90px;" class="text-center">Nilai Angka</th>
                            <th style="width: 90px;" class="text-center">Nilai Tertimbang</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">1</td>
                            <td>
                                <strong>Kompetensi Teknis / Kejuruan</strong>
                                <div style="font-size: 8pt; color: #64748b;">Penguasaan alat kerja, pemecahan masalah teknis, kerapian output</div>
                            </td>
                            <td class="text-center">40%</td>
                            <td class="text-center font-sans font-medium">{{ number_format($evaluation->nilai_teknis, 1) }}</td>
                            <td class="text-center font-sans font-bold">{{ number_format($evaluation->nilai_teknis * 0.40, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-center">2</td>
                            <td>
                                <strong>Disiplin & Etika Kerja</strong>
                                <div style="font-size: 8pt; color: #64748b;">Ketepatan waktu, kepatuhan SOP keselamatan kerja, integritas</div>
                            </td>
                            <td class="text-center">30%</td>
                            <td class="text-center font-sans font-medium">{{ number_format($evaluation->nilai_disiplin, 1) }}</td>
                            <td class="text-center font-sans font-bold">{{ number_format($evaluation->nilai_disiplin * 0.30, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-center">3</td>
                            <td>
                                <strong>Komunikasi & Kerjasama Tim</strong>
                                <div style="font-size: 8pt; color: #64748b;">Inisiatif, kolaborasi dengan tim kerja, keterbukaan masukan</div>
                            </td>
                            <td class="text-center">30%</td>
                            <td class="text-center font-sans font-medium">{{ number_format($evaluation->nilai_kerjasama, 1) }}</td>
                            <td class="text-center font-sans font-bold">{{ number_format($evaluation->nilai_kerjasama * 0.30, 2) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="3" class="text-right">Skor Akhir Komposit (Skala 0 - 100):</td>
                            <td colspan="2" class="text-center font-sans font-bold" style="font-size: 11pt;">{{ number_format($evaluation->nilai_akhir, 2) }}</td>
                        </tr>
                        <tr class="predicate-row">
                            <td colspan="3" class="text-right">Predikat Kelulusan PKL:</td>
                            <td colspan="2" class="text-center" style="font-size: 11pt;">{{ $evaluation->predikat }} ({{ $evaluation->predicate_label }})</td>
                        </tr>
                    </tbody>
                </table>

                @if($evaluation->catatan_industri)
                    <div class="notes-box">
                        <strong>Ulasan Pembimbing Mitra Industri:</strong>
                        "{{ $evaluation->catatan_industri }}"
                    </div>
                @endif
            </div>

            <!-- Bagian Tanda Tangan Dual Pihak -->
            <div class="signatures">
                <div class="sig-col">
                    <div class="sig-date">&nbsp;</div>
                    <div class="sig-title">
                        Supervisor Pembimbing Lapangan<br>
                        <strong>{{ $evaluation->company->nama ?? 'Mitra Industri' }}</strong>
                    </div>
                    <div class="sig-name">( &nbsp; Tim HRD / Pembimbing PKL &nbsp; )</div>
                    <div class="sig-id">Mitra DUDI SMKN 1 Cirebon</div>
                </div>

                <div class="sig-col">
                    <div class="sig-date">Cirebon, {{ $evaluation->verified_at ? $evaluation->verified_at->translatedFormat('d F Y') : date('d F Y') }}</div>
                    <div class="sig-title">
                        Guru Pembimbing / Penguji Kejuruan<br>
                        <strong>SMK Negeri 1 Cirebon</strong>
                    </div>
                    <div class="sig-name">{{ $evaluation->teacher->user->name ?? 'Pembimbing Kejuruan, S.Pd.' }}</div>
                    <div class="sig-id">NIP. {{ $evaluation->teacher->nip ?? '198501152010011002' }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
