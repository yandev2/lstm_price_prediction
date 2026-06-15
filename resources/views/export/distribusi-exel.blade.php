<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Data Distribusi</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%
        }
    </style>
</head>

<body style="font-family: 'Arial', sans-serif;">
    <table>
        <thead>
            <tr>
                <th colspan="7"
                    style="text-align: center;vertical-align: middle;font-size: 20px;height: 50px;color: black; font-weight: 900; border: none;">
                    DATA LAPORAN DISTRIBUSI
                </th>
            </tr>
            <tr>
                <th colspan="7" style="text-align: center;vertical-align: middle; height: 30px;">
                    periode {{ Carbon\Carbon::parse($start)->translatedFormat('l, d F Y') }} - {{
                    Carbon\Carbon::parse($end)->translatedFormat('l, d F Y') }}
                </th>
            </tr>
            <tr>
                <th
                    style="border:solid; height: 25px; color:white; background-color: blue; font-weight: 600; text-align: center; vertical-align: middle;">
                    ‎ NO‎ </th>
                <th
                    style="border:solid; height: 25px; color:white; background-color: blue; font-weight: 600; text-align: center; vertical-align: middle;">
                    ‎ TANGGAL‎ </th>
                <th
                    style="border:solid; height: 25px; color:white; background-color: blue; font-weight: 600; text-align: center; vertical-align: middle;">
                    ‎ KOMODITAS‎ </th>
                <th
                    style="border:solid; height: 25px; color:white; background-color: blue; font-weight: 600; text-align: center; vertical-align: middle;">
                    ‎ PASAR ASAL‎ </th>
                <th
                    style="border:solid; height: 25px; color:white; background-color: blue; font-weight: 600; text-align: center; vertical-align: middle;">
                    ‎ HARGA TUJUAN‎ </th>

                <th
                    style="border:solid; height: 25px; color:white; background-color: blue; font-weight: 600; text-align: center; vertical-align: middle;">
                    ‎ TRANSPORTASI‎ </th>

                <th style="border:solid; height: 25px; color:white; background-color: blue; font-weight: 600; text-align:
                center; vertical-align: middle;">
                    ‎ VOLUME‎ </th>

            </tr>
        </thead>
        <tbody>
            <!-- Contoh data -->

            @foreach ($data as $item )
            <tr>
                <td style="border: solid; text-align: center; vertical-align: middle;">‎ {{$loop->iteration }} ‎ </td>
                <td style="border: solid">‎ {{Carbon\Carbon::parse($item->tanggal)->format('d-m-Y')}} ‎ </td>
                <td style="border: solid">‎ {{$item?->komoditas?->nama_komoditas??'-' }} ‎ </td>
                <td style="border: solid">‎ {{$item?->pasarAsal?->nama_pasar??'-' }} ‎ </td>
                <td style="border: solid">‎ {{$item?->pasarTujuan?->nama_pasar??'-' }} ‎ </td>
                <td style="border: solid">‎ {{$item->transportasi }} ‎ </td>
                <td style="border: solid">‎ {{$item->volume}} {{$item->komoditas->satuan}} ‎ </td>

            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>