<?php
session_start();
include '../koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$query = "DELETE FROM kereta WHERE id = $id";
if (mysqli_query($conn, $query)) {
    header('Location: Kelola_kereta.php');
} else {
    echo "Gagal menghapus data: " . mysqli_error($conn);
}
?>
