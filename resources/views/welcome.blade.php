<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kedai Mily</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    {{-- NAVBAR --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-warning" href="#">Kedai Mily</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="/">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/menu">Data Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="/penjualan">Data Penjualan</a></li>
                    <li class="nav-item"><a class="nav-link" href="/peramalan">Peramalan</a></li>
                </ul>

                {{-- TOMBOL LOGOUT --}}
                <form action="{{ route('logout') }}" method="POST" class="d-flex">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- KONTEN DASHBOARD --}}
    <div class="container mt-5">
        <h1 class="text-center mb-4">Selamat Datang di Dashboard Kedai Mily</h1>
        <p class="text-center">Ini adalah halaman utama menggantikan halaman welcome bawaan Laravel.</p>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">📋 Menu</h5>
                        <p class="card-text">Lihat daftar menu yang tersedia.</p>
                        <a href="/menu" class="btn btn-primary">Lihat Menu</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">💰 Penjualan</h5>
                        <p class="card-text">Laporan transaksi penjualan.</p>
                        <a href="/penjualan" class="btn btn-success">Lihat Laporan</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">📊 Peramalan</h5>
                        <p class="card-text">Lihat hasil prediksi penjualan.</p>
                        <a href="/peramalan" class="btn btn-info">Lihat Peramalan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</body>
</html>
