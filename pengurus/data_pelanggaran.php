<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'pengurus') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

$pesan = "";

/* =========================
   TAMBAH PELANGGARAN
========================= */
if (isset($_POST['simpan'])) {
    $id_santri = $_POST['id_santri'];
    $jenis = trim($_POST['jenis_pelanggaran']);
    $poin = $_POST['poin'];
    $tanggal = $_POST['tanggal'];
    $keterangan = trim($_POST['keterangan']);
    $id_user = $_SESSION['id_user'];

    $pengurus = mysqli_query($conn,
        "SELECT id_pengurus FROM pengurus WHERE id_user = '$id_user'"
    );

    $data_pengurus = mysqli_fetch_assoc($pengurus);
    $id_pengurus = $data_pengurus['id_pengurus'];

    $stmt = mysqli_prepare($conn,
        "INSERT INTO pelanggaran
        (id_santri, id_pengurus, jenis_pelanggaran, poin, tanggal, keterangan)
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "iisiss",
        $id_santri,
        $id_pengurus,
        $jenis,
        $poin,
        $tanggal,
        $keterangan
    );

    if (mysqli_stmt_execute($stmt)) {
        $pesan = "Data pelanggaran berhasil disimpan!";
    } else {
        $pesan = "Gagal menyimpan data pelanggaran!";
    }
}


/* =========================
   EDIT PELANGGARAN
========================= */
if (isset($_POST['update'])) {
    $id_pelanggaran = $_POST['id_pelanggaran'];
    $id_santri = $_POST['id_santri'];
    $jenis = trim($_POST['jenis_pelanggaran']);
    $poin = $_POST['poin'];
    $tanggal = $_POST['tanggal'];
    $keterangan = trim($_POST['keterangan']);

    $stmt = mysqli_prepare($conn,
        "UPDATE pelanggaran
         SET id_santri = ?, jenis_pelanggaran = ?, poin = ?, tanggal = ?, keterangan = ?
         WHERE id_pelanggaran = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "isissi",
        $id_santri,
        $jenis,
        $poin,
        $tanggal,
        $keterangan,
        $id_pelanggaran
    );

    if (mysqli_stmt_execute($stmt)) {
        $pesan = "Data pelanggaran berhasil diperbarui!";
    } else {
        $pesan = "Gagal memperbarui data pelanggaran!";
    }
}


