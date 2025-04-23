<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id'];

if (!isset($_GET['id'])) {
    echo "ID pemesanan tidak ditemukan.";
    exit;
}

$id_pemesanan = $_GET['id'];

// Ambil data pemesanan
$query = "SELECT * FROM pemesanan WHERE id = '$id_pemesanan' AND id_user = '$id_user'";
$result = mysqli_query($conn, $query);
$pemesanan = mysqli_fetch_assoc($result);

if (!$pemesanan) {
    echo "Data pemesanan tidak ditemukan.";
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Status Pembayaran</title>
    <link rel="stylesheet" href="../assets/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Status Pembayaran</h2>
    <div class="card">
        <div class="card-header">
            <h4>Status Pembayaran Pemesanan #<?= htmlspecialchars($pemesanan['id']) ?></h4>
        </div>
        <div class="card-body">
            <p><strong>Metode Pembayaran:</strong> <?= htmlspecialchars($pemesanan['metode_pembayaran']) ?></p>
            <p><strong>Status Pembayaran:</strong> <?= htmlspecialchars($pemesanan['status_bayar']) ?></p>
            <p><strong>Total Pembayaran:</strong> Rp <?= number_format($pemesanan['total_bayar'], 0, ',', '.') ?></p>
            <?php if ($pemesanan['status_bayar'] == 'proses'): ?>
                <p>Mohon tunggu konfirmasi pembayaran dari admin.</p>
            <?php elseif ($pemesanan['status_bayar'] == 'lunas'): ?>
                <p>Pembayaran Anda sudah dikonfirmasi, tiket akan segera tersedia.</p>
            <?php else: ?>
                <p>Pembayaran Anda gagal, silakan coba lagi.</p>
            <?php endif; ?>
            <a href="pembayaran.php" class="btn btn-primary">Kembali ke Tiket Saya</a>
        </div>
    </div>
</div>
</body>
</html>
