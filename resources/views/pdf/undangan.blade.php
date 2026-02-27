<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    /* Reset sederhana untuk PDF */
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'Helvetica', 'Arial', sans-serif; /* DejaVu Sans kadang berat, Helvetica standar PDF */
        font-size: 10.5pt;
        color: #1a1a1a;
        width: 100%;
    }

    .header {
        background-color: #0d47a1;
        color: white;
        padding: 5mm 0; /* Padding vertikal saja, horizontal diatur di td */
        width: 100%;
    }

    .header-table {
        width: 100%;
        border-collapse: collapse;
    }

    /* Solusi Center: Gunakan text-align center pada TD */
    .td-logo { 
        width: 25mm; 
        text-align: center; 
        vertical-align: middle;
    }
    
    .td-text { 
        text-align: center; 
        vertical-align: middle;
    }

    .logo-wrap {
        width: 18mm;
        height: 18mm;
        background: white;
        border-radius: 50%; /* Bulat sempurna */
        margin: 0 auto; /* Center secara horizontal */
        text-align: center;
    }

    /* Memperbaiki posisi text di dalam lingkaran logo */
    .logo-text {
        display: block;
        font-size: 6pt;
        font-weight: bold;
        color: #0d47a1;
        padding-top: 5mm; /* Mengatur teks agar ke tengah lingkaran */
        line-height: 1.2;
    }

    .h-univ {
        font-size: 14pt;
        font-weight: bold;
        color: white;
    }
    .h-fak {
        font-size: 9.5pt;
        font-weight: bold;
        color: white;
        margin-top: 1mm;
    }
    .h-alamat {
        font-size: 7.5pt;
        color: #e0e0e0;
        margin-top: 1mm;
        line-height: 1.4;
    }

    .gold-bar { height: 3mm; background: #f0a500; }
    .body { padding: 10mm 15mm; }

    .nomor-table { width: 100%; margin-bottom: 5mm; border-collapse: collapse; }
    .nomor-table td { padding: 1mm 0; vertical-align: top; }
    .col-label { width: 25mm; }
    .col-titik { width: 5mm; }

    .separator { border-top: 1px solid #0d47a1; margin: 5mm 0; }

    .kepada { margin-bottom: 5mm; line-height: 1.5; }
    .pembuka { margin-bottom: 4mm; line-height: 1.6; text-align: justify; }

    .acara-box {
        background: #e3f2fd;
        border: 1px solid #0d47a1;
        padding: 5mm;
        margin: 5mm 0;
        text-align: center;
    }
    .acara-title { font-size: 11pt; font-weight: bold; color: #0d47a1; }
    .acara-sub { font-size: 9pt; font-style: italic; margin-top: 1mm; }

    .detail-table { margin: 4mm auto; border-collapse: collapse; width: 80%; }
    .detail-table td { padding: 1.5mm; font-size: 10pt; text-align: left; }
    .d-label { font-weight: bold; width: 35%; }

    .penutup { line-height: 1.6; margin-top: 5mm; text-align: justify; }

    .ttd-table { width: 100%; margin-top: 10mm; border-collapse: collapse; }
    .td-ttd-kanan { width: 100%; text-align: right; }
    .ttd-space { height: 20mm; } 
    .ttd-name { font-weight: bold; text-decoration: underline; }

    .footer-gold { height: 2mm; background: #f0a500; position: fixed; bottom: 10mm; width: 100%; }
    .footer-bar {
        background: #0d47a1;
        color: white;
        text-align: center;
        font-size: 8pt;
        padding: 3mm;
        position: fixed;
        bottom: 0;
        width: 100%;
    }
</style>
</head>
<body>

<div class="header">
    <table class="header-table">
        <tr>
            <td class="td-logo">
                <div class="logo-wrap">
                    <span class="logo-text">LOGO<br>UNIV</span>
                </div>
            </td>

            <td class="td-text">
                <div class="h-univ">UNIVERSITAS NUSANTARA JAYA</div>
                <div class="h-fak">FAKULTAS ILMU KOMPUTER DAN TEKNOLOGI INFORMASI</div>
                <div class="h-alamat">
                    Jl. Raya Kampus No. 1, Surabaya 60111, Jawa Timur<br>
                    Telp: (031) 555-1234 &nbsp;|&nbsp; Email: fkti@unj.ac.id
                </div>
            </td>

            <td class="td-logo">
                <div class="logo-wrap">
                    <span class="logo-text">LOGO<br>FAK</span>
                </div>
            </td>
        </tr>
    </table>
</div>
<div class="gold-bar"></div>

<div class="body">
    <table class="nomor-table">
        <tr>
            <td class="col-label">Nomor</td>
            <td class="col-titik">:</td>
            <td>045/UN-J/FKTI/II/2026</td>
        </tr>
        <tr>
            <td class="col-label">Hal</td>
            <td class="col-titik">:</td>
            <td><strong>Undangan Seminar Nasional Teknologi Informasi</strong></td>
        </tr>
    </table>

    <div class="separator"></div>

    <div class="kepada">
        Kepada Yth.<br>
        <strong>Bapak/Ibu Dosen dan Mahasiswa</strong><br>
        Fakultas Ilmu Komputer dan Teknologi Informasi<br>
        di Tempat
    </div>

    <div class="pembuka">
        Dengan hormat,<br><br>
        Dalam rangka meningkatkan wawasan dan kompetensi di bidang Teknologi Informasi,
        Fakultas Ilmu Komputer dan Teknologi Informasi Universitas Nusantara Jaya dengan
        bangga mengundang Bapak/Ibu Dosen serta seluruh Mahasiswa untuk menghadiri kegiatan:
    </div>

    <div class="acara-box">
        <div class="acara-title">SEMINAR NASIONAL TEKNOLOGI INFORMASI 2026</div>
        <div class="acara-sub">"Transformasi Digital untuk Indonesia Maju"</div>
        <table class="detail-table">
            <tr>
                <td class="d-label">Hari, Tanggal</td>
                <td>: Sabtu, 1 Maret 2026</td>
            </tr>
            <tr>
                <td class="d-label">Waktu</td>
                <td>: 08.00 - 16.00 WIB</td>
            </tr>
            <tr>
                <td class="d-label">Tempat</td>
                <td>: Auditorium Gedung A, Lantai 3</td>
            </tr>
            <tr>
                <td class="d-label">Pembicara</td>
                <td>: Dr. Ir. IKa Kusnianti, M.Sc (CTO PT. Teknologi Nusantara)</td>
            </tr>

        </table>
    </div>

    <div class="penutup">
        Demikian undangan ini kami sampaikan. Atas perhatian dan kehadiran Bapak/Ibu, kami ucapkan terima kasih.
    </div>

    <table class="ttd-table">
        <tr>
            <td class="td-ttd-kanan">
                Surabaya, 18 Februari 2026<br>
                Dekan,<br>
                <div class="ttd-space"></div>
                <div class="ttd-name">Prof. Dr. Budi Santoso, M.T</div>
                NIP. 197001012000031001
            </td>
        </tr>
    </table>
</div>

    <div class="footer-gold"></div>
<div class="footer-bar">
    Jl. Raya Kampus No. 1, Surabaya 60111 &nbsp;|&nbsp; www.unj.ac.id
</div>

</body>
</html>