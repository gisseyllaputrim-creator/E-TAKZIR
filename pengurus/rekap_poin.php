<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'pengurus') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

$data = mysqli_query($conn, "
    SELECT 
        santri.id_santri,
        santri.nama_santri,
        santri.kelas,
        COUNT(pelanggaran.id_pelanggaran) AS jumlah_pelanggaran,
        COALESCE(SUM(pelanggaran.poin), 0) AS total_poin
    FROM santri
    LEFT JOIN pelanggaran 
        ON santri.id_santri = pelanggaran.id_santri
    GROUP BY santri.id_santri, santri.nama_santri, santri.kelas
    ORDER BY total_poin DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Poin - E-TAKZIR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container py-4">

    <a href="dashboard.php" class="btn btn-secondary mb-3">Kembali</a>

    <h2>Rekap Poin Pelanggaran</h2>
    <p>Ringkasan jumlah pelanggaran dan total poin setiap santri.</p>

    <div class="card shadow-sm p-4">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-success">
                    <tr>
                        <th>No.</th>
                        <th>Nama Santri</th>
                        <th>Kelas</th>
                        <th>Jumlah Pelanggaran</th>
                        <th>Total Poin</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $no = 1; ?>
                    <?php while ($row = mysqli_fetch_assoc($data)) { ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($row['nama_santri']); ?></td>
                            <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                            <td><?php echo $row['jumlah_pelanggaran']; ?></td>
                            <td>
                                <strong><?php echo $row['total_poin']; ?></strong>
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