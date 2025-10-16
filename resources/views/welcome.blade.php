<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kedai Mily</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Kedai Mily</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/daftar-menu">Daftar Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Penjualan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Pengaturan</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Selamat Datang di Dashboard Kedai Mily</h1>
        <p class="text-center">Ini adalah halaman utama menggantikan halaman welcome bawaan Laravel.</p>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">📋 Menu</h5>
                        <p class="card-text">Lihat daftar menu yang tersedia.</p>
                        <a href="/daftar-menu" class="btn btn-primary">Lihat Menu</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">💰 Penjualan</h5>
                        <p class="card-text">Laporan transaksi penjualan.</p>
                        <a href="#" class="btn btn-success">Lihat Laporan</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">⚙️ Pengaturan</h5>
                        <p class="card-text">Atur sistem Kedai Mily.</p>
                        <a href="#" class="btn btn-warning">Atur Sistem</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
