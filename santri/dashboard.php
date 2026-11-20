<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'santri') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

$id_user = $_SESSION['id_user'];

$query = mysqli_prepare($conn, "
    SELECT * FROM santri
    WHERE id_user = ?
");
mysqli_stmt_bind_param($query, "i", $id_user);
mysqli_stmt_execute($query);
$hasil = mysqli_stmt_get_result($query);
$santri = mysqli_fetch_assoc($hasil);

if (!$santri) {
    die("Data santri tidak ditemukan.");
}

$id_santri = $santri['id_santri'];

$riwayat = mysqli_prepare($conn, "
    SELECT * FROM pelanggaran
    WHERE id_santri = ?
    ORDER BY tanggal DESC
");
mysqli_stmt_bind_param($riwayat, "i", $id_santri);
mysqli_stmt_execute($riwayat);
$data_pelanggaran = mysqli_stmt_get_result($riwayat);

$total = mysqli_prepare($conn, "
    SELECT COALESCE(SUM(poin), 0) AS total_poin
    FROM pelanggaran
    WHERE id_santri = ?
");
mysqli_stmt_bind_param($total, "i", $id_santri);
mysqli_stmt_execute($total);
$hasil_total = mysqli_stmt_get_result($total);
$jumlah_poin = mysqli_fetch_assoc($hasil_total);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Santri E-TAKZIR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-success">
    <div class="container">
        <span class="navbar-brand fw-bold">E-TAKZIR</span>
        <a href="../logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
</nav>

<div class="container py-5">

    <h2>Dashboard Santri</h2>
    <p>Selamat datang, <?php echo htmlspecialchars($santri['nama_santri']); ?>!</p>

    <div class="row g-3 mt-3">

        <div class="col-md-6">
            <div class="card shadow-sm p-4 h-100">
                <h5>Data Diri</h5>
                <hr>
                <p><strong>Nama:</strong> <?php echo htmlspecialchars($santri['nama_santri']); ?></p>
                <p><strong>Kelas:</strong> <?php echo htmlspecialchars($santri['kelas']); ?></p>
                <p><strong>Jenis Kelamin:</strong> <?php echo htmlspecialchars($santri['jenis_kelamin']); ?></p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm p-4 h-100">
                <h5>Total Poin Pelanggaran</h5>
                <h1 class="text-success mt-3">
                    <?php echo $jumlah_poin['total_poin']; ?>
                </h1>
                <p class="text-muted">Jumlah seluruh poin pelanggaran kamu.</p>
            </div>
        </div>

    </div>

    <div class="card shadow-sm p-4 mt-4">
        <h5>Riwayat Pelanggaran</h5>

        <div class="table-responsive mt-3">
            <table class="table table-bordered table-striped">
                <thead class="table-success">
                    <tr>
                        <th>No.</th>
                        <th>Jenis Pelanggaran</th>
                        <th>Poin</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (mysqli_num_rows($data_pelanggaran) > 0) { ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($data_pelanggaran)) { ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['jenis_pelanggaran']); ?></td>
                                <td><?php echo $row['poin']; ?></td>
                                <td><?php echo htmlspecialchars($row['tanggal']); ?></td>
                                <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="5" class="text-center">Belum ada riwayat pelanggaran.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>