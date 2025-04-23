<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'pengguna') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_SESSION['id_user'];
    $id_jadwal = $_POST['id_jadwal'];
    $jumlah = $_POST['jumlah_tiket'];
    $penumpang = $_POST['nama_penumpang'];
    $kursi = $_POST['no_kursi'];
    $waktu = date('Y-m-d H:i:s');

    // Ambil harga tiket
    $query = $conn->prepare("SELECT harga FROM jadwal WHERE id = ?");
    $query->bind_param("i", $id_jadwal);
    $query->execute();
    $result = $query->get_result();
    $data = $result->fetch_assoc();
    $harga = $data['harga'];
    $total = $harga * $jumlah;

    // Simpan ke tabel pemesanan
    $stmt = $conn->prepare("INSERT INTO pemesanan (id_user, id_jadwal, jumlah_tiket, total_bayar, status_bayar, waktu_pemesanan) VALUES (?, ?, ?, ?, 'Belum Bayar', ?)");
    $stmt->bind_param("iiids", $id_user, $id_jadwal, $jumlah, $total, $waktu);
    $stmt->execute();
    $id_pemesanan = $stmt->insert_id;

    // Simpan ke tabel penumpang
    for ($i = 0; $i < $jumlah; $i++) {
        $stmt = $conn->prepare("INSERT INTO penumpang (id_pemesanan, nama_penumpang, no_kursi) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id_pemesanan, $penumpang[$i], $kursi[$i]);
        $stmt->execute();
    }

    header("Location: pembayaran.php?id=$id_pemesanan");
    exit();
} else {
    echo "Akses tidak sah.";
}
?>
