
<?php
session_start();
include 'koneksi.php';

$pesan = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    $user = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

    if ($user['role'] == 'pengurus') {
        header("Location: pengurus/dashboard.php");
    } elseif ($user['role'] == 'santri') {
        header("Location: santri/dashboard.php");
    } elseif ($user['role'] == 'orang_tua') {
        header("Location: orang_tua/dashboard.php");
    }
        exit;
    } else {
        $pesan = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login E-TAKZIR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow p-4" style="width: 400px;">
        <h3 class="text-center mb-2">E-TAKZIR</h3>
        <p class="text-center text-muted">Sistem Pencatatan Pelanggaran dan Kedisiplinan</p>

        <?php if ($pesan != "") { ?>
            <div class="alert alert-danger">
                <?php echo $pesan; ?>
            </div>
        <?php } ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" name="login" class="btn btn-success w-100">
                Masuk
            </button>
        </form>
    </div>
</div>

</body>
</html>
