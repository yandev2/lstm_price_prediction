<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Data Prediksi Harga</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 8px;
            word-wrap: break-word;
            text-align: left;
        }

        th {
            background-color: #0814b4;
            color: white;
            text-align: center
        }

        thead {
            display: table-header-group;
        }

        tbody {
            display: table-row-group;
        }
    </style>
</head>

<body>
    <table>
        <thead>
            <tr>
                <th colspan="8" style="text-align:center; font-size:15px; height:50px;">
                    DATA LAPORAN PREDIKSI HARGA PANGAN <br>
                    <span style="textlign: center; font-size: 10px; font-weight: 200;"> periode prediksi {{
                        Carbon\Carbon::parse($start)->translatedFormat('l, d F Y') }} - {{
                        Carbon\Carbon::parse($end)->translatedFormat('l, d F Y') }}</span>
                </th>
            </tr>
            <tr>
                <th style="width: 1%;">NO</th>
                <th style="width: 15%;">PASAR</th>
                <th style="width: 15%;">KOMODITAS</th>
                <th style="width: 8%;">TANGGAL PREDIKSI</th>
                <th style="width: 8%;">TARGET TANGGAL</th>
                <th style="width: 10%;">HARGA</th>
                <th style="width: 10%;">SELISIH</th>
                <th style="width: 20%;">ANOMALI</th>
            </tr>
        </thead>
        <tbody>
            <!-- Contoh data -->

            @foreach ($data as $item )
            <tr>
                <td> {{$loop->iteration }}  </td>
                <td> {{$item?->pasar?->nama_pasar??'-' }}  </td>
                <td> {{$item?->komoditas?->nama_komoditas??'-' }}  </td>
                <td> {{Carbon\Carbon::parse($item->tanggal_prediksi)->format('d-m-Y')}}  </td>
                <td> {{Carbon\Carbon::parse($item->prediksi_harga_untuk_tanggal)->format('d-m-Y')}}  </td>
                <td> Rp{{ number_format($item->harga_prediksi, 0, ',', '.') }}  </td>
                <td> {{$item->selisih_persen }}%  </td>
                <td> {{$item->status_anomali }}  </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>