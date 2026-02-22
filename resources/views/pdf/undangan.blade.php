<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 10.5pt;
        color: #1a1a1a;
        width: 100%;   /* DomPDF akan otomatis menyesuaikan dengan ukuran kertas */
        height: auto;
    }

    /* ===== HEADER ===== */
    .header {
        background-color: #0d47a1;
        color: white;
        padding: 5mm 8mm;
        width: 100%;
    }

    .header-table {
        width: 100%;
        border-collapse: collapse;
    }

    .header-table td {
        vertical-align: middle;
        padding: 0 2mm;
    }

    .td-logo { width: 20mm; text-align: center; overflow: hidden; }
    .td-text  { text-align: center; }

    /* Logo pakai TABLE di dalam agar bulat konsisten di DomPDF */
    .logo-wrap {
        width: 18mm;
        height: 18mm;
        background: white;
        border-radius: 9mm;   /* setengah dari width/height */
        display: block;
        margin: 0 auto;
        text-align: center;
        line-height: 1;
        padding-top: 3mm;
    }

    .logo-text {
        font-size: 6.5pt;
        font-weight: bold;
        color: #0d47a1;
        line-height: 1.4;
    }

    .h-univ {
        font-size: 14pt;
        font-weight: bold;
        letter-spacing: 1px;
        color: white;
    }
    .h-fak {
        font-size: 9.5pt;
        font-weight: bold;
        color: white;
        margin-top: 1.5mm;
    }
    .h-alamat {
        font-size: 7.5pt;
        color: rgba(255,255,255,0.88);
        margin-top: 1.5mm;
        line-height: 1.6;
    }

    .gold-bar { height: 3mm; background: #f0a500; }

    /* ===== BODY ===== */
    .body { padding: 6mm 18mm 5mm 18mm; }

    .nomor-table { width: 100%; margin-bottom: 4mm; border-collapse: collapse; }
    .nomor-table td { padding: 0.8mm 0; vertical-align: top; font-size: 10.5pt; }
    .col-label { width: 30mm; }
    .col-titik { width: 6mm; }

    .separator { border-top: 1px solid #0d47a1; margin: 4mm 0; }

    .kepada  { line-height: 1.8; margin-bottom: 4mm; }
    .pembuka { line-height: 1.7; margin-bottom: 3mm; }

    /* Box acara */
    .acara-box {
        background: #e3f2fd;
        border: 1px solid #0d47a1;
        padding: 4mm 7mm;
        margin: 3mm 0;
        text-align: center;
    }
    .acara-title { font-size: 11.5pt; font-weight: bold; color: #0d47a1; }
    .acara-sub   { font-size: 9.5pt; font-style: italic; color: #333; margin-top: 1.5mm; }

    .detail-table { margin: 3mm auto 0; border-collapse: collapse; }
    .detail-table td { padding: 0.8mm 2mm; font-size: 9.5pt; vertical-align: top; text-align: left; }
    .d-label { font-weight: bold; width: 30mm; }
    .d-titik { width: 5mm; }

    .penutup { line-height: 1.7; margin-top: 3mm; }

    /* Tanda tangan - pakai table agar rapi */
    .ttd-table { width: 100%; margin-top: 5mm; border-collapse: collapse; }
    .ttd-table td { vertical-align: top; font-size: 10.5pt; }
    .td-ttd-kiri  { width: 50%; }
    .td-ttd-kanan { width: 50%; text-align: right; }

    .ttd-space { height: 14mm; }  /* ruang tanda tangan */
    .ttd-name  { font-weight: bold; }

    /* Footer */
    .footer-gold { height: 2mm; background: #f0a500; margin-top: 5mm; }
    .footer-bar {
        background: #0d47a1;
        color: white;
        text-align: center;
        font-size: 7.5pt;
        padding: 2.5mm;
    }
</style>
</head>
<body>

<!-- HEADER -->
<div class="header">
    <table class="header-table">
        <tr>
            <!-- Logo Kiri -->
            <td class="td-logo">
                <div class="logo-wrap">
                    <span class="logo-text">LOGO<br>UNIV</span>
                </div>
            </td>

            <!-- Teks Tengah -->
            <td class="td-text">
                <div class="h-univ">UNIVERSITAS NUSANTARA JAYA</div>
                <div class="h-fak">FAKULTAS ILMU KOMPUTER DAN TEKNOLOGI INFORMASI</div>
                <div class="h-alamat">
                    Jl. Raya Kampus No. 1, Surabaya 60111, Jawa Timur<br>
                    Telp: (031) 555-1234 &nbsp;|&nbsp; Email: fkti@unj.ac.id &nbsp;|&nbsp; www.unj.ac.id
                </div>
            </td>

            <!-- Logo Kanan -->
            <td class="td-logo">
                <div class="logo-wrap">
                    <span class="logo-text">LOGO<br>FAK</span>
                </div>
            </td>
        </tr>
    </table>
</div>
<div class="gold-bar"></div>

<!-- BODY SURAT -->
<div class="body">

    <!-- Nomor Surat -->
    <table class="nomor-table">
        <tr>
            <td class="col-label">Nomor</td>
            <td class="col-titik">:</td>
            <td>045/UN-J/FKTI/II/2025</td>
        </tr>
        <tr>
            <td class="col-label">Lampiran</td>
            <td class="col-titik">:</td>
            <td>-</td>
        </tr>
        <tr>
            <td class="col-label">Hal</td>
            <td class="col-titik">:</td>
            <td><strong>Undangan Seminar Nasional Teknologi Informasi</strong></td>
        </tr>
    </table>

    <div class="separator"></div>

    <!-- Kepada -->
    <div class="kepada">
        Kepada Yth.<br>
        Bapak/Ibu Dosen dan Mahasiswa<br>
        Fakultas Ilmu Komputer dan Teknologi Informasi<br>
        Universitas Nusantara Jaya<br>
        di &ndash; <em><strong>Surabaya</strong></em>
    </div>

    <!-- Pembuka -->
    <div class="pembuka">
        Dengan hormat,<br><br>
        Dalam rangka meningkatkan wawasan dan kompetensi di bidang Teknologi Informasi,
        Fakultas Ilmu Komputer dan Teknologi Informasi Universitas Nusantara Jaya dengan
        bangga mengundang Bapak/Ibu Dosen serta seluruh Mahasiswa untuk menghadiri kegiatan:
    </div>

    <!-- Box Acara -->
    <div class="acara-box">
        <div class="acara-title">SEMINAR NASIONAL TEKNOLOGI INFORMASI 2025</div>
        <div class="acara-sub">"Transformasi Digital untuk Indonesia Maju"</div>
        <table class="detail-table">
            <tr>
                <td class="d-label">Hari, Tanggal</td>
                <td class="d-titik">:</td>
                <td>Sabtu, 1 Maret 2025</td>
            </tr>
            <tr>
                <td class="d-label">Waktu</td>
                <td class="d-titik">:</td>
                <td>08.00 &ndash; 16.00 WIB</td>
            </tr>
            <tr>
                <td class="d-label">Tempat</td>
                <td class="d-titik">:</td>
                <td>Auditorium Gedung A, Lantai 3</td>
            </tr>
            <tr>
                <td class="d-label">Narasumber</td>
                <td class="d-titik">:</td>
                <td>Dr. Ir. Ahmad Fauzi, M.T (Google Indonesia)</td>
            </tr>
        </table>
    </div>

    <!-- Penutup -->
    <div class="penutup">
        Mengingat pentingnya kegiatan ini, besar harapan kami agar Bapak/Ibu dan seluruh
        mahasiswa dapat hadir tepat waktu. Konfirmasi kehadiran dapat dilakukan melalui
        email: <strong>seminar@unj.ac.id</strong> atau WhatsApp: <strong>0812-3456-7890</strong>
        paling lambat tanggal 25 Februari 2025.<br><br>
        Demikian undangan ini kami sampaikan. Atas perhatian dan kehadiran Bapak/Ibu,
        kami ucapkan terima kasih.
    </div>

    <!-- Tanda Tangan -->
    <table class="ttd-table">
        <tr>
            <td class="td-ttd-kiri"></td>
            <td class="td-ttd-kanan">
                Surabaya, 18 Februari 2025<br>
                Dekan,<br>
                <div class="ttd-space"></div>
                <div class="ttd-name">Prof. Dr. Budi Santoso, M.T</div>
                NIP. 197001012000031001
            </td>
        </tr>
    </table>

</div>

<!-- Footer -->
<div class="footer-gold"></div>
<div class="footer-bar">
    Jl. Raya Kampus No. 1, Surabaya 60111 &nbsp;|&nbsp; (031) 555-1234 &nbsp;|&nbsp; fkti@unj.ac.id
</div>

</body>
</html>