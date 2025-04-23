<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pengguna') {
    header('Location: ../login.php');
    exit();
}

include '../koneksi.php';

$id_jadwal = $_GET['id'] ?? '';
if (!$id_jadwal) {
    echo "ID Jadwal tidak ditemukan.";
    exit();
}

// Ambil data jadwal dan kereta
$stmt = $conn->prepare("SELECT jadwal.*, kereta.nama_kereta, kereta.jenis, kereta.kapasitas FROM jadwal JOIN kereta ON jadwal.id_kereta = kereta.id WHERE jadwal.id = ?");
$stmt->bind_param("i", $id_jadwal);
$stmt->execute();
$result = $stmt->get_result();
$jadwal = $result->fetch_assoc();
if (!$jadwal) {
    echo "Data jadwal tidak ditemukan.";
    exit();
}

$kapasitas = $jadwal['kapasitas'];
$harga = $jadwal['harga'];

$kursi_terisi = [];
$sql = "SELECT no_kursi FROM penumpang WHERE id_pemesanan IN (SELECT id FROM pemesanan WHERE id_jadwal = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_jadwal);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $kursi_terisi[] = $row['no_kursi'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jumlah_tiket = $_POST['jumlah_tiket'] ?? 0;
    $penumpang = $_POST['nama_penumpang'] ?? [];
    $kursi = $_POST['no_kursi'] ?? [];

    if ($jumlah_tiket <= 0 || count($penumpang) != $jumlah_tiket || count($kursi) != $jumlah_tiket) {
        echo "<script>alert('Data pemesanan tidak lengkap atau tidak sesuai.'); window.history.back();</script>";
        exit();
    }

    foreach ($kursi as $k) {
        if (in_array($k, $kursi_terisi)) {
            echo "<script>alert('Kursi nomor $k sudah terisi!'); window.history.back();</script>";
            exit();
        }
    }

    $total = $jumlah_tiket * $harga;
    $id_user = $_SESSION['id_user'];
    $waktu = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO pemesanan (id_user, id_jadwal, jumlah_tiket, total_bayar, status_bayar, waktu_pemesanan) VALUES (?, ?, ?, ?, 'Belum Bayar', ?)");
    $stmt->bind_param("iiids", $id_user, $id_jadwal, $jumlah_tiket, $total, $waktu);
    if ($stmt->execute()) {
        $id_pemesanan = $stmt->insert_id;

        for ($i = 0; $i < $jumlah_tiket; $i++) {
            $stmt = $conn->prepare("INSERT INTO penumpang (id_pemesanan, nama_penumpang, no_kursi) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $id_pemesanan, $penumpang[$i], $kursi[$i]);
            $stmt->execute();
        }

        header('Location: pembayaran.php?id_pemesanan=' . $id_pemesanan);
        exit();
    } else {
        echo "Gagal menyimpan pemesanan.";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesan Tiket</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .kursi-wrapper { display: flex; flex-wrap: wrap; max-width: 300px; margin: auto; }
        .kursi {
            width: 40px; height: 40px; margin: 5px;
            text-align: center; line-height: 40px;
            border-radius: 5px; cursor: pointer;
            font-weight: bold;
        }
        .kosong { background-color: #28a745; color: white; }
        .terisi { background-color: #dc3545; color: white; cursor: not-allowed; }
        .dipilih { background-color: #ffc107; color: black; }
    </style>
</head>
<body>
<div class="container mt-5 mb-5">
    <div class="card p-4 shadow-sm">
        <h3 class="mb-4">Pesan Tiket Kereta</h3>
        <p><strong>Kereta:</strong> <?= htmlspecialchars($jadwal['nama_kereta']) ?></p>
        <p><strong>Rute:</strong> <?= htmlspecialchars($jadwal['asal']) ?> → <?= htmlspecialchars($jadwal['tujuan']) ?></p>
        <p><strong>Waktu:</strong> <?= $jadwal['waktu_berangkat'] ?> → <?= $jadwal['waktu_tiba'] ?></p>
        <p><strong>Harga:</strong> Rp <?= number_format($harga, 4, ',', '.') ?> / orang</p>

        <form method="POST" onsubmit="return validateForm()">
            <input type="hidden" name="id_jadwal" value="<?= $jadwal['id'] ?>">
            <div class="form-group">
                <label>Jumlah Tiket</label>
                <input type="number" id="jumlah_tiket" name="jumlah_tiket" class="form-control" min="1" max="10" required>
            </div>
            <div class="form-group" id="nama-penumpang-area">
                <label>Nama Penumpang</label>
            </div>
            <div class="form-group">
                <label>Pilih Kursi</label>
                <div class="kursi-wrapper" id="kursi-wrapper">
                    <?php
                    for ($i = 1; $i <= $kapasitas; $i++) {
                        $status = in_array($i, $kursi_terisi) ? 'terisi' : 'kosong';
                        echo "<div class='kursi $status' data-nomor='$i'>$i</div>";
                    }
                    ?>
                </div>
                <div id="input-kursi"></div>
            </div>
            <button type="submit" class="btn btn-warning">Pesan Sekarang</button>
            <a href="cari_tiket.php" class="btn btn-secondary ml-2">Kembali</a>
        </form>
    </div>
</div>

<script>
    const jumlahInput = document.getElementById('jumlah_tiket');
    const penumpangArea = document.getElementById('nama-penumpang-area');
    const kursiWrapper = document.getElementById('kursi-wrapper');
    const inputKursi = document.getElementById('input-kursi');
    let kursiDipilih = [];

    jumlahInput.addEventListener('change', function () {
        let jumlah = parseInt(this.value);
        penumpangArea.innerHTML = '<label>Nama Penumpang</label>';
        for (let i = 0; i < jumlah; i++) {
            let input = document.createElement('input');
            input.type = 'text';
            input.name = 'nama_penumpang[]';
            input.className = 'form-control mb-2';
            input.placeholder = 'Penumpang ' + (i + 1);
            input.required = true;
            penumpangArea.appendChild(input);
        }

        kursiDipilih = [];
        inputKursi.innerHTML = '';
        document.querySelectorAll('.kursi').forEach(k => k.classList.remove('dipilih'));
    });

    kursiWrapper.addEventListener('click', function (e) {
        if (e.target.classList.contains('kursi') && e.target.classList.contains('kosong')) {
            let no = e.target.getAttribute('data-nomor');

            if (kursiDipilih.includes(no)) {
                kursiDipilih = kursiDipilih.filter(n => n !== no);
                e.target.classList.remove('dipilih');
            } else {
                if (kursiDipilih.length >= parseInt(jumlahInput.value)) {
                    alert("Jumlah kursi yang dipilih melebihi jumlah tiket!");
                    return;
                }
                kursiDipilih.push(no);
                e.target.classList.add('dipilih');
            }

            // Update input tersembunyi
            inputKursi.innerHTML = '';
            kursiDipilih.forEach(k => {
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'no_kursi[]';
                input.value = k;
                inputKursi.appendChild(input);
            });
        }
    });

    function validateForm() {
        if (kursiDipilih.length !== parseInt(jumlahInput.value)) {
            alert("Jumlah kursi yang dipilih harus sama dengan jumlah tiket.");
            return false;
        }
        return true;
    }
</script>
</body>
</html>
