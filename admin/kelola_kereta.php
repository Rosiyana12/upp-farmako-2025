<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$keyword = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';

if ($keyword) {
    $query = "SELECT * FROM kereta 
              WHERE nama_kereta LIKE '%$keyword%' 
              OR jenis LIKE '%$keyword%'
              ORDER BY id DESC";
} else {
    $query = "SELECT * FROM kereta ORDER BY id DESC";
}

$result = mysqli_query($conn, $query);

if (!$result) {
    die('Query failed: ' . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Manajemen Kereta | E-Tiket</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }

    body { background: #f4f6f9; }

    .sidebar {
      width: 250px;
      background: #2c3e50;
      color: #fff;
      position: fixed;
      top: 0; bottom: 0;
      padding: 30px 20px;
    }

    .sidebar h2 {
      margin-bottom: 30px;
      font-size: 22px;
      text-align: center;
      color: #f39c12;
    }

    .sidebar a {
      display: block;
      color: #ecf0f1;
      padding: 12px 15px;
      margin: 10px 0;
      text-decoration: none;
      border-radius: 8px;
      transition: 0.3s;
    }

    .sidebar a:hover { background: #34495e; }

    .main {
      margin-left: 250px;
      padding: 40px 30px;
    }

    .main h2 {
      margin-bottom: 20px;
      color: #2c3e50;
    }

    .navbar {
      background-color: #fff;
      border-radius: 10px;
      padding: 10px 20px;
      margin-bottom: 30px;
    }

    .table thead th {
      background-color: #2c3e50;
      color: white;
      text-align: center;
    }

    .table td, .table th {
      vertical-align: middle;
      text-align: center;
    }

    .btn i {
      margin-right: 4px;
    }

    @media (max-width: 768px) {
      .sidebar { width: 100%; height: auto; position: relative; }
      .main { margin-left: 0; }
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h2>Sistem Tiket</h2>
  <a href="#">Dashboard</a>
  <a href="kelola_jadwal.php">Kelola Jadwal</a>
  <a href="kelola_kereta.php">Kelola Kereta</a>
  <a href="kelola_penumpang.php">Kelola Penumpang</a>
  <a href="laporan.php">Laporan Pemesanan</a>
  <a href=" ../logout.php">Logout</a>
</div>

<div class="main">
  <nav class="navbar shadow-sm mb-4">
    <form class="d-flex w-100" method="GET">
      <input class="form-control me-2" type="search" placeholder="Cari nama kereta atau jenis..." name="q" value="<?= htmlspecialchars($keyword) ?>">
      <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
    </form>
  </nav>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Manajemen Kereta</h2>
    <a href="tambah_kereta.php" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i>Tambah Kereta
    </a>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Kereta</th>
          <th>Jenis</th>
          <th>Kapasitas</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; ?>
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['nama_kereta']) ?></td>
          <td><?= htmlspecialchars($row['jenis']) ?></td>
          <td><?= htmlspecialchars($row['kapasitas']) ?></td>
          <td>
            <a href="edit_kereta.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning me-1">
              <i class="bi bi-pencil-square"></i> Edit
            </a>
            <a href="hapus_kereta.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus kereta ini?')" class="btn btn-sm btn-danger">
              <i class="bi bi-trash-fill"></i> Hapus
            </a>
          </td>
        </tr>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($result) === 0): ?>
        <tr>
          <td colspan="5" class="text-center">Tidak ada data ditemukan.</td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php mysqli_close($conn); ?>
