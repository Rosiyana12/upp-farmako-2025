<?php
session_start();
include '../koneksi.php';

// Cek apakah pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Verifikasi pembayaran
if (isset($_GET['verifikasi']) && isset($_GET['id'])) {
    $pemesanan_id = intval($_GET['id']);
    
    // Update status pembayaran menjadi lunas
    if ($pemesanan_id > 0) {
        $query = "UPDATE pemesanan SET status_bayar = 'lunas' WHERE id = $pemesanan_id";
        $result = mysqli_query($conn, $query);
        
        if ($result) {
            $_SESSION['message'] = "Pembayaran telah diverifikasi sebagai lunas.";
        } else {
            $_SESSION['message'] = "Gagal memverifikasi pembayaran: " . mysqli_error($conn);
        }
    }
    header('Location: kelola_penumpang.php');
    exit();
}

// Pagination setup
$items_per_page = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $items_per_page;

// Query untuk mengambil data penumpang
$query = "
SELECT 
    penumpang.id AS penumpang_id,
    penumpang.nama_penumpang,
    penumpang.no_kursi,
    pemesanan.id AS pemesanan_id,
    pemesanan.status_bayar,
    kereta.nama_kereta,
    jadwal.waktu_berangkat,
    jadwal.waktu_tiba,
    pembayaran.bukti,
    pembayaran.metode_bayar
FROM penumpang
JOIN pemesanan ON penumpang.id_pemesanan = pemesanan.id
JOIN jadwal ON pemesanan.id_jadwal = jadwal.id
JOIN kereta ON jadwal.id_kereta = kereta.id
LEFT JOIN pembayaran ON pemesanan.id = pembayaran.id_pemesanan
ORDER BY pemesanan.id DESC
LIMIT $offset, $items_per_page
";
$result = mysqli_query($conn, $query) or die('Query gagal: ' . mysqli_error($conn));
$penumpang_data = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Query untuk menghitung total data
$count_query = "
SELECT COUNT(*) AS total
FROM penumpang
JOIN pemesanan ON penumpang.id_pemesanan = pemesanan.id
JOIN jadwal ON pemesanan.id_jadwal = jadwal.id
JOIN kereta ON jadwal.id_kereta = kereta.id
LEFT JOIN pembayaran ON pemesanan.id = pembayaran.id_pemesanan
";
$count_result = mysqli_query($conn, $count_query) or die('Query gagal: ' . mysqli_error($conn));
$total_items = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_items / $items_per_page);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Penumpang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: #fff;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            padding: 30px;
        }
        .sidebar a {
            display: block;
            color: #ecf0f1;
            padding: 10px;
            text-decoration: none;
            margin-bottom: 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .sidebar a:hover { background-color: #34495e; }
        .main {
            margin-left: 260px;
            padding: 20px;
        }
        .table th, .table td { vertical-align: middle; }
        .bukti-img { max-width: 200px; max-height: 100px; cursor: pointer; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Sistem Tiket</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="kelola_kereta.php">Kelola Kereta</a>
    <a href="kelola_jadwal.php">Kelola Jadwal</a>
    <a href="kelola_penumpang.php">Kelola Penumpang</a>
    <a href="laporan.php">Laporan Pemesanan</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">
    <h1>Kelola Penumpang</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?= $_SESSION['message'] ?>
            <?php unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>No</th>
                <th>Nama Penumpang</th>
                <th>No Kursi</th>
                <th>Kereta</th>
                <th>Waktu Berangkat</th>
                <th>Waktu Tiba</th>
                <th>Status Bayar</th>
                <th>Bukti</th>
                <th>Metode Bayar</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = $offset + 1; foreach ($penumpang_data as $penumpang): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($penumpang['nama_penumpang']) ?></td>
                <td><?= htmlspecialchars($penumpang['no_kursi']) ?></td>
                <td><?= htmlspecialchars($penumpang['nama_kereta']) ?></td>
                <td><?= $penumpang['waktu_berangkat'] ?></td>
                <td><?= $penumpang['waktu_tiba'] ?></td>
                <td>
                    <?php if ($penumpang['status_bayar'] === 'lunas'): ?>
                        <span class="badge badge-success">Lunas</span>
                    <?php elseif ($penumpang['status_bayar'] === 'proses'): ?>
                        <span class="badge badge-warning">Proses</span>
                    <?php else: ?>
                        <span class="badge badge-secondary">lunas</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($penumpang['bukti'])): ?>
                        <!-- Tampilkan tombol untuk melihat bukti pembayaran -->
                        <a href="../assets/img/<?= htmlspecialchars($penumpang['bukti']) ?>" target="_blank" class="btn btn-info btn-sm">Lihat Bukti</a>
                    <?php else: ?>
                        <span class="text-muted">Tidak ada</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($penumpang['metode_bayar']) ?></td>
                <td>
                    <?php if ($penumpang['status_bayar'] === 'belum bayar'): ?>
                        <a href="?verifikasi=true&id=<?= $penumpang['pemesanan_id'] ?>" class="btn btn-success btn-sm">Verifikasi Bayar</a>
                    <?php elseif ($penumpang['status_bayar'] === 'proses'): ?>
                        <span class="badge badge-warning">Menunggu Verifikasi</span>
                    <?php else: ?>
                        <span class="badge badge-success">Lunas</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=1">First</a>
            </li>
            <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
            </li>
            <li class="page-item <?= $page == $total_pages ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
            </li>
            <li class="page-item <?= $page == $total_pages ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $total_pages ?>">Last</a>
            </li>
        </ul>
    </nav>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
