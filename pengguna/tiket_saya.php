<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pengguna') {
    header('Location: ../login.php');
    exit();
}

include '../koneksi.php';

$id_user = $_SESSION['id_user'];

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $deleteQuery = "DELETE FROM pemesanan WHERE id = '$delete_id' AND id_user = '$id_user'";
    $deleteResult = mysqli_query($conn, $deleteQuery);
    if ($deleteResult) {
        header('Location: tiket_saya.php');
        exit;
    } else {
        echo "Gagal menghapus pemesanan: " . mysqli_error($conn);
    }
}

$query = "
    SELECT 
        p.*, 
        j.asal AS berangkat_dari, 
        j.tujuan, 
        DATE(j.waktu_berangkat) AS tanggal,
        TIME(j.waktu_berangkat) AS jam,
        j.waktu_berangkat, 
        j.waktu_tiba, 
        k.nama_kereta,
        k.jenis,
        (SELECT COUNT(*) FROM penumpang WHERE id_pemesanan = p.id) AS jumlah_penumpang,
        GROUP_CONCAT(pe.nama_penumpang ORDER BY pe.id SEPARATOR ', ') AS nama_penumpang,
        GROUP_CONCAT(pe.no_kursi ORDER BY pe.id SEPARATOR ', ') AS no_kursi
    FROM pemesanan p
    JOIN jadwal j ON p.id_jadwal = j.id
    JOIN kereta k ON j.id_kereta = k.id
    LEFT JOIN penumpang pe ON pe.id_pemesanan = p.id
    WHERE p.id_user = '$id_user' 
    GROUP BY p.id
    ORDER BY p.waktu_pemesanan DESC
";

$result = mysqli_query($conn, $query);
if (!$result) {
    echo "Query error: " . mysqli_error($conn);
    exit;
}

$rowsPerPage = 5;
$totalRows = mysqli_num_rows($result);
$totalPages = ceil($totalRows / $rowsPerPage);

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $rowsPerPage;

$queryWithLimit = $query . " LIMIT $offset, $rowsPerPage";
$resultWithLimit = mysqli_query($conn, $queryWithLimit);
if (!$resultWithLimit) {
    echo "Query error: " . mysqli_error($conn);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Saya - Sistem Tiket Kereta</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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

        .table td, .table th {
            vertical-align: middle;
        }

        .pagination {
            display: flex;
            justify-content: center;
            padding-top: 20px;
        }

        .pagination a {
            margin: 0 5px;
        }

        .alert-warning {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
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
    <a href="index.php">Dashboard</a>
    <a href="cari_tiket.php">Cari Tiket</a>
    <a href="tiket_saya.php">Tiket Saya</a>
    <a href="../logout.php">Logout</a>
</div>

<div class="main">
    <h1>Tiket Saya</h1>

    <?php if (mysqli_num_rows($resultWithLimit) > 0): ?>
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>No</th>
                    <th>Nama Penumpang</th>
                    <th>Nama Kereta</th>
                    <th>No Kursi</th>
                    <th>Rute</th>
                    <th>Tanggal & Waktu</th>
                    <th>Jumlah Penumpang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = $offset + 1; while ($row = mysqli_fetch_assoc($resultWithLimit)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_penumpang']) ?></td>
                        <td><?= htmlspecialchars($row['nama_kereta']) ?></td>
                        <td><?= htmlspecialchars($row['no_kursi']) ?></td>
                        <td><?= htmlspecialchars($row['berangkat_dari']) ?> → <?= htmlspecialchars($row['tujuan']) ?></td>
                        <td><?= $row['tanggal'] ?> / <?= $row['jam'] ?></td>
                        <td><?= $row['jumlah_penumpang'] ?></td>
                        <td>
                            <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">Detail</a>
                            <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pemesanan ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">Anda belum memesan tiket.</div>
    <?php endif; ?>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" class="btn btn-secondary btn-sm">Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="btn btn-light btn-sm <?= $page == $i ? 'font-weight-bold' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>" class="btn btn-secondary btn-sm">Next</a>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
