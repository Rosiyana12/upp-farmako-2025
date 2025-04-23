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
$query = "SELECT p.*, j.berangkat, j.tiba, j.tanggal, j.harga, k.nama_kereta 
          FROM pemesanan p 
          JOIN jadwal j ON p.id= j.id 
          JOIN kereta k ON j.id_kereta = k.id 
          WHERE p.id = '$id_pemesanan' AND p.id_user = '$id_user' AND p.status_bayar = 'sudah'";
$result = mysqli_query($conn, $query);
$pemesanan = mysqli_fetch_assoc($result);

if (!$pemesanan) {
    echo "Data pemesanan tidak ditemukan atau pembayaran belum diverifikasi.";
    exit;
}

// Ambil data penumpang
$penumpang_result = mysqli_query($conn, "SELECT * FROM penumpang WHERE id_pemesanan = '$id_pemesanan'");
$penumpangs = [];
while ($row = mysqli_fetch_assoc($penumpang_result)) {
    $penumpangs[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cetak Tiket</title>
    <link rel="stylesheet" href="../assets/bootstrap.min.css">
    <style>
        body { padding: 20px; background-color: #f7f7f7; }
        .card { max-width: 700px; margin: auto; }
    </style>
</head>
<body>
<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h4>Tiket Pemesanan</h4>
    </div>
    <div class="card-body">
        <p><strong>Nama Kereta:</strong> <?= htmlspecialchars($pemesanan['nama_kereta']) ?></p>
        <p><strong>Tanggal:</strong> <?= htmlspecialchars($pemesanan['tanggal']) ?></p>
        <p><strong>Waktu:</strong> <?= htmlspecialchars($pemesanan['berangkat']) ?> - <?= htmlspecialchars($pemesanan['tiba']) ?></p>
        <p><strong>Jumlah Tiket:</strong> <?= htmlspecialchars($pemesanan['jumlah_tiket']) ?></p>
        <p><strong>Total Bayar:</strong> Rp <?= number_format($pemesanan['total_bayar'], 0, ',', '.') ?></p>
        <p><strong>Status Pembayaran:</strong> <?= htmlspecialchars($pemesanan['status_bayar']) ?></p>

        <hr>
        
        <ul class="list-group mb-3">
            <?php foreach ($penumpangs as $p): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= isset($p['nama_penumpang']) ? htmlspecialchars($p['nama_penumpang']) : 'Nama tidak tersedia' ?>
                    <span class="badge badge-info badge-pill">
                        Kursi <?= isset($p['no_kursi']) ? htmlspecialchars($p['no_kursi']) : 'N/A' ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>

        <button onclick="window.print()" class="btn btn-primary">Cetak Tiket</button>
    </div>
</div>
</body>
</html>
