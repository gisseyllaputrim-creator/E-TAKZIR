<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pengurus') {
    header("Location: login.php");
    exit();
}

$id_pelanggaran = $_POST['id_pelanggaran'];
$id_santri = $_POST['id_santri'];
$jenis_pelanggaran = $_POST['jenis_pelanggaran'];
$poin = $_POST['poin'];
$tanggal = $_POST['tanggal'];
$keterangan = $_POST['keterangan'];

$query = "UPDATE pelanggaran SET
            id_santri = '$id_santri',
            jenis_pelanggaran = '$jenis_pelanggaran',
            poin = '$poin',
            tanggal = '$tanggal',
            keterangan = '$keterangan'
          WHERE id_pelanggaran = $id_pelanggaran";

$result = mysqli_query($koneksi, $query);

if ($result) {
    $_SESSION['pesan'] = "Data pelanggaran berhasil diubah!";
    header("Location: pengurus/dashboard.php");
    exit();
} else {
    echo "Error: Gagal mengupdate data. " . mysqli_error($koneksi);
}
?>