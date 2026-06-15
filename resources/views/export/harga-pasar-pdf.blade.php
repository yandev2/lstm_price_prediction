<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Data Harga</title>
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
                    DATA LAPORAN HARGA PANGAN <br>
                    <span style="textlign: center; font-size: 10px; font-weight: 200;"> periode {{
                        Carbon\Carbon::parse($start)->translatedFormat('l, d F Y') }} - {{
                        Carbon\Carbon::parse($end)->translatedFormat('l, d F Y') }}</span>
                </th>
            </tr>
            <tr>
                <th style="width: 1%;">NO</th>
                <th style="width: 8%;">TANGGAL</th>
                <th style="width: 13%;">KOMODITAS</th>
                <th style="width: 17%;">PASAR</th>
                <th style="width: 7%;">HARGA</th>
                <th style="width: 20%;">FAKTOR EKSTERNAL</th>
                <th style="width: 20%;">SUMBER DATA</th>
            </tr>
        </thead>
        <tbody>
            <!-- Contoh data -->

            @foreach ($data as $item )
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td> {{Carbon\Carbon::parse($item->tanggal)->format('d-m-Y')}}</td>
                <td> {{$item->komoditas->nama_komoditas??'' }}</td>
                <td> {{$item->pasar->nama_pasar??'' }}</td>
                <td>Rp{{ number_format($item->harga, 0, ',', '.') }}</td>
                <td>{{ !empty($item->faktor_eksternal) ? implode(', ', $item->faktor_eksternal)
                    : '-' }}</td>
                <td> {{$item->sumber_data?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>