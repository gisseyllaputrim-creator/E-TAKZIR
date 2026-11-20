<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'pengurus') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

$pesan = "";

// TAMBAH DATA SANTRI
if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama_santri']);
    $kelas = trim($_POST['kelas']);
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $cek = mysqli_prepare($conn,
        "SELECT id_user FROM users WHERE username = ?"
    );
    mysqli_stmt_bind_param($cek, "s", $username);
    mysqli_stmt_execute($cek);
    $hasil = mysqli_stmt_get_result($cek);

    if (mysqli_num_rows($hasil) > 0) {
        $pesan = "Username sudah digunakan!";
    } else {
        mysqli_begin_transaction($conn);

        try {
            $role = "santri";

            $stmt1 = mysqli_prepare($conn,
                "INSERT INTO users (username, password, role)
                 VALUES (?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt1, "sss",
                $username, $password, $role
            );
            mysqli_stmt_execute($stmt1);

            $id_user = mysqli_insert_id($conn);

            $stmt2 = mysqli_prepare($conn,
                "INSERT INTO santri
                 (id_user, nama_santri, kelas, jenis_kelamin)
                 VALUES (?, ?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt2, "isss",
                $id_user, $nama, $kelas, $jenis_kelamin
            );
            mysqli_stmt_execute($stmt2);

            mysqli_commit($conn);
            $pesan = "Data santri berhasil ditambahkan!";

        } catch (Throwable $e) {
            mysqli_rollback($conn);
            $pesan = "Gagal menambahkan data santri!";
        }
    }
}

// EDIT DATA SANTRI
if (isset($_POST['update'])) {
    $id_santri = (int) $_POST['id_santri'];
    $nama = trim($_POST['nama_santri']);
    $kelas = trim($_POST['kelas']);
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $username = trim($_POST['username']);
    $password_baru = $_POST['password'];

    $cek = mysqli_prepare($conn,
        "SELECT id_user FROM santri WHERE id_santri = ?"
    );
    mysqli_stmt_bind_param($cek, "i", $id_santri);
    mysqli_stmt_execute($cek);
    $hasil_cek = mysqli_stmt_get_result($cek);
    $santri_lama = mysqli_fetch_assoc($hasil_cek);

    if (!$santri_lama) {
        $pesan = "Data santri tidak ditemukan!";
    } else {
        $id_user = $santri_lama['id_user'];

        $cek_username = mysqli_prepare($conn,
            "SELECT id_user FROM users
             WHERE username = ? AND id_user != ?"
        );
        mysqli_stmt_bind_param($cek_username, "si",
            $username, $id_user
        );
        mysqli_stmt_execute($cek_username);
        $hasil_username = mysqli_stmt_get_result($cek_username);

        if (mysqli_num_rows($hasil_username) > 0) {
            $pesan = "Username sudah digunakan akun lain!";
        } else {
            mysqli_begin_transaction($conn);

            try {
                $stmt1 = mysqli_prepare($conn,
                    "UPDATE santri
                     SET nama_santri = ?, kelas = ?, jenis_kelamin = ?
                     WHERE id_santri = ?"
                );
                mysqli_stmt_bind_param($stmt1, "sssi",
                    $nama, $kelas, $jenis_kelamin, $id_santri
                );
                mysqli_stmt_execute($stmt1);

                if ($password_baru != "") {
                    $password_hash = password_hash(
                        $password_baru,
                        PASSWORD_DEFAULT
                    );

                    $stmt2 = mysqli_prepare($conn,
                        "UPDATE users
                         SET username = ?, password = ?
                         WHERE id_user = ?"
                    );
                    mysqli_stmt_bind_param($stmt2, "ssi",
                        $username, $password_hash, $id_user
                    );
                } else {
                    $stmt2 = mysqli_prepare($conn,
                        "UPDATE users
                         SET username = ?
                         WHERE id_user = ?"
                    );
                    mysqli_stmt_bind_param($stmt2, "si",
                        $username, $id_user
                    );
                }

                mysqli_stmt_execute($stmt2);

                mysqli_commit($conn);
                $pesan = "Data santri berhasil diperbarui!";

            } catch (Throwable $e) {
                mysqli_rollback($conn);
                $pesan = "Gagal memperbarui data santri!";
            }
        }
    }
}

// HAPUS DATA SANTRI
if (isset($_POST['hapus'])) {
    $id_santri = (int) $_POST['id_santri'];

    $cek = mysqli_prepare($conn,
        "SELECT id_user FROM santri WHERE id_santri = ?"
    );
    mysqli_stmt_bind_param($cek, "i", $id_santri);
    mysqli_stmt_execute($cek);
    $hasil_cek = mysqli_stmt_get_result($cek);
    $santri_hapus = mysqli_fetch_assoc($hasil_cek);

    if (!$santri_hapus) {
        $pesan = "Data santri tidak ditemukan!";
    } else {
        $cek_pelanggaran = mysqli_prepare($conn,
            "SELECT COUNT(*) AS jumlah
             FROM pelanggaran WHERE id_santri = ?"
        );
        mysqli_stmt_bind_param($cek_pelanggaran, "i", $id_santri);
        mysqli_stmt_execute($cek_pelanggaran);
        $hasil_pelanggaran = mysqli_stmt_get_result($cek_pelanggaran);
        $jumlah_pelanggaran = mysqli_fetch_assoc($hasil_pelanggaran);

        $cek_orang_tua = mysqli_prepare($conn,
            "SELECT COUNT(*) AS jumlah
             FROM orang_tua WHERE id_santri = ?"
        );
        mysqli_stmt_bind_param($cek_orang_tua, "i", $id_santri);
        mysqli_stmt_execute($cek_orang_tua);
        $hasil_orang_tua = mysqli_stmt_get_result($cek_orang_tua);
        $jumlah_orang_tua = mysqli_fetch_assoc($hasil_orang_tua);

        if ($jumlah_pelanggaran['jumlah'] > 0 ||
            $jumlah_orang_tua['jumlah'] > 0) {

            $pesan = "Santri tidak dapat dihapus karena masih memiliki data pelanggaran atau akun orang tua.";

        } else {
            mysqli_begin_transaction($conn);

            try {
                $id_user = $santri_hapus['id_user'];

                $hapus_santri = mysqli_prepare($conn,
                    "DELETE FROM santri WHERE id_santri = ?"
                );
                mysqli_stmt_bind_param($hapus_santri, "i", $id_santri);
                mysqli_stmt_execute($hapus_santri);

                $hapus_user = mysqli_prepare($conn,
                    "DELETE FROM users WHERE id_user = ?"
                );
                mysqli_stmt_bind_param($hapus_user, "i", $id_user);
                mysqli_stmt_execute($hapus_user);

                mysqli_commit($conn);
                $pesan = "Data santri berhasil dihapus!";

            } catch (Throwable $e) {
                mysqli_rollback($conn);
                $pesan = "Gagal menghapus data santri!";
            }
        }
    }
}

// AMBIL DATA UNTUK FORM EDIT
$editData = null;

if (isset($_GET['edit'])) {
    $id_edit = (int) $_GET['edit'];

    $stmt = mysqli_prepare($conn,
        "SELECT santri.*, users.username
         FROM santri
         JOIN users ON santri.id_user = users.id_user
         WHERE santri.id_santri = ?"
    );
    mysqli_stmt_bind_param($stmt, "i", $id_edit);
    mysqli_stmt_execute($stmt);
    $hasil_edit = mysqli_stmt_get_result($stmt);
    $editData = mysqli_fetch_assoc($hasil_edit);
}

// DAFTAR SANTRI
$data = mysqli_query($conn,
    "SELECT santri.*, users.username
     FROM santri
     JOIN users ON santri.id_user = users.id_user
     ORDER BY santri.id_santri DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Santri - E-TAKZIR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <a href="dashboard.php" class="btn btn-secondary mb-3">Kembali</a>

    <h2>Data Santri</h2>
    <p>Kelola data santri E-TAKZIR.</p>

    <?php if ($pesan != "") { ?>
        <div class="alert alert-info">
            <?php echo htmlspecialchars($pesan); ?>
        </div>
    <?php } ?>

    <div class="card shadow-sm p-4 mb-4">

        <?php if ($editData) { ?>

            <h5 class="mb-3">Edit Data Santri</h5>

            <form method="POST">
                <input type="hidden" name="id_santri"
                    value="<?php echo $editData['id_santri']; ?>">

                <div class="mb-3">
                    <label class="form-label">Nama Santri</label>
                    <input type="text" name="nama_santri"
                        class="form-control"
                        value="<?php echo htmlspecialchars($editData['nama_santri']); ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kelas</label>
                    <input type="text" name="kelas"
                        class="form-control"
                        value="<?php echo htmlspecialchars($editData['kelas']); ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select" required>
                        <option value="Laki-laki"
                            <?php echo $editData['jenis_kelamin'] == 'Laki-laki' ? 'selected' : ''; ?>>
                            Laki-laki
                        </option>
                        <option value="Perempuan"
                            <?php echo $editData['jenis_kelamin'] == 'Perempuan' ? 'selected' : ''; ?>>
                            Perempuan
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username"
                        class="form-control"
                        value="<?php echo htmlspecialchars($editData['username']); ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password"
                        class="form-control">
                    <small class="text-muted">
                        Kosongkan jika tidak ingin mengganti password.
                    </small>
                </div>

                <button type="submit" name="update" class="btn btn-success">
                    Simpan Perubahan
                </button>

                <a href="data_santri.php" class="btn btn-secondary">
                    Batal
                </a>
            </form>

        <?php } else { ?>

            <h5 class="mb-3">Tambah Santri</h5>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Nama Santri</label>
                    <input type="text" name="nama_santri"
                        class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kelas</label>
                    <input type="text" name="kelas"
                        class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select" required>
                        <option value="">Pilih jenis kelamin</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>

                <hr>
                <h6>Akun Login Santri</h6>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username"
                        class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password"
                        class="form-control" required>
                </div>

                <button type="submit" name="simpan" class="btn btn-success">
                    Simpan Data Santri
                </button>

            </form>

        <?php } ?>

    </div>

    <div class="card shadow-sm p-4">

        <h5 class="mb-3">Daftar Santri</h5>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">

                <thead class="table-success">
                    <tr>
                        <th>No.</th>
                        <th>Nama Santri</th>
                        <th>Kelas</th>
                        <th>Jenis Kelamin</th>
                        <th>Username</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $no = 1; ?>

                    <?php while ($row = mysqli_fetch_assoc($data)) { ?>
                        <tr>
                            <td><?php echo $no++; ?></td>

                            <td>
                                <?php echo htmlspecialchars($row['nama_santri']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['kelas']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['jenis_kelamin']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['username']); ?>
                            </td>

                            <td>
                                <a href="data_santri.php?edit=<?php echo $row['id_santri']; ?>"
                                   class="btn btn-warning btn-sm mb-1">
                                    Edit
                                </a>

                                <form method="POST" class="d-inline"
                                    onsubmit="return confirm('Yakin ingin menghapus data santri ini?');">

                                    <input type="hidden" name="id_santri"
                                        value="<?php echo $row['id_santri']; ?>">

                                    <button type="submit" name="hapus"
                                        class="btn btn-danger btn-sm mb-1">
                                        Hapus
                                    </button>

                                </form>
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