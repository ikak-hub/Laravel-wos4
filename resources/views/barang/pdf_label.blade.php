<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            width: 790px;
            height: 1120px;
            background: #ffffff;
        }

        .grid {
            width: 790px;
            height: 1120px;
            display: block;
        }

        .row-label {
            display: block;
            width: 790px;
            height: 140px;
            overflow: hidden;
        }

        .cell {
            display: inline-block;
            width: 158px;
            height: 140px;
            vertical-align: top;
            overflow: hidden;
            border: 1px dashed #cccccc;
        }

        /* Label sudah terpakai (sebelum start index) */
        .cell.used {
            background: #f0f0f0;
        }

        .cell.used::after {
            content: '';
            display: block;
            /* garis diagonal tanda terpakai */
            border-top: 1px solid #cccccc;
            margin: 68px 10px 0;
        }

        /* ── Sel kosong setelah semua label habis ── */
        .cell.empty {
            background: #ffffff;
        }

        /* ── Konten label aktif ── */
        .label-inner {
            margin: 3px;
            width: 150px;
            height: 133px;
            border: 1.5px solid #2c3e50;
            border-radius: 3px;
            overflow: hidden;
            background: #ffffff;
            display: block;
        }

        /* Strip header biru tua */
        .lbl-header {
            background: #2c3e50;
            color: #ecf0f1;
            text-align: center;
            padding: 3px 2px 2px;
            font-size: 7px;
            letter-spacing: 0.5px;
            height: 18px;
            line-height: 1.4;
            overflow: hidden;
        }

        /* Body label */
        .lbl-body {
            padding: 3px 4px 2px;
            text-align: center;
        }

        /* Garis pemisah */
        .lbl-divider {
            border: none;
            border-top: 1px solid #bdc3c7;
            margin: 3px auto;
            width: 80%;
        }

        /* Nama barang */
        .lbl-nama {
            font-size: 8px;
            font-weight: bold;
            color: #2c3e50;
            line-height: 1.25;
            word-wrap: break-word;
            max-height: 34px;
            overflow: hidden;
            margin-bottom: 3px;
        }

        /* Harga */
        .lbl-harga {
            font-size: 13px;
            font-weight: bold;
            color: #e74c3c;
            line-height: 1.2;
        }

        .lbl-rp {
            font-size: 7px;
            color: #7f8c8d;
            font-weight: normal;
        }

        /* Footer label kecil */
        .lbl-footer {
            font-size: 6px;
            color: #bdc3c7;
            text-align: center;
            padding: 2px 0 1px;
            margin-top: 2px;
        }
    </style>
</head>

<body>
    <?php

    $cols      = 5;
    $rows      = 8;
    $total     = $cols * $rows;         
    $list      = $barang->values();
    $count     = $list->count();

    $cellIdx   = 0;    // posisi sel saat ini (0-39)
    $barangPos = 0;    // indeks ke dalam $list
    ?>

    <div class="grid">

        <?php for ($r = 0; $r < $rows; $r++): ?>
            <div class="row-label">

                <?php for ($c = 0; $c < $cols; $c++): ?>
                    <?php
                    $relPos = $cellIdx - $startIndex;   // < 0 : terpakai, 0+ : isi barang, >= $count : kosong

                    if ($cellIdx < $startIndex):
                        // ── Sel terpakai ───
                    ?>
                        <div class="cell used"></div>

                    <?php elseif ($relPos >= 0 && $relPos < $count):
                        // ── Sel berisi label ───
                        $b = $list[$relPos];

                        // Format harga: Rp 5.000
                        $hargaFmt = number_format($b->harga, 0, ',', '.');

                        $namaDisplay = mb_strlen($b->nama) > 26
                            ? mb_substr($b->nama, 0, 24) . '…'
                            : $b->nama;
                    ?>
                        <div class="cell">
                            <div class="label-inner">

                                {{-- Header: ID Barang --}}
                                <div class="lbl-header">
                                    ID: {{ $b->id_barang }}
                                </div>

                                {{-- Body --}}
                                <div class="lbl-body">
                                    <div class="lbl-nama">{{ $namaDisplay }}</div>
                                    <hr class="lbl-divider">
                                    <div class="lbl-harga">
                                        <span class="lbl-rp">Rp </span>{{ $hargaFmt }}
                                    </div>
                                </div>

                                {{-- Footer --}}
                                <div class="lbl-footer">
                                    {{ \Carbon\Carbon::parse($b->timestamp)->format('d/m/Y') }}
                                </div>

                            </div>
                        </div>

                    <?php else:
                        // ── Sel kosong ───
                    ?>
                        <div class="cell empty"></div>

                    <?php endif; ?>

                    <?php $cellIdx++; ?>
                <?php endfor; // kolom 
                ?>

            </div>{{-- /.row-label --}}
        <?php endfor; // baris 
        ?>

    </div>{{-- /.grid --}}

</body>

</html>