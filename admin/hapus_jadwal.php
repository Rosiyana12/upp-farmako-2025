<?php
session_start();

// Validasi role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../koneksi.php';

// Cek apakah ada ID yang dikirimkan
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Hapus data jadwal dari database
    $query = "DELETE FROM jadwal WHERE id = $id";
    $result = mysqli_query($conn, $query);

    if ($result) {
        // Jika berhasil, redirect ke halaman kelola jadwal
        header('Location: kelola_jadwal.php?status=success');
    } else {
        // Jika gagal, tampilkan pesan error
        die("Gagal menghapus jadwal: " . mysqli_error($conn));
    }
} else {
    // Jika tidak ada ID yang dikirim, redirect ke kelola jadwal
    header('Location: kelola_jadwal.php');
}
?>
