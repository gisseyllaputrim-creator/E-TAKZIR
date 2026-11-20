
<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'pengurus') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

/* DATA RINGKASAN */
$total_santri = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM santri")
)['total'];

$total_orang_tua = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM orang_tua")
)['total'];

$total_pelanggaran = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM pelanggaran")
)['total'];

$total_poin = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COALESCE(SUM(poin), 0) AS total FROM pelanggaran")
)['total'];

/* DATA GRAFIK PELANGGARAN PER BULAN */
$label_bulan = [];
$data_pelanggaran = [];

$query_grafik = mysqli_query($conn,
    "SELECT DATE_FORMAT(tanggal, '%Y-%m') AS bulan,
            COUNT(*) AS jumlah
     FROM pelanggaran
     WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH)
     GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
     ORDER BY bulan ASC"
);

while ($grafik = mysqli_fetch_assoc($query_grafik)) {
    $label_bulan[] = date('M Y', strtotime($grafik['bulan'] . '-01'));
    $data_pelanggaran[] = (int) $grafik['jumlah'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengurus E-TAKZIR</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-success">
    <div class="container">
        <span class="navbar-brand fw-bold">E-TAKZIR</span>
        <a href="../logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
</nav>

<div class="container py-5">

    <h2>Dashboard Pengurus</h2>

    <p>
        Selamat datang,
        <?php echo htmlspecialchars($_SESSION['username']); ?>!
    </p>

    <h5 class="mt-4 mb-3">Ringkasan Data</h5>

    <div class="row g-3">

        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 p-3 h-100">
                <h6 class="text-muted">Total Santri</h6>
                <h2 class="text-success">
                    <?php echo $total_santri; ?>
                </h2>
                <small>Santri terdaftar</small>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 p-3 h-100">
                <h6 class="text-muted">Total Orang Tua</h6>
                <h2 class="text-primary">
                    <?php echo $total_orang_tua; ?>
                </h2>
                <small>Akun orang tua</small>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 p-3 h-100">
                <h6 class="text-muted">Total Pelanggaran</h6>
                <h2 class="text-danger">
                    <?php echo $total_pelanggaran; ?>
                </h2>
                <small>Catatan pelanggaran</small>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 p-3 h-100">
                <h6 class="text-muted">Total Poin</h6>
                <h2 class="text-warning">
                    <?php echo $total_poin; ?>
                </h2>
                <small>Akumulasi poin santri</small>
            </div>
        </div>

    </div>


    <!-- GRAFIK -->
    <div class="card shadow-sm border-0 p-4 mt-4">

        <h5 class="mb-3">Grafik Pelanggaran Santri</h5>

        <p class="text-muted">
            Jumlah pelanggaran berdasarkan bulan dalam 12 bulan terakhir.
        </p>

        <div style="height: 300px;">
            <canvas id="grafikPelanggaran"></canvas>
        </div>

    </div>


    <!-- MENU PENGELOLAAN -->
    <h5 class="mt-5 mb-3">Menu Pengelolaan</h5>

    <div class="row g-3">

        <div class="col-md-4">
            <div class="card shadow-sm p-3 h-100">
                <h5>Data Santri</h5>
                <p>Kelola data santri pondok pesantren.</p>
                <a href="data_santri.php" class="btn btn-success btn-sm">
                    Kelola Data Santri
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-3 h-100">
                <h5>Pelanggaran</h5>
                <p>Catat dan kelola pelanggaran santri.</p>
                <a href="data_pelanggaran.php" class="btn btn-success btn-sm">
                    Kelola Pelanggaran
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-3 h-100">
                <h5>Rekap Poin</h5>
                <p>Pantau poin pelanggaran santri.</p>
                <a href="rekap_poin.php" class="btn btn-success btn-sm">
                    Lihat Rekap Poin
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-3 h-100">
                <h5>Data Orang Tua</h5>
                <p>Kelola akun orang tua dan hubungkan dengan santri.</p>
                <a href="data_orang_tua.php" class="btn btn-success btn-sm">
                    Kelola Data Orang Tua
                </a>
            </div>
        </div>

    </div>

</div>


<script>
const labelBulan = <?php echo json_encode($label_bulan); ?>;
const jumlahPelanggaran = <?php echo json_encode($data_pelanggaran); ?>;

const ctx = document.getElementById('grafikPelanggaran');

new Chart(ctx, {
    type: 'bar',

    data: {
        labels: labelBulan,

        datasets: [{
            label: 'Jumlah Pelanggaran',

            data: jumlahPelanggaran,

            backgroundColor: 'rgba(25, 135, 84, 0.7)',
            borderColor: 'rgb(25, 135, 84)',
            borderWidth: 1,
            borderRadius: 5
        }]
    },

    options: {
        responsive: true,
        maintainAspectRatio: false,

        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});
</script>

</body>
</html>
