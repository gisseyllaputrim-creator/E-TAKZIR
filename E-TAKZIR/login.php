<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - E-Takzir Pondok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .login-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { max-width: 400px; width: 100%; }
    </style>
</head>
<body>
<div class="container login-container">
    <div class="card shadow-sm login-card">
        <div class="card-body p-5">
            <?php
            if (isset($_GET['error'])) {
                $pesan_error = "Terjadi kesalahan saat login.";
                if ($_GET['error'] == 'notfound') {
                    $pesan_error = "Username tidak ditemukan!";
                } else if ($_GET['error'] == 'wrongpassword') {
                    $pesan_error = "Password salah!";
                } else if ($_GET['error'] == 'empty') {
                    $pesan_error = "Username dan password wajib diisi!";
                } else if ($_GET['error'] == 'role') {
                    $pesan_error = "Role akun tidak dikenali!";
                }
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                echo $pesan_error;
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                echo '</div>';
            }
            ?>
            <h3 class="card-title text-center mb-2">E-Takzir Pondok</h3>
            <p class="text-center text-muted mb-4">Sistem Pencatatan Pelanggaran & Kedisiplinan</p>

            <form action="proses-login.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>