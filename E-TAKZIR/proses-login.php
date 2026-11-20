<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header("Location: login.php?error=empty");
    exit();
}

$stmt = mysqli_prepare($koneksi, "SELECT id_user, username, password, role FROM users WHERE username = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if ($user && password_verify($password, $user['password'])) {
    session_regenerate_id(true);

    $_SESSION['user_id'] = (int)$user['id_user'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    if ($_SESSION['role'] == 'pengurus') {
        $q_prof = mysqli_query($koneksi, "SELECT * FROM pengurus WHERE id_user = " . (int)$user['id_user']);
        $prof = mysqli_fetch_assoc($q_prof);
        $_SESSION['nama_lengkap'] = $prof['nama_pengurus'] ?? $user['username'];
        $_SESSION['id_pengurus'] = (int)($prof['id_pengurus'] ?? 0);
        header("Location: pengurus/dashboard.php");
    } else if ($_SESSION['role'] == 'santri') {
        $q_prof = mysqli_query($koneksi, "SELECT * FROM santri WHERE id_user = " . (int)$user['id_user']);
        $prof = mysqli_fetch_assoc($q_prof);
        $_SESSION['nama_lengkap'] = $prof['nama_santri'] ?? $user['username'];
        $_SESSION['id_santri'] = (int)($prof['id_santri'] ?? 0);
        header("Location: santri/dashboard.php");
    } else if ($_SESSION['role'] == 'orang_tua') {
        $q_prof = mysqli_query($koneksi, "SELECT * FROM orang_tua WHERE id_user = " . (int)$user['id_user']);
        $prof = mysqli_fetch_assoc($q_prof);
        $_SESSION['nama_lengkap'] = $prof['nama_orang_tua'] ?? $user['username'];
        $_SESSION['id_santri'] = (int)($prof['id_santri'] ?? 0);
        header("Location: orang-tua/dashboard.php");
    } else {
        header("Location: login.php?error=role");
    }
    exit();
}

if (!$user) {
    header("Location: login.php?error=notfound");
    exit();
}

header("Location: login.php?error=wrongpassword");
exit();