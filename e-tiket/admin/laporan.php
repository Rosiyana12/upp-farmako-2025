<?php
session_start();
include '../koneksi.php';

// Validasi role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Ambil keyword pencarian dari form
$searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Tentukan berapa banyak data yang ingin ditampilkan per halaman
$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $perPage;

// Query untuk mengambil data pemesanan dengan batasan dan pencarian
$query = "
SELECT 
    pemesanan.id AS pemesanan_id,
    pemesanan.id_user,
    pemesanan.id_jadwal,
    pemesanan.jumlah_tiket,
    pemesanan.total_bayar,
    pemesanan.waktu_pemesanan,
    penumpang.nama_penumpang,
    jadwal.waktu_berangkat,
    jadwal.waktu_tiba,
    kereta.nama_kereta
FROM pemesanan
JOIN penumpang ON pemesanan.id_user = penumpang.id
JOIN jadwal ON pemesanan.id_jadwal = jadwal.id
JOIN kereta ON jadwal.id_kereta = kereta.id
WHERE penumpang.nama_penumpang LIKE ? OR kereta.nama_kereta LIKE ?
LIMIT ?, ?
";

// Gunakan prepared statement untuk menghindari SQL injection
$searchTermWildCard = "%$searchTerm%";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'ssii', $searchTermWildCard, $searchTermWildCard, $start, $perPage);
mysqli_stmt_execute($stmt);
$data_pemesanan = mysqli_stmt_get_result($stmt);

// Query untuk menghitung total jumlah data dengan pencarian
$countQuery = "SELECT COUNT(*) AS total FROM pemesanan
               JOIN penumpang ON pemesanan.id_user = penumpang.id
               JOIN jadwal ON pemesanan.id_jadwal = jadwal.id
               JOIN kereta ON jadwal.id_kereta = kereta.id
               WHERE penumpang.nama_penumpang LIKE ? OR kereta.nama_kereta LIKE ?";
$countStmt = mysqli_prepare($conn, $countQuery);
mysqli_stmt_bind_param($countStmt, 'ss', $searchTermWildCard, $searchTermWildCard);
mysqli_stmt_execute($countStmt);
$countResult = mysqli_stmt_get_result($countStmt);
$totalData = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalData / $perPage);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemesanan - Sistem Tiket Kereta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: #f4f6f9;
        }

        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: #fff;
            position: fixed;
            top: 0;
            bottom: 0;
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

        .sidebar a:hover {
            background: #34495e;
        }

        .main {
            margin-left: 250px;
            padding: 40px 30px;
        }

        .main h1 {
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .search-bar {
            margin-bottom: 20px;
        }

        .search-bar input {
            padding: 10px;
            width: 200px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
        }

        table th, table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: #f39c12;
            color: #fff;
        }

        .btn-print {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #27ae60;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-print:hover {
            background: #219150;
        }

        .pagination {
            margin-top: 20px;
        }

        .pagination a {
            display: inline-block;
            margin: 0 5px;
            padding: 8px 15px;
            background: #ecf0f1;
            color: #34495e;
            border-radius: 5px;
            text-decoration: none;
        }

        .pagination a:hover {
            background: #f39c12;
            color: white;
        }

        .pagination a.active {
            background: #f39c12;
            color: white;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Sistem Tiket</h2>
    <a href="#">Dashboard</a>
    <a href="Kelola_jadwal.php">Kelola Jadwal</a>
    <a href="Kelola_kereta.php">Kelola Kereta</a>
    <a href="kelola_penumpang.php">Kelola Penumpang</a>
    <a href="laporan.php">Laporan Pemesanan</a>
    <a href=" ../logout.php">Logout</a>
</div>

<div class="main">
    <h1>Laporan Pemesanan Tiket</h1>
    <p>Berikut adalah laporan pemesanan tiket kereta.</p>

    <!-- Form Pencarian -->
    <div class="search-bar">
        <form action="" method="get">
            <input type="text" name="search" placeholder="Cari nama penumpang atau kereta" value="<?= htmlspecialchars($searchTerm) ?>">
            <input type="submit" value="Cari">
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Penumpang</th>
                <th>Nama Kereta</th>
                <th>Waktu Keberangkatan</th>
                <th>Waktu Tiba</th>
                <th>Jumlah Tiket</th>
                <th>Total Bayar</th>
                <th>Tanggal Pemesanan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($data_pemesanan)) { ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama_penumpang']) ?></td>
                <td><?= htmlspecialchars($row['nama_kereta']) ?></td>
                <td><?= $row['waktu_berangkat'] ?></td>
                <td><?= $row['waktu_tiba'] ?></td>
                <td><?= $row['jumlah_tiket'] ?></td>
                <td><?= number_format($row['total_bayar'], 3, ',', '.') ?></td>
                <td><?= $row['waktu_pemesanan'] ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="javascript:window.print()" class="btn-print">Cetak Laporan</a>

    <div class="pagination">
        <?php if ($page > 1) { ?>
            <a href="?page=<?= $page - 1 ?>&search=<?= htmlspecialchars($searchTerm) ?>">&laquo; Sebelumnya</a>
        <?php } ?>
        <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
            <a href="?page=<?= $i ?>&search=<?= htmlspecialchars($searchTerm) ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
        <?php } ?>
        <?php if ($page < $totalPages) { ?>
            <a href="?page=<?= $page + 1 ?>&search=<?= htmlspecialchars($searchTerm) ?>">Berikutnya &raquo;</a>
        <?php } ?>
    </div>
</div>

</body>
</html>
