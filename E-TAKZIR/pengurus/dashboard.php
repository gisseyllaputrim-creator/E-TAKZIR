<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pengurus') {
    header("Location: ../login.php");
    exit();
}
require '../config.php';

$q_santri = mysqli_query($koneksi, "SELECT * FROM santri ORDER BY nama_santri ASC");

$query = "SELECT pelanggaran.*, santri.nama_santri, santri.kelas, pengurus.nama_pengurus
          FROM pelanggaran
          JOIN santri ON pelanggaran.id_santri = santri.id_santri
          JOIN pengurus ON pelanggaran.id_pengurus = pengurus.id_pengurus
          ORDER BY pelanggaran.tanggal DESC, pelanggaran.id_pelanggaran DESC";
$hasil_log = mysqli_query($koneksi, $query);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Pengurus - E-Takzir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="#">Dashboard Pengurus</a>
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
    <?php
    if (isset($_SESSION['pesan'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        echo $_SESSION['pesan'];
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        unset($_SESSION['pesan']);
    }
    ?>

    <h3>Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>!</h3>
    <p class="text-muted">Kelola data pelanggaran dan takzir santri di pondok.</p>

    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Pelanggaran</button>

    <div class="card mt-2">
        <div class="card-header">
            <h5>Daftar Pelanggaran Santri</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Santri</th>
                            <th>Kelas</th>
                            <th>Jenis Pelanggaran</th>
                            <th>Poin</th>
                            <th>Keterangan</th>
                            <th>Pengurus</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($hasil_log)) : ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($row['tanggal'])); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_santri']); ?></td>
                            <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                            <td><?php echo htmlspecialchars($row['jenis_pelanggaran']); ?></td>
                            <td><span class="badge bg-danger"><?php echo $row['poin']; ?> Poin</span></td>
                            <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_pengurus']); ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo $row['id_pelanggaran']; ?>">Edit</button>
                                <a href="../hapus-pelanggaran.php?id=<?php echo $row['id_pelanggaran']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">Hapus</a>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEdit<?php echo $row['id_pelanggaran']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="../proses-edit.php" method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Pelanggaran</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id_pelanggaran" value="<?php echo $row['id_pelanggaran']; ?>">
                                            <input type="hidden" name="id_santri" value="<?php echo $row['id_santri']; ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal</label>
                                                <input type="date" class="form-control" name="tanggal" value="<?php echo $row['tanggal']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Jenis Pelanggaran</label>
                                                <input type="text" class="form-control" name="jenis_pelanggaran" value="<?php echo htmlspecialchars($row['jenis_pelanggaran']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Poin</label>
                                                <input type="number" class="form-control" name="poin" value="<?php echo $row['poin']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Keterangan</label>
                                                <textarea class="form-control" name="keterangan" rows="3"><?php echo htmlspecialchars($row['keterangan']); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../proses-tambah.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Pelanggaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Santri</label>
                        <select class="form-select" name="id_santri" required>
                            <option value="">-- Pilih Santri --</option>
                            <?php while ($s = mysqli_fetch_assoc($q_santri)) : ?>
                                <option value="<?php echo $s['id_santri']; ?>">
                                    <?php echo htmlspecialchars($s['nama_santri'] . ' (' . $s['kelas'] . ')'); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Pelanggaran</label>
                        <input type="text" class="form-control" name="jenis_pelanggaran" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Poin</label>
                        <input type="number" class="form-control" name="poin" value="5" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>