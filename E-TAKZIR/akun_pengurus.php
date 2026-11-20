<?php
include 'koneksi.php';

$pesan = "";

if (isset($_POST['daftar'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");

    if (mysqli_num_rows($cek) > 0) {
        $pesan = "Username sudah digunakan!";
    } else {
        $query = mysqli_query($conn, "INSERT INTO users (username, password, role)
        VALUES ('$username', '$password', 'pengurus')");

        if ($query) {
            $id_user = mysqli_insert_id($conn);

            mysqli_query($conn, "INSERT INTO pengurus (id_user, nama_pengurus)
            VALUES ('$id_user', '$nama')");

            $pesan = "Akun pengurus berhasil dibuat!";
        } else {
            $pesan = "Gagal membuat akun!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Akun Pengurus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow p-4 mx-auto" style="max-width:450px">
        <h3 class="text-center">Akun Pengurus E-TAKZIR</h3>

        <?php if ($pesan != "") { ?>
            <div class="alert alert-info"><?php echo $pesan; ?></div>
        <?php } ?>

        <form method="POST">
            <label class="form-label">Nama Pengurus</label>
            <input type="text" name="nama" class="form-control mb-3" required>

            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control mb-3" required>

            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control mb-3" required>

            <button type="submit" name="daftar" class="btn btn-success w-100">
                Buat Akun
            </button>
        </form>
    </div>
</div>

</body>
</html>