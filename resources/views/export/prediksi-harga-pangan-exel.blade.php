<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Data Prediksi Harga</title>
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
                <th colspan="8"
                    style="text-align: center;vertical-align: middle;font-size: 20px;height: 50px;color: black; font-weight: 900; border: none;">
                    DATA LAPORAN PREDIKSI HARGA PANGAN
                </th>
            </tr>
            <tr>
                <th colspan="8" style="text-align: center;vertical-align: middle; height: 30px;">
                    periode prediksi {{ Carbon\Carbon::parse($start)->translatedFormat('l, d F Y') }} - {{
                    Carbon\Carbon::parse($end)->translatedFormat('l, d F Y') }}
                </th>
            </tr>
            <tr>
                <th
                    style="border:solid; height: 25px; color:white; background-color: blue; font-weight: 600; text-align: center; vertical-align: middle;">
                    ‎ NO‎ </th>
                <th
                    style="border:solid; height: 25px; color:white; background-color: blue; font-weight: 600; text-align: center; vertical-align: middle;">
                    ‎ PASAR‎ </th>
                <th
                    style="border:solid; height: 25px; color:white; background-color: blue; font-weight: 600; text-align: center; vertical-align: middle;">
                    ‎ KOMODITAS‎ </th>
                <th
                    style="border:solid; height: 25px; color:white; background-color: blue; font-weight: 600; text-align: center; vertical-align: middle;">
                    ‎ TANGGAL PREDIKSI‎ </th>
                <th
                    style="border:solid; height: 25px; color:white; background-color: blue; font-weight: 600; text-align: center; vertical-align: middle;">
                    ‎ TARGET TANGGAL‎ </th>
                <th
                    style="border:solid; height: 25px; color:white; background-color: blue; font-weight: 600; text-align: center; vertical-align: middle;">
                    ‎ HARGA‎ </th>
                <th
                    style="border:solid; height: 25px; color:white; background-color: blue; font-weight: 600; text-align: center; vertical-align: middle;">
                    ‎ SELISIH‎ </th>

                <th style="border:solid; height: 25px; color:white; background-color: blue; font-weight: 600; text-align:
                center; vertical-align: middle;">
                    ‎ STATUS ANOMALI‎ </th>

            </tr>
        </thead>
        <tbody>
            <!-- Contoh data -->

            @foreach ($data as $item )
            <tr>
                <td style="border: solid; text-align: center; vertical-align: middle;">‎ {{$loop->iteration }} ‎ </td>
                <td style="border: solid">‎ {{$item?->pasar?->nama_pasar??'-' }} ‎ </td>
                <td style="border: solid">‎ {{$item?->komoditas?->nama_komoditas??'-' }} ‎ </td>
                <td style="border: solid">‎ {{Carbon\Carbon::parse($item->tanggal_prediksi)->format('d-m-Y')}} ‎ </td>
                <td style="border: solid">‎ {{Carbon\Carbon::parse($item->prediksi_harga_untuk_tanggal)->format('d-m-Y')}} ‎ </td>
                <td style="border: solid">‎ Rp{{ number_format($item->harga_prediksi, 0, ',', '.') }} ‎ </td>
                <td style="border: solid">‎ {{$item->selisih_persen }}% ‎ </td>
                <td style="border: solid">‎ {{$item->status_anomali }} ‎ </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>