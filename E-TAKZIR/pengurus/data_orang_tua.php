<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'pengurus') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

$pesan = "";

/* TAMBAH AKUN ORANG TUA */
if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama_orang_tua']);
    $no_hp = trim($_POST['no_hp']);
    $id_santri = (int) $_POST['id_santri'];
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $cek = mysqli_prepare($conn, "SELECT id_user FROM users WHERE username = ?");
    mysqli_stmt_bind_param($cek, "s", $username);
    mysqli_stmt_execute($cek);
    $hasil = mysqli_stmt_get_result($cek);

    if (mysqli_num_rows($hasil) > 0) {
        $pesan = "Username sudah digunakan!";
    } else {
        mysqli_begin_transaction($conn);

        try {
            $role = "orang_tua";

            $stmt1 = mysqli_prepare($conn,
                "INSERT INTO users (username, password, role) VALUES (?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt1, "sss", $username, $password, $role);
            mysqli_stmt_execute($stmt1);

            $id_user = mysqli_insert_id($conn);

            $stmt2 = mysqli_prepare($conn,
                "INSERT INTO orang_tua (id_user, id_santri, nama_orang_tua, no_hp)
                 VALUES (?, ?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt2, "iiss", $id_user, $id_santri, $nama, $no_hp);
            mysqli_stmt_execute($stmt2);

            mysqli_commit($conn);
            $pesan = "Akun orang tua berhasil dibuat!";
        } catch (Throwable $e) {
            mysqli_rollback($conn);
            $pesan = "Gagal membuat akun orang tua. Periksa kembali data.";
        }
    }
}

/* EDIT AKUN ORANG TUA */
if (isset($_POST['update'])) {
    $id_orang_tua = (int) $_POST['id_orang_tua'];
    $id_user = (int) $_POST['id_user'];
    $nama = trim($_POST['nama_orang_tua']);
    $no_hp = trim($_POST['no_hp']);
    $id_santri = (int) $_POST['id_santri'];
    $username = trim($_POST['username']);
    $password_baru = trim($_POST['password']);

    $cek = mysqli_prepare($conn,
        "SELECT id_user FROM users WHERE username = ? AND id_user != ?"
    );
    mysqli_stmt_bind_param($cek, "si", $username, $id_user);
    mysqli_stmt_execute($cek);
    $hasil = mysqli_stmt_get_result($cek);

    if (mysqli_num_rows($hasil) > 0) {
        $pesan = "Username sudah digunakan akun lain!";
    } else {
        mysqli_begin_transaction($conn);

        try {
            $stmt1 = mysqli_prepare($conn,
                "UPDATE orang_tua
                 SET nama_orang_tua = ?, no_hp = ?, id_santri = ?
                 WHERE id_orang_tua = ?"
            );
            mysqli_stmt_bind_param($stmt1, "ssii", $nama, $no_hp, $id_santri, $id_orang_tua);
            mysqli_stmt_execute($stmt1);

            if ($password_baru != "") {
                $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);

                $stmt2 = mysqli_prepare($conn,
                    "UPDATE users SET username = ?, password = ? WHERE id_user = ?"
                );
                mysqli_stmt_bind_param($stmt2, "ssi", $username, $password_hash, $id_user);
            } else {
                $stmt2 = mysqli_prepare($conn,
                    "UPDATE users SET username = ? WHERE id_user = ?"
                );
                mysqli_stmt_bind_param($stmt2, "si", $username, $id_user);
            }

            mysqli_stmt_execute($stmt2);

            mysqli_commit($conn);
            $pesan = "Data orang tua berhasil diperbarui!";
        } catch (Throwable $e) {
            mysqli_rollback($conn);
            $pesan = "Gagal memperbarui data orang tua.";
        }
    }
}

/* HAPUS AKUN ORANG TUA */
if (isset($_GET['hapus'])) {
    $id_orang_tua = (int) $_GET['hapus'];

    $stmt = mysqli_prepare($conn,
        "SELECT id_user FROM orang_tua WHERE id_orang_tua = ?"
    );
    mysqli_stmt_bind_param($stmt, "i", $id_orang_tua);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
    $orang_tua_hapus = mysqli_fetch_assoc($hasil);

    if ($orang_tua_hapus) {
        mysqli_begin_transaction($conn);

        try {
            $id_user_hapus = (int) $orang_tua_hapus['id_user'];

            $stmt1 = mysqli_prepare($conn,
                "DELETE FROM orang_tua WHERE id_orang_tua = ?"
            );
            mysqli_stmt_bind_param($stmt1, "i", $id_orang_tua);
            mysqli_stmt_execute($stmt1);

            $stmt2 = mysqli_prepare($conn,
                "DELETE FROM users WHERE id_user = ?"
            );
            mysqli_stmt_bind_param($stmt2, "i", $id_user_hapus);
            mysqli_stmt_execute($stmt2);

            mysqli_commit($conn);
            $pesan = "Data dan akun orang tua berhasil dihapus!";
        } catch (Throwable $e) {
            mysqli_rollback($conn);
            $pesan = "Gagal menghapus akun orang tua.";
        }
    } else {
        $pesan = "Data orang tua tidak ditemukan.";
    }
}

/* DATA UNTUK FORM EDIT */
$edit = null;

if (isset($_GET['edit'])) {
    $id_orang_tua = (int) $_GET['edit'];

    $stmt = mysqli_prepare($conn,
        "SELECT orang_tua.*, users.username
         FROM orang_tua
         JOIN users ON orang_tua.id_user = users.id_user
         WHERE orang_tua.id_orang_tua = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id_orang_tua);
    mysqli_stmt_execute($stmt);
    $hasil_edit = mysqli_stmt_get_result($stmt);
    $edit = mysqli_fetch_assoc($hasil_edit);
}

/* DATA SANTRI */
$santri = mysqli_query($conn,
    "SELECT id_santri, nama_santri FROM santri ORDER BY nama_santri ASC"
);

/* DAFTAR ORANG TUA */
$data = mysqli_query($conn,
    "SELECT orang_tua.*, santri.nama_santri, users.username
     FROM orang_tua
     JOIN santri ON orang_tua.id_santri = santri.id_santri
     JOIN users ON orang_tua.id_user = users.id_user
     ORDER BY orang_tua.id_orang_tua DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Orang Tua - E-TAKZIR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container py-4">

    <a href="dashboard.php" class="btn btn-secondary mb-3">Kembali</a>

    <h2>Data Orang Tua</h2>
    <p>Kelola akun orang tua dan hubungkan dengan santri.</p>

    <?php if ($pesan != "") { ?>
        <div class="alert alert-info">
            <?php echo htmlspecialchars($pesan); ?>
        </div>
    <?php } ?>

    <div class="card shadow-sm p-4 mb-4">

        <h5 class="mb-3">
            <?php echo $edit ? "Edit Data Orang Tua" : "Tambah Akun Orang Tua"; ?>
        </h5>

        <form method="POST">

            <?php if ($edit) { ?>
                <input type="hidden" name="id_orang_tua"
                    value="<?php echo $edit['id_orang_tua']; ?>">
                <input type="hidden" name="id_user"
                    value="<?php echo $edit['id_user']; ?>">
            <?php } ?>

            <div class="mb-3">
                <label class="form-label">Nama Orang Tua</label>
                <input type="text" name="nama_orang_tua" class="form-control"
                    value="<?php echo $edit ? htmlspecialchars($edit['nama_orang_tua']) : ''; ?>"
                    required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nomor HP</label>
                <input type="text" name="no_hp" class="form-control"
                    value="<?php echo $edit ? htmlspecialchars($edit['no_hp']) : ''; ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Santri</label>
                <select name="id_santri" class="form-select" required>
                    <option value="">Pilih santri</option>

                    <?php while ($s = mysqli_fetch_assoc($santri)) { ?>
                        <option value="<?php echo $s['id_santri']; ?>"
                            <?php
                            if ($edit && $edit['id_santri'] == $s['id_santri']) {
                                echo "selected";
                            }
                            ?>>
                            <?php echo htmlspecialchars($s['nama_santri']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <hr>
            <h6>Akun Login Orang Tua</h6>

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control"
                    value="<?php echo $edit ? htmlspecialchars($edit['username']) : ''; ?>"
                    required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password
                    <?php echo $edit ? "(Kosongkan jika tidak diganti)" : ""; ?>
                </label>
                <input type="password" name="password" class="form-control"
                    <?php echo $edit ? "" : "required"; ?>>
            </div>

            <?php if ($edit) { ?>
                <button type="submit" name="update" class="btn btn-primary">
                    Update Data
                </button>
                <a href="data_orang_tua.php" class="btn btn-secondary">Batal</a>
            <?php } else { ?>
                <button type="submit" name="simpan" class="btn btn-success">
                    Simpan Akun Orang Tua
                </button>
            <?php } ?>

        </form>
    </div>

    <div class="card shadow-sm p-4">
        <h5 class="mb-3">Daftar Orang Tua</h5>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-success">
                    <tr>
                        <th>No.</th>
                        <th>Nama Orang Tua</th>
                        <th>Nomor HP</th>
                        <th>Nama Santri</th>
                        <th>Username</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $no = 1; ?>
                    <?php while ($row = mysqli_fetch_assoc($data)) { ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($row['nama_orang_tua']); ?></td>
                            <td><?php echo htmlspecialchars($row['no_hp']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_santri']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td>
                                <a href="data_orang_tua.php?edit=<?php echo $row['id_orang_tua']; ?>"
                                   class="btn btn-warning btn-sm">Edit</a>

                                <a href="data_orang_tua.php?hapus=<?php echo $row['id_orang_tua']; ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin ingin menghapus akun orang tua ini? Akun login juga akan dihapus.');">
                                   Hapus
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>