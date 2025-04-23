<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id'];

if (!isset($_POST['id_pemesanan']) || !isset($_POST['metode'])) {
    echo "Data pemesanan atau metode pembayaran tidak valid.";
    exit;
}

$id_pemesanan = $_POST['id_pemesanan'];
$metode = $_POST['metode'];

// Validasi metode pembayaran
$valid_metode = ['transfer_bank', 'ovo', 'gopay', 'dana', 'kartu_kredit'];
if (!in_array($metode, $valid_metode)) {
    echo "Metode pembayaran tidak valid.";
    exit;
}

// Update status pembayaran menjadi 'menunggu verifikasi' atau 'belum dibayar'
$query = "UPDATE pemesanan SET status_bayar = 'menunggu', metode_pembayaran = '$metode' WHERE id = '$id_pemesanan' AND id_user = '$id_user'";

if (mysqli_query($conn, $query)) {
    // Pembayaran berhasil diproses
    header("Location: status_pembayaran.php?id=$id_pemesanan");
    exit;
} else {
    echo "Terjadi kesalahan saat memproses pembayaran.";
    exit;
}
?>
