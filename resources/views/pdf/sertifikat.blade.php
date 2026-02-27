<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'DejaVu Sans', sans-serif;
        background: #1a1a2e;
        color: white;
        margin: 0;
        padding: 0;
        width: 279mm;  
        height: 197mm; 
    }

    .page {
        width: 279mm;
        height: 197mm;
        background: #1a1a2e;
        position: relative;
        text-align: center;
        padding: 9mm;
        overflow: hidden;
    }

    .strip-left {
        position: absolute;
        left: 0; top: 0; bottom: 0; width: 20mm;
        background: #16213e;
        border-right: 4px solid #f0a500;
    }
    .strip-right {
        position: absolute;
        right: 0; top: 0; bottom: 0; width: 20mm;
        background: #16213e;
        border-left: 4px solid #f0a500;
    }

    .border-outer {
        position: absolute;
        top: 6mm; left: 6mm; right: 6mm; bottom: 6mm;
        border: 2.5px solid #f0a500;
    }
    .border-inner {
        position: absolute;
        top: 9mm; left: 9mm; right: 9mm; bottom: 9mm;
        border: 1px solid #f0a500;
    }

    .university {
        font-size: 11pt;
        font-weight: bold;
        color: #f0a500;
        letter-spacing: 2px;
        margin-bottom: 1mm;
    }
    .faculty {
        font-size: 8.5pt;
        color: #cccccc;
        margin-bottom: 3mm;
    }

    .divider {
        border: none;
        border-top: 1.5px solid #f0a500;
        width: 110mm;
        margin: 0 auto 4mm;
    }

    .title {
        font-size: 32pt;
        font-weight: bold;
        color: white;
        letter-spacing: 5px;
        margin-bottom: 1mm;
    }
    .subtitle {
        font-size: 10.5pt;
        color: #f0a500;
        letter-spacing: 2px;
        margin-bottom: 5mm;
    }
    .presented {
        font-size: 9.5pt;
        color: #aaaaaa;
        margin-bottom: 2mm;
    }
    .name {
        font-size: 24pt;
        font-style: italic;
        color: #f0a500;
        margin-bottom: 2mm;
    }
    .name-line {
        border: none;
        border-top: 1px solid #f0a500;
        width: 130mm;
        margin: 0 auto 3mm;
    }
    .desc {
        font-size: 9.5pt;
        color: white;
        line-height: 1.6;
        margin-bottom: 2mm;
    }
    .achievement {
        font-size: 11.5pt;
        font-weight: bold;
        color: #f0a500;
        margin-bottom: 6mm;
    }

    .sig-table {
        width: 100%;
        border-collapse: collapse;
    }
    .sig-table td {
        width: 50%;
        text-align: center;
        vertical-align: top;
        font-size: 8.5pt;
        color: white;
        padding: 0 8mm;
    }
    .sig-space { height: 10mm; }
    .sig-line-el {
        border-top: 1px solid #f0a500;
        margin: 0 auto 2mm;
        width: 45mm;
    }
    .sig-name { font-weight: bold; font-size: 8.5pt; margin-bottom: 1mm; }
    .sig-nip  { font-size: 7.5pt; color: #aaaaaa; }

    .cert-no {
        position: absolute;
        bottom: 11mm;
        left: 0; right: 0;
        text-align: center;
        font-size: 7.5pt;
        color: #888888;
    }
</style>
</head>
<body>
<div class="page">
    <div class="strip-left"></div>
    <div class="strip-right"></div>
    <div class="border-outer"></div>
    <div class="border-inner"></div>

    <div class="university">UNIVERSITAS NUSANTARA JAYA</div>
    <div class="faculty">Fakultas Ilmu Komputer dan Teknologi Informasi</div>
    <hr class="divider">

    <div class="title">SERTIFIKAT</div>
    <div class="subtitle">Penghargaan Mahasiswa Berprestasi</div>

    <div class="presented">Diberikan kepada:</div>
    <div class="name">Muhammad Rizky Pratama</div>
    <hr class="name-line">

    <div class="desc">
        Atas prestasi luar biasa dalam Kompetisi Pemrograman Nasional<br>
        Tingkat Perguruan Tinggi Tahun 2025 sebagai
    </div>
    <div class="achievement">JUARA I (PERTAMA)</div>

    <table class="sig-table">
        <tr>
            <td>
                Ketua Program Studi
                <div class="sig-space"></div>
                <div class="sig-line-el"></div>
                <div class="sig-name">Dr. Siti Rahayu, M.Kom</div>
                <div class="sig-nip">NIP. 198502152010122001</div>
            </td>
            <td>
                Surabaya, 18 Februari 2025<br>
                Dekan Fakultas,
                <div class="sig-space"></div>
                <div class="sig-line-el"></div>
                <div class="sig-name">Prof. Dr. Budi Santoso, M.T</div>
                <div class="sig-nip">NIP. 197001012000031001</div>
            </td>
        </tr>
    </table>

    <div class="cert-no">No. Sertifikat: UNJ/FKTI/SERT/2025/001</div>
</div>
</body>
</html>