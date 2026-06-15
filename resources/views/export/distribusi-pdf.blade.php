<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Data Distribusi</title>
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
                <th colspan="7" style="text-align:center; font-size:15px; height:50px;">
                    DATA LAPORAN DISTRIBUSI <br>
                    <span style="textlign: center; font-size: 10px; font-weight: 200;"> periode {{
                        Carbon\Carbon::parse($start)->translatedFormat('l, d F Y') }} - {{
                        Carbon\Carbon::parse($end)->translatedFormat('l, d F Y') }}</span>
                </th>
            </tr>
            <tr>
                <th style="width: 1%;">NO</th>
                <th style="width: 8%;">TANGGAL</th>
                <th style="width: 15%;">KOMODITAS</th>
                <th style="width: 18%;">PASAR ASAL</th>
                <th style="width: 18%;"> PASAR TUJUAN</th>
                <th style="width: 10%;">TRANSPORTASI</th>
                <th style="width: 8%;">VOLUME</th>
            </tr>
        </thead>
        <tbody>
            <!-- Contoh data -->

            @foreach ($data as $item )
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td> {{Carbon\Carbon::parse($item->tanggal)->format('d-m-Y')}}</td>
                <td> {{$item->komoditas->nama_komoditas??'' }}</td>
                <td> {{$item->pasarAsal?->nama_pasar??'' }}</td>
                <td> {{$item->pasarTujuan?->nama_pasar??'' }}</td>
                <td> {{$item->transportasi?? '' }}</td>
                <td> {{$item->volume}} {{$item->komoditas->satuan}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>