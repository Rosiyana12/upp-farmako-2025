<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Ambil daftar enum dari struktur kolom 'jenis'
$enum_result = mysqli_query($conn, "SHOW COLUMNS FROM kereta LIKE 'jenis'");
$row_enum = mysqli_fetch_assoc($enum_result);
$enum_str = $row_enum['Type'];

// Ambil nilai enum dari string seperti: enum('Ekonomi','Bisnis','Eksekutif')
preg_match("/^enum\('(.*)'\)$/", $enum_str, $matches);
$enum_values = explode("','", $matches[1]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis']);
    $kapasitas = (int) $_POST['kapasitas'];

    $query = "INSERT INTO kereta (nama_kereta, jenis, kapasitas) 
              VALUES ('$nama', '$jenis', $kapasitas)";
    if (mysqli_query($conn, $query)) {
        header('Location: Kelola_kereta.php');
        exit();
    } else {
        echo "Gagal menambahkan kereta: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Kereta</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
  <h2 class="mb-4">Tambah Data Kereta</h2>
  <form method="POST">
    <div class="mb-3">
      <label for="nama" class="form-label">Nama Kereta</label>
      <input type="text" class="form-control" id="nama" name="nama" required>
    </div>
    <div class="mb-3">
      <label for="jenis" class="form-label">Jenis Kereta</label>
      <select name="jenis" id="jenis" class="form-select" required>
        <option value="">-- Pilih Jenis --</option>
        <?php foreach ($enum_values as $j) : ?>
          <option value="<?= $j ?>"><?= htmlspecialchars($j) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label for="kapasitas" class="form-label">Kapasitas</label>
      <input type="number" class="form-control" id="kapasitas" name="kapasitas" required>
    </div>
    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="Kelola_kereta.php" class="btn btn-secondary">Kembali</a>
  </form>
</body>
</html>
