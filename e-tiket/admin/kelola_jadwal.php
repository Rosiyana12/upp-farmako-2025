<?php
session_start();

// Validasi role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../koneksi.php';

// Ambil data jadwal dan nama kereta dari database dengan join
$query = "
    SELECT jadwal.*, kereta.nama_kereta
    FROM jadwal
    JOIN kereta ON jadwal.id_kereta = kereta.id
    ORDER BY jadwal.waktu_berangkat ASC
";

$result = mysqli_query($conn, $query);

// Cek apakah query berhasil
if (!$result) {
    die("Query gagal dijalankan: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Jadwal - Sistem Tiket Kereta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS langsung -->
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #f4f6f9; }
        .sidebar {
            width: 250px; background: #2c3e50; color: #fff; position: fixed; top: 0; bottom: 0; padding: 30px 20px;
        }
        .sidebar h2 { margin-bottom: 30px; font-size: 22px; text-align: center; color: #f39c12; }
        .sidebar a {
            display: block; color: #ecf0f1; padding: 12px 15px; margin: 10px 0; text-decoration: none;
            border-radius: 8px; transition: 0.3s;
        }
        .sidebar a:hover { background: #34495e; }

        .main { margin-left: 250px; padding: 40px 30px; }
        h1 { color: #2c3e50; margin-bottom: 20px; }

        table {
            width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        th, td {
            padding: 12px 15px; text-align: left; border-bottom: 1px solid #ecf0f1;
        }

        th { background: #f39c12; color: white; }

        tr:hover { background-color: #f1f1f1; }

        .add-button {
            margin-bottom: 20px; padding: 10px 15px; background: #27ae60; color: white;
            text-decoration: none; border-radius: 8px; display: inline-block;
        }

        .action-button {
            margin-right: 5px;
            padding: 5px 10px;
            background: #3498db;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }

        .action-button.delete {
            background: #e74c3c;
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
    <a href="Kelola_jadwal.php">Kelola Jadwal</a>
    <a href="Kelola_kereta.php">Kelola kereta</a>
    <a href="kelola_penumpang.php">Kelola Penumpang</a>
    <a href="laporan.php">Laporan Pemesanan</a>
    <a href=" ../logout.php">logout</a>
</div>
</div>

<div class="main">
    <h1>Kelola Jadwal</h1>

    <a href="tambah_jadwal.php" class="add-button">+ Tambah Jadwal</a>

    <table>
    <thead>
    <tr>
        <th>No</th>
        <th>Nama Kereta</th>
        <th>Asal</th>
        <th>Tujuan</th>
        <th>Berangkat</th>
        <th>Tiba</th>
        <th>Harga</th>
        <th>Aksi</th>
    </tr>
</thead>
<tbody>
    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) : ?>
    <tr>
        <td><?= $no++; ?></td>
        <td><?= $row['nama_kereta']; ?></td>
        <td><?= $row['asal']; ?></td>
        <td><?= $row['tujuan']; ?></td>
        <td><?= $row['waktu_berangkat']; ?></td>
        <td><?= $row['waktu_tiba']; ?></td>
        <td>Rp <?= number_format($row['harga'], 4, ',', '.'); ?></td>

        <td>
            <a class="action-button" href="edit_jadwal.php?id=<?= $row['id']; ?>">Edit</a>
            <a class="action-button delete" href="hapus_jadwal.php?id=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
        </td>
    </tr>
    <?php endwhile; ?>
</tbody>


    </table>
</div>

</body>
</html>