/* =========================
   HAPUS PELANGGARAN
========================= */
if (isset($_GET['hapus'])) {
    $id_pelanggaran = $_GET['hapus'];

    $stmt = mysqli_prepare($conn,
        "DELETE FROM pelanggaran WHERE id_pelanggaran = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id_pelanggaran);

    if (mysqli_stmt_execute($stmt)) {
        $pesan = "Data pelanggaran berhasil dihapus!";
    } else {
        $pesan = "Gagal menghapus data pelanggaran!";
    }
}


/* =========================
   DATA UNTUK FORM EDIT
========================= */
$edit = null;

if (isset($_GET['edit'])) {
    $id_pelanggaran = $_GET['edit'];

    $stmt = mysqli_prepare($conn,
        "SELECT * FROM pelanggaran WHERE id_pelanggaran = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id_pelanggaran);
    mysqli_stmt_execute($stmt);

    $hasil_edit = mysqli_stmt_get_result($stmt);
    $edit = mysqli_fetch_assoc($hasil_edit);
}


/* =========================
   DATA SANTRI
========================= */
$santri = mysqli_query($conn,
    "SELECT id_santri, nama_santri
     FROM santri
     ORDER BY nama_santri ASC"
);


/* =========================
   DATA PELANGGARAN
========================= */
$data = mysqli_query($conn,
    "SELECT pelanggaran.*, santri.nama_santri
     FROM pelanggaran
     JOIN santri ON pelanggaran.id_santri = santri.id_santri
     ORDER BY pelanggaran.tanggal DESC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Pelanggaran - E-TAKZIR</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <a href="dashboard.php" class="btn btn-secondary mb-3">
        Kembali
    </a>

    <h2>Data Pelanggaran</h2>

    <p>Catat dan kelola pelanggaran santri.</p>


    <?php if ($pesan != "") { ?>

        <div class="alert alert-info">
            <?php echo htmlspecialchars($pesan); ?>
        </div>

    <?php } ?>


    <!-- =========================
         FORM TAMBAH / EDIT
    ========================== -->

    <div class="card shadow-sm p-4 mb-4">

        <h5 class="mb-3">
            <?php
            if ($edit) {
                echo "Edit Pelanggaran";
            } else {
                echo "Tambah Pelanggaran";
            }
            ?>
        </h5>

        <form method="POST">

            <?php if ($edit) { ?>

                <input
                    type="hidden"
                    name="id_pelanggaran"
                    value="<?php echo $edit['id_pelanggaran']; ?>">

            <?php } ?>


            <div class="mb-3">

                <label class="form-label">
                    Nama Santri
                </label>

                <select
                    name="id_santri"
                    class="form-select"
                    required>

                    <option value="">
                        Pilih santri
                    </option>

                    <?php while ($s = mysqli_fetch_assoc($santri)) { ?>

                        <option
                            value="<?php echo $s['id_santri']; ?>"
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


            <div class="mb-3">

                <label class="form-label">
                    Jenis Pelanggaran
                </label>

                <input
                    type="text"
                    name="jenis_pelanggaran"
                    class="form-control"
                    value="<?php
                    echo $edit
                        ? htmlspecialchars($edit['jenis_pelanggaran'])
                        : '';
                    ?>"
                    required>

            </div>


            <div class="mb-3">

                <label class="form-label">
                    Poin Pelanggaran
                </label>

                <input
                    type="number"
                    name="poin"
                    class="form-control"
                    min="1"
                    value="<?php
                    echo $edit
                        ? htmlspecialchars($edit['poin'])
                        : '';
                    ?>"
                    required>

            </div>


            <div class="mb-3">

                <label class="form-label">
                    Tanggal
                </label>

                <input
                    type="date"
                    name="tanggal"
                    class="form-control"
                    value="<?php
                    echo $edit
                        ? htmlspecialchars($edit['tanggal'])
                        : '';
                    ?>"
                    required>

            </div>


            <div class="mb-3">

                <label class="form-label">
                    Keterangan
                </label>

                <textarea
                    name="keterangan"
                    class="form-control"
                    rows="3"><?php
                    echo $edit
                        ? htmlspecialchars($edit['keterangan'])
                        : '';
                    ?></textarea>

            </div>


            <?php if ($edit) { ?>

                <button
                    type="submit"
                    name="update"
                    class="btn btn-primary">

                    Update Pelanggaran

                </button>

                <a
                    href="data_pelanggaran.php"
                    class="btn btn-secondary">

                    Batal

                </a>

            <?php } else { ?>

                <button
                    type="submit"
                    name="simpan"
                    class="btn btn-success">

                    Simpan Pelanggaran

                </button>

            <?php } ?>

        </form>

    </div>


    <!-- =========================
         DAFTAR PELANGGARAN
    ========================== -->

    <div class="card shadow-sm p-4">

        <h5 class="mb-3">
            Daftar Pelanggaran
        </h5>

        <div class="table-responsive">

            <table class="table table-bordered table-striped">

                <thead class="table-success">

                    <tr>

                        <th>No.</th>
                        <th>Nama Santri</th>
                        <th>Jenis Pelanggaran</th>
                        <th>Poin</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>

                    </tr>

                </thead>

                <tbody>

                    <?php $no = 1; ?>

                    <?php while ($row = mysqli_fetch_assoc($data)) { ?>

                        <tr>

                            <td>
                                <?php echo $no++; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['nama_santri']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['jenis_pelanggaran']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['poin']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['tanggal']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['keterangan']); ?>
                            </td>

                            <td>

                                <a
                                    href="data_pelanggaran.php?edit=<?php echo $row['id_pelanggaran']; ?>"
                                    class="btn btn-warning btn-sm">

                                    Edit

                                </a>

                                <a
                                    href="data_pelanggaran.php?hapus=<?php echo $row['id_pelanggaran']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus data pelanggaran ini?');">

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