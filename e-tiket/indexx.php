<?php
session_start();

    header('Location: indexx.php');
    exit();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Beranda - Sistem E-Ticket</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary: #0d47a1;
            --accent: #ff6f00;
            --bg: #f4f6f9;
            --text-dark: #222;
            --text-light: #666;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background-color: var(--bg);
            line-height: 1.6;
        }

        .navbar {
            background-color: var(--primary);
            padding: 20px 40px;
            color: var(--white);
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .navbar .brand span {
            color: var(--accent);
        }

        .hero {
            background: linear-gradient(rgba(13, 71, 161, 0.7), rgba(13, 71, 161, 0.7)), url('https://kai.id/images/slider/slider3.jpg') center/cover no-repeat;
            color: var(--white);
            text-align: center;
            padding: 140px 20px 100px;
            animation: fadeIn 1.5s ease-in-out;
        }

        .hero h1 {
            font-size: 52px;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 20px;
            max-width: 700px;
            margin: auto;
            opacity: 0.9;
        }

        .actions {
            margin-top: 40px;
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .actions a {
            text-decoration: none;
            background-color: var(--accent);
            color: var(--white);
            padding: 14px 30px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            transition: 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .actions a:hover {
            background-color: #e65100;
            transform: translateY(-3px);
        }

        .features {
            background: var(--white);
            padding: 70px 20px 60px;
            text-align: center;
            animation: fadeInUp 1.5s ease-in-out;
        }

        .features h2 {
            color: var(--primary);
            font-size: 34px;
            margin-bottom: 40px;
        }

        .feature-grid {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 40px;
            margin-top: 30px;
        }

        .feature-item {
            width: 260px;
            padding: 25px;
            background-color: #fefefe;
            border-radius: 15px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        }

        .feature-item img {
            width: 60px;
            margin-bottom: 15px;
        }

        .feature-item h4 {
            color: var(--primary);
            margin-bottom: 10px;
            font-size: 18px;
        }

        .feature-item p {
            color: var(--text-light);
            font-size: 14px;
        }

        .feature-item .layout-img {
            width: 100%;
            border-radius: 10px;
            margin-top: 15px;
        }

        .footer {
            background-color: #fff;
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #777;
            border-top: 1px solid #ddd;
            margin-top: 40px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 600px) {
            .hero h1 {
                font-size: 36px;
            }

            .feature-item {
                width: 90%;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="brand">Sistem <span>E-Ticket</span></div>
</div>

<div class="hero">
    <h1>Selamat Datang di Sistem E-Ticket</h1>
    <p>Pesan tiket kereta api dengan mudah, aman, dan nyaman seperti menggunakan aplikasi resmi KAI.</p>
    <div class="actions">
        <a href=" ../login.php">🔐 Login</a>
        <a href="pengguna/cari_tiket.php">🎫 Cari Tiket</a>
    </div>
</div>

<div class="features">
    <h2>Kenapa Pilih Kami?</h2>
    <div class="feature-grid">
        <div class="feature-item">
            <img src="https://img.icons8.com/ios-filled/100/train.png" alt="Jadwal Lengkap">
            <h4>Jadwal Lengkap</h4>
            <p>Akses semua jadwal kereta api terbaru dan terupdate langsung dari sistem.</p>
        </div>
        <div class="feature-item">
            <img src="https://img.icons8.com/ios-filled/100/seat.png" alt="Pilih Kursi">
            <h4>Pilih Kursi</h4>
            <p>Atur tempat duduk favorit Anda langsung saat pemesanan tiket dilakukan.</p>
            <div style="text-align: center; margin-top: 20px;">
                <img src="https://img.icons8.com/ios-filled/100/cinema-seat.png" alt="Layout Kursi" style="max-width: 80%; border-radius: 10px;">
            </div>
        </div>
        <div class="feature-item">
            <img src="https://img.icons8.com/ios-filled/100/secure.png" alt="Pembayaran Aman">
            <h4>Pembayaran Aman</h4>
            <p>Dukungan berbagai metode pembayaran yang aman dan terpercaya.</p>
        </div>
    </div>
</div>

<div class="footer">
    &copy; <?= date('Y') ?> Sistem E-Ticket. All rights reserved.
</div>

</body>
</html>
