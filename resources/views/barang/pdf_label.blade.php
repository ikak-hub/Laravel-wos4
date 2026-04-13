<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Cetak Label</title>

    <style>
        @page {
            size: 22.2cm 18.5cm;
            margin: 0;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            margin: 0;
            font-size: 9px;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0.2cm 0.2cm;
            /* jarak antar label */
            table-layout: fixed;
        }

        /* CELL */
        td {
            width: 3.8cm;
            height: 1.8cm;
            vertical-align: middle;
            text-align: center;
            box-sizing: border-box;
        }

        /* LABEL BOX */
        .lbl {
            width: 100%;
        }

        /* ISI */
        .lbl-id {
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .lbl-nama {
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .lbl-harga-text {
            font-size: 6px;
        }

        .lbl-harga {
            font-size: 11px;
            font-weight: bold;
        }

        .lbl-barcode img {
            max-width: 100%;
            height: 28px;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>

<body>

    <table>
        @php
        $cols = 5;
        $rows = 8;
        $startIdx = ($start_y - 1) * $cols + ($start_x - 1);
        @endphp

        @for ($row = 0; $row < $rows; $row++)
            <tr>
            @for ($col = 0; $col < $cols; $col++)
                @php
                $cellIndex=($row * $cols) + $col;
                $rel=$cellIndex - $startIdx;
                $b=($rel>= 0 && $rel < count($items)) ? $items[$rel] : null;
                    @endphp

                    <td>
                    @if($b)
                    <div class="lbl">
                        <div class="lbl-barcode">
                            <img src="data:image/png;base64,{!! DNS1D::getBarcodePNG($b['id_barang'], 'C128', 1, 28, [0,0,0], true) !!}"></div>
                        <div class="lbl-id">{{ $b['id_barang'] }}</div>
                        <div class="lbl-nama">
                            {{ mb_strlen($b['nama']) > 22 
                            ? mb_substr($b['nama'], 0, 20).'…' 
                            : $b['nama'] }}
                        </div>
                        <div class="lbl-harga-text">Harga</div>
                        <div class="lbl-harga">
                            Rp {{ number_format($b['harga'], 0, ',', '.') }}
                        </div>
                    </div>
                    @endif
                    </td>
                    @endfor
                    </tr>
                    @endfor
    </table>

</body>

</html>