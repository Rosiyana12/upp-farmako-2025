<?php
session_start();

// Contoh validasi session (optional)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Sistem Tiket Kereta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS langsung -->
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

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .card {
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            margin-bottom: 10px;
            color: #34495e;
        }

        .card p {
            color: #7f8c8d;
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
    <a href="Kelola_jadwal.php">Kelola Jadwal</a>
    <a href="Kelola_kereta.php">Kelola kereta</a>
    <a href="kelola_penumpang.php">Kelola Penumpang</a>
    <a href="kelola_petugas.php">Kelola Petugas</a>
    <a href="laporan.php">Laporan Pemesanan</a>
    <a href=" ../logout.php">logout</a>
</div>

<div class="main">
    <h1>Selamat Datang, Admin!</h1>
    <p>Berikut beberapa menu yang dapat Anda kelola:</p>

    <div class="cards">
        <div class="card">
            <h3>Kelola Jadwal</h3>
            <p>Atur jadwal keberangkatan kereta.</p>
        </div>
        <div class="card">
            <h3>Kelola Tiket & Penumpang</h3>
            <p>Kelola data tiket dan penumpang.</p>
        </div>
        <div class="card">
            <h3>Laporan Pemesanan</h3>
            <p>Lihat rekap pemesanan tiket.</p>
        </div>
        <div class="card">
            <h3>logout</h3>
            <p>Konfirmasi dan logout setelah selesai.</p>
        </div>
    </div>
</div>

</body>
</html>
