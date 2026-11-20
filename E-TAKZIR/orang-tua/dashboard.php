<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'orang_tua') {
    header("Location: ../login.php");
    exit();
}
require '../config.php';

$id_santri = $_SESSION['id_santri'];

$q_santri = mysqli_query($koneksi, "SELECT * FROM santri WHERE id_santri = $id_santri");
$d_santri = mysqli_fetch_assoc($q_santri);

$q_poin = mysqli_query($koneksi, "SELECT SUM(poin) AS total_poin FROM pelanggaran WHERE id_santri = $id_santri");
$d_poin = mysqli_fetch_assoc($q_poin);
$total_poin = $d_poin['total_poin'] ?? 0;

$query = "SELECT pelanggaran.*, pengurus.nama_pengurus
          FROM pelanggaran
          JOIN pengurus ON pelanggaran.id_pengurus = pengurus.id_pengurus
          WHERE pelanggaran.id_santri = $id_santri
          ORDER BY pelanggaran.tanggal DESC";
$hasil_log = mysqli_query($koneksi, $query);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Wali Santri - E-Takzir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="#">Dashboard Wali Santri</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link text-warning" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h3>Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>!</h3>
    <p class="text-muted">
        Pemantauan Kedisiplinan Santri: <strong><?php echo htmlspecialchars($d_santri['nama_santri'] ?? '-'); ?></strong>
        (Kelas: <?php echo htmlspecialchars($d_santri['kelas'] ?? '-'); ?>) &mdash;
        Total Poin: <strong><?php echo $total_poin; ?> Poin</strong>
    </p>

    <div class="card mt-3">
        <div class="card-header">
            <h5>Riwayat Pelanggaran Anak</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis Pelanggaran</th>
                            <th>Poin</th>
                            <th>Keterangan</th>
                            <th>Dicatat Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($hasil_log)) : ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($row['tanggal'])); ?></td>
                            <td><?php echo htmlspecialchars($row['jenis_pelanggaran']); ?></td>
                            <td><?php echo $row['poin']; ?></td>
                            <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_pengurus']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>