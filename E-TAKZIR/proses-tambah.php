<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pengurus') {
    header("Location: login.php");
    exit();
}

$id_santri = $_POST['id_santri'];
$id_pengurus = $_SESSION['id_pengurus'];
$jenis_pelanggaran = $_POST['jenis_pelanggaran'];
$poin = $_POST['poin'];
$tanggal = $_POST['tanggal'];
$keterangan = $_POST['keterangan'];

$query = "INSERT INTO pelanggaran (id_santri, id_pengurus, jenis_pelanggaran, poin, tanggal, keterangan)
          VALUES ('$id_santri', '$id_pengurus', '$jenis_pelanggaran', '$poin', '$tanggal', '$keterangan')";

$result = mysqli_query($koneksi, $query);

if ($result) {
    $_SESSION['pesan'] = "Data pelanggaran berhasil ditambahkan!";
    header("Location: pengurus/dashboard.php");
    exit();
} else {
    echo "Error: Gagal menyimpan data. " . mysqli_error($koneksi);
}
?>