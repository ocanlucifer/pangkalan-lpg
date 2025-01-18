<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe29 - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #121212;
            color: #ffffff;
        }
        .login-card {
            margin-top: 100px;
            padding: 2rem;
            border: none;
            border-radius: 12px;
            background: #1e1e1e;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }
        .login-card h3 {
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #ffffff;
        }
        .btn-primary {
            background-color: #4CAF50;
            border: none;
        }
        .btn-primary:hover {
            background-color: #45a049;
        }
        .form-control {
            background-color: #2e2e2e;
            color: #ffffff; /* Warna teks */
            border: 1px solid #444;
        }
        .form-control:focus {
            background-color: #ffffff
            border-color: #4CAF50;
            box-shadow: none;
        }
        .form-label {
            font-weight: 500;
            color: #aaaaaa;
        }
        .alert {
            font-size: 0.875rem;
            margin-bottom: 1rem;
            background-color: #d9534f;
            border: none;
            color: #ffffff;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
        }
        a:hover {
            color: #45a049;
            text-decoration: underline;
        }
        /* Placeholder styling */
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.65); /* Placeholder transparan */
        }
        .form-control:-ms-input-placeholder { /* Internet Explorer 10-11 */
            color: rgba(255, 255, 255, 0.65);
        }
        .form-control::-ms-input-placeholder { /* Microsoft Edge */
            color: rgba(255, 255, 255, 0.65);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="login-card">
                    <h3 class="text-center">Cafe29 Login</h3>

                    <!-- Menampilkan error jika ada -->
                    @if($errors->has('login'))
                        <div class="alert alert-danger">
                            {{ $errors->first('login') }}
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="login" class="form-label">Username atau Email</label>
                            <input type="text" name="login" class="form-control" id="login" placeholder="Masukkan username atau email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="Masukkan password" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label for="remember" class="form-check-label">Ingat Saya</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
