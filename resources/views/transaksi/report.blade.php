<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
    }

    .container {
        width: 100%;
        max-width: 1000px;
        margin: 0 auto;
    }

    .header {
        text-align: center;
        margin-bottom: 40px;
    }

    .header h1 {
        font-size: 24px;
        margin: 0;
    }

    .header p {
        font-size: 16px;
        margin: 5px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 50px;
    }

    th,
    td {
        padding: 12px 15px;
        text-align: left;
        border: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    .signature {
        margin-top: 40px;
        display: flex;
        justify-content: space-between;
        width: 100%;
        font-size: 16px;
    }

    .signature div {
        text-align: center;
    }

    .footer {
        text-align: center;
        font-size: 12px;
        color: #777;
        margin-top: 30px;
    }

    @media print {
        body {
            margin: 0;
            padding: 0;
        }

        .footer {
            display: none;
        }

        .signature {
            margin-top: 20px;
        }

        .header p {
            display: none;
        }

        table {
            margin-bottom: 80px;
        }
    }
    </style>
    <title>CETAK DATA TRANSAKSI</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Laporan Data Transaksi</h1>
            @php
            use Carbon\Carbon;

            $bulan = request('bulan');
            $tahun = request('tahun');

            if ($bulan) {
            $namaBulan = Carbon::create(null, $bulan)->translatedFormat('F');
            } else {
            $namaBulan = 'Semua Bulan';
            }

            $periode = $tahun ? "$namaBulan $tahun" : ($bulan ? $namaBulan : 'Semua Periode');
            @endphp

            <p>Periode: {{ $periode }}</p>

        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaksi as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d F Y') }}</td>
                    <td>{{ optional($item->menu)->nama_menu ?? '-' }}</td>
                    <td>{{ $item->jumlah }}</td>
                    <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="signature">
            <div>
                <p>Mengetahui,</p>
                <p>pemilik</p>
                <br /><br /><br />
                <p>_____________________</p>
            </div>
            <div>
                <p>Banyuwangi, {{ date('d F Y') }}</p>
            </div>
        </div>

        <div class="footer">
            <p>Printed on {{ date('d F Y H:i:s') }}</p>
        </div>
    </div>

    <script type="text/javascript">
    window.print();
    </script>
</body>

</html>