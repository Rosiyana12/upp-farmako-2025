<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pengguna') {
    header('Location: ../login.php');
    exit();
}

include '../koneksi.php';

$id_pemesanan = $_GET['id_pemesanan'] ?? '';
if (!$id_pemesanan) {
    echo "ID Pemesanan tidak ditemukan.";
    exit();
}

$stmt = $conn->prepare("SELECT pemesanan.*, kereta.nama_kereta, kereta.jenis, jadwal.asal, jadwal.tujuan, jadwal.waktu_berangkat 
                        FROM pemesanan 
                        JOIN jadwal ON pemesanan.id_jadwal = jadwal.id 
                        JOIN kereta ON jadwal.id_kereta = kereta.id 
                        WHERE pemesanan.id = ?");
$stmt->bind_param("i", $id_pemesanan);
$stmt->execute();
$result = $stmt->get_result();
$pemesanan = $result->fetch_assoc();

if (!$pemesanan) {
    echo "Data pemesanan tidak ditemukan.";
    exit();
}

$status_bayar = $pemesanan['status_bayar'];
$success_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metodebayar = $_POST['metodebayar'] ?? '';
    $bukti = $_FILES['bukti']['name'] ?? '';
    $bukti_tmp = $_FILES['bukti']['tmp_name'];

    if (!$metodebayar || !$bukti) {
        echo "Harap lengkapi data pembayaran.";
        exit();
    }

    $target_dir = "../assets/";
    $filename = time() . '_' . basename($bukti);
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($bukti_tmp, $target_file)) {
        $stmt = $conn->prepare("INSERT INTO pembayaran (id_pemesanan, status, bukti, metode_bayar, waktu_bayar) 
                                VALUES (?, 'lunas', ?, ?, NOW())");
        $stmt->bind_param("iss", $id_pemesanan, $filename, $metodebayar);
        if ($stmt->execute()) {
            $stmt_update = $conn->prepare("UPDATE pemesanan SET status_bayar = 'lunas' WHERE id = ?");
            $stmt_update->bind_param("i", $id_pemesanan);
            if ($stmt_update->execute()) {
                // Redirect setelah sukses
                header("Location: tiket_saya.php?pesan=sukses");
                exit();
            } else {
                echo "Gagal memperbarui status pemesanan.";
            }
        } else {
            echo "Gagal menyimpan bukti pembayaran ke database.";
        }
    } else {
        echo "Gagal mengupload bukti pembayaran.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran Tiket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3 class="mb-4">💳 Pembayaran Tiket</h3>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?= $success_message ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Nama Kereta:</strong> <?= htmlspecialchars($pemesanan['nama_kereta']) ?></p>
            <p><strong>Rute:</strong> <?= htmlspecialchars($pemesanan['asal']) ?> → <?= htmlspecialchars($pemesanan['tujuan']) ?></p>
            <p><strong>Waktu Berangkat:</strong> <?= htmlspecialchars($pemesanan['waktu_berangkat']) ?></p>
            <p><strong>Jumlah Tiket:</strong> <?= htmlspecialchars($pemesanan['jumlah_tiket']) ?></p>
            <p><strong>Total Harga:</strong> Rp <?= number_format($pemesanan['total_bayar'], 4, ',', '.') ?></p>
        </div>
    </div>

    <h5 class="mt-4">Transfer ke:</h5>
    <ul>
        <li>Bank BCA - 1234567890 a.n PT Kereta Hebat</li>
        <li>OVO / DANA - 085716438414</li>
    </ul>
    <p class="text-danger">* Setelah transfer, kirim bukti pembayaran dan tiket akan langsung aktif.</p>
    <hr>

    <?php if ($status_bayar === 'lunas'): ?>
        <a href="cetak_bukti.php?id=<?= $id_pemesanan ?>" class="btn btn-success btn-lg">🎫 Ambil E-Tiket</a>
    <?php else: ?>
        <h4>Konfirmasi Pembayaran</h4>
        <form method="POST" enctype="multipart/form-data" class="card p-4">
            <div class="mb-3">
                <label for="metodebayar" class="form-label">Metode Pembayaran:</label>
                <select class="form-select" name="metodebayar" required>
                    <option value="">Pilih...</option>
                    <option value="Bank BCA">Bank BCA</option>
                    <option value="OVO">OVO</option>
                    <option value="DANA">DANA</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="bukti" class="form-label">Upload Bukti Pembayaran:</label>
                <input type="file" class="form-control" name="bukti" required>
            </div>
            <button type="submit" class="btn btn-primary">Kirim Bukti</button>
        </form>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
