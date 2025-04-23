<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['id_user'])) {
    echo "Silakan login terlebih dahulu.";
    exit;
}

if (!isset($_GET['id'])) {
    echo "ID pemesanan tidak ditemukan.";
    exit;
}

$id_user = $_SESSION['id_user'];
$id_pemesanan = (int)$_GET['id'];

$query = "
    SELECT 
        p.*, 
        j.asal AS berangkat_dari, 
        j.tujuan, 
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
    WHERE p.id_user = '$id_user' AND p.id = '$id_pemesanan'
    GROUP BY p.id
";

$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    echo "Data pemesanan tidak ditemukan.";
    exit;
}
$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pemesanan Tiket</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .ticket-container {
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
            width: 80%;
            margin: 0 auto;
        }
        .ticket-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .ticket-header h3 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .ticket-header p {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .ticket-details {
            border-top: 1px solid #ddd;
            padding-top: 15px;
            margin-top: 15px;
        }
        .ticket-details p {
            font-size: 16px;
            margin-bottom: 8px;
        }
        .ticket-footer {
            text-align: center;
            margin-top: 30px;
        }
        .btn-back {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-back:hover {
            background-color: #218838;
        }

        /* CSS for print media */
        @media print {
            body {
                font-family: 'Arial', sans-serif;
                background-color: #fff;
            }
            .ticket-container {
                box-shadow: none;
                width: 100%;
                margin: 0;
                padding: 20px;
                border-radius: 0;
                border: 1px solid #ddd;
            }
            .ticket-header h3 {
                font-size: 28px;
            }
            .ticket-details p {
                font-size: 14px;
            }
            .ticket-footer {
                display: none; /* Hide the back button in print */
            }
            .btn-back {
                display: none; /* Hide the back button in print */
            }
            .ticket-header p,
            .ticket-details p {
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>

<div class="ticket-container">
    <div class="ticket-header">
        <h3><?= htmlspecialchars($data['nama_kereta']) ?> (<?= htmlspecialchars($data['jenis']) ?>)</h3>
        <p><strong>ID Pemesanan:</strong> <?= htmlspecialchars($data['id']) ?></p>
        <p><strong>Rute:</strong> <?= htmlspecialchars($data['berangkat_dari']) ?> → <?= htmlspecialchars($data['tujuan']) ?></p>
    </div>

    <div class="ticket-details">
        <p><strong>Keberangkatan:</strong> <?= date('d-m-Y H:i', strtotime($data['waktu_berangkat'])) ?></p>
        <p><strong>Tiba:</strong> <?= date('d-m-Y H:i', strtotime($data['waktu_tiba'])) ?></p>
        <p><strong>Total Bayar:</strong> Rp <?= number_format($data['total_bayar'], 3, ',', '.') ?></p>
        <p><strong>Metode Pembayaran:</strong> <?= htmlspecialchars($data['metode_pembayaran']) ?></p>
        <p><strong>Waktu Pemesanan:</strong> <?= date('d-m-Y H:i', strtotime($data['waktu_pemesanan'])) ?></p>
    </div>

    <div class="ticket-details">
        <h5>Daftar Penumpang:</h5>
        <ul>
            <?php
            $penumpangNames = explode(',', $data['nama_penumpang']);
            $penumpangSeats = explode(',', $data['no_kursi']);
            foreach ($penumpangNames as $key => $penumpang) {
                echo '<li>' . htmlspecialchars($penumpang) . ' - Kursi: ' . htmlspecialchars($penumpangSeats[$key]) . '</li>';
            }
            ?>
        </ul>
    </div>

    <div class="ticket-footer">
        <a href="tiket_saya.php" class="btn-back">Kembali</a>
        <button class="btn btn-primary" onclick="window.print();">Cetak Tiket</button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
