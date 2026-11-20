<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pengurus') {
    header("Location: login.php");
    exit();
}

$id_pelanggaran = $_GET['id'];

$query = "DELETE FROM pelanggaran WHERE id_pelanggaran = $id_pelanggaran";
$result = mysqli_query($koneksi, $query);

if ($result) {
    $_SESSION['pesan'] = "Data pelanggaran berhasil dihapus!";
    header("Location: pengurus/dashboard.php");
    exit();
} else {
    echo "Error: Gagal menghapus data. " . mysqli_error($koneksi);
}
?>