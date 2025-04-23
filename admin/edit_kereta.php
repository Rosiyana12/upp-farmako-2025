<?php
session_start();
include '../koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$query = "SELECT * FROM kereta WHERE id = $id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    die("Data kereta tidak ditemukan.");
}

$kereta = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis']);
    $kapasitas = (int) $_POST['kapasitas'];

    $update = "UPDATE kereta SET nama_kereta='$nama', jenis='$jenis', kapasitas=$kapasitas WHERE id = $id";
    if (mysqli_query($conn, $update)) {
        header('Location: Kelola_kereta.php');
    } else {
        echo "Gagal memperbarui data: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Kereta</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
  <h2 class="mb-4">Edit Data Kereta</h2>
  <form method="POST">
    <div class="mb-3">
      <label for="nama" class="form-label">Nama Kereta</label>
      <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($kereta['nama_kereta']) ?>" required>
    </div>
    <div class="mb-3">
      <label for="jenis" class="form-label">Jenis Kereta</label>
      <input type="text" class="form-control" id="jenis" name="jenis" value="<?= htmlspecialchars($kereta['jenis']) ?>" required>
    </div>
    <div class="mb-3">
      <label for="kapasitas" class="form-label">Kapasitas</label>
      <input type="number" class="form-control" id="kapasitas" name="kapasitas" value="<?= $kereta['kapasitas'] ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="Kelola_kereta.php" class="btn btn-secondary">Batal</a>
  </form>
</body>
</html>
