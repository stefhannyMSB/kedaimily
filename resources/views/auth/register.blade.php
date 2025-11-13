<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Kedai Mily</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e8f5e9;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .card {
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .btn-success {
            background-color: #2e7d32;
            border: none;
        }
        .btn-success:hover {
            background-color: #1b5e20;
        }
        a.text-success:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="card p-4" style="width: 380px;">
        <h4 class="text-center text-success mb-3">Daftar Akun</h4>

        @if ($errors->any())
            <div class="alert alert-danger small">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('register.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Daftar</button>
        </form>

        <p class="text-center mt-3 mb-0">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-success">Login</a>
        </p>
    </div>
</body>
</html>
