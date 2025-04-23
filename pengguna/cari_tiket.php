<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pengguna') {
    header('Location: login.php');
    exit();
}

include '../koneksi.php';

$asal = $_GET['asal'] ?? '';
$tujuan = $_GET['tujuan'] ?? '';
$tanggal = $_GET['tanggal'] ?? '';

$hasil = [];

$where = [];
$params = [];
$types = '';

if (!empty($asal)) {
    $where[] = "jadwal.asal LIKE ?";
    $params[] = "%$asal%";
    $types .= 's';
}
if (!empty($tujuan)) {
    $where[] = "jadwal.tujuan LIKE ?";
    $params[] = "%$tujuan%";
    $types .= 's';
}
if (!empty($tanggal)) {
    $where[] = "DATE(jadwal.waktu_berangkat) = ?";
    $params[] = $tanggal;
    $types .= 's';
}

$query = "
SELECT 
    jadwal.id AS jadwal_id,
    jadwal.asal,
    jadwal.tujuan,
    jadwal.waktu_berangkat,
    jadwal.waktu_tiba,
    kereta.nama_kereta,
    jadwal.harga
FROM jadwal
JOIN kereta ON jadwal.id_kereta = kereta.id
";

if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}

$stmt = $conn->prepare($query);
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $hasil[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cari Tiket</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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

        .search-section {
            background-color: #2c3e50;
            padding: 40px;
            color: white;
            border-radius: 0 0 20px 20px;
            margin-bottom: 30px;
        }

        .search-section h2 {
            margin-bottom: 20px;
        }

        .btn-pesan,
        .btn-light.btn-block {
            background-color: #f39c12;
            color: white;
            border: none;
        }

        .btn-pesan:hover,
        .btn-light.btn-block:hover {
            background-color: #d78e0c;
            color: white;
        }

        .card-tiket {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .card-tiket:hover {
            transform: translateY(-10px);
        }

        .card-tiket h5 {
            font-size: 1.2rem;
            color: #2c3e50;
        }

        .card-tiket p {
            color: #666;
            font-size: 0.9rem;
        }

        .card-tiket .text-right h4 {
            color: #f39c12;
        }

        .alert-warning {
            background-color: #f39c12;
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
    <h2>Menu</h2>
    <a href="cari_tiket.php">Cari Tiket</a>
    <a href="tiket_saya.php">Tiket Saya</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">
    <div class="container-fluid search-section">
        <div class="container">
            <h2>Cari Tiket Kereta</h2>
            <form method="GET" action="">
                <div class="form-row">
                    <div class="col-md-3 mb-2">
                        <input type="text" name="asal" class="form-control" placeholder="Kota Asal" value="<?= htmlspecialchars($asal) ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <input type="text" name="tujuan" class="form-control" placeholder="Kota Tujuan" value="<?= htmlspecialchars($tujuan) ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($tanggal) ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <button type="submit" class="btn btn-light btn-block">Cari Tiket</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="container">
        <h4 class="mb-4">
            <?= ($asal || $tujuan || $tanggal) ? 'Hasil Pencarian Tiket:' : 'Semua Jadwal Tersedia:' ?>
        </h4>
        <?php if (count($hasil) > 0): ?>
            <?php foreach ($hasil as $row): ?>
                <div class="card-tiket">
                    <div class="row">
                        <div class="col-md-8">
                            <h5><?= htmlspecialchars($row['nama_kereta']) ?></h5>
                            <p>
                                <?= htmlspecialchars($row['asal']) ?> → <?= htmlspecialchars($row['tujuan']) ?><br>
                                Tanggal: <?= date('d-m-Y', strtotime($row['waktu_berangkat'])) ?><br>
                                Waktu: <?= date('H:i', strtotime($row['waktu_berangkat'])) ?> - <?= date('H:i', strtotime($row['waktu_tiba'])) ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-right">
                            <h4>Rp <?= number_format($row['harga'], 3, ',', '.') ?></h4>
                            <a href="pesan_tiket.php?id=<?= $row['jadwal_id'] ?>" class="btn btn-pesan">Pesan Sekarang</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-warning">Tidak ada tiket ditemukan.</div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
