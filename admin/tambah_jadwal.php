<?php
session_start();

// Validasi role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../koneksi.php';

// Ambil data kereta untuk dropdown
$kereta_query = "SELECT * FROM kereta";
$kereta_result = mysqli_query($conn, $kereta_query);

// Proses ketika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $id_kereta = $_POST['id_kereta']; // Pilih kereta berdasarkan ID
    $asal = $_POST['asal'];
    $tujuan = $_POST['tujuan'];
    $waktu_berangkat = $_POST['waktu_berangkat'];
    $waktu_tiba = $_POST['waktu_tiba'];
    $harga = $_POST['harga'];

    // Pastikan data tidak kosong
    if (empty($id_kereta) || empty($asal) || empty($tujuan) || empty($waktu_berangkat) || empty($waktu_tiba) || empty($harga)) {
        echo "Semua kolom harus diisi!";
    } else {
        // Query untuk menambah jadwal
        $query = "INSERT INTO jadwal (id_kereta, asal, tujuan, waktu_berangkat, waktu_tiba, harga) 
                  VALUES ('$id_kereta', '$asal', '$tujuan', '$waktu_berangkat', '$waktu_tiba', '$harga')";

        if (mysqli_query($conn, $query)) {
            // Redirect ke halaman kelola jadwal setelah berhasil menambah
            header('Location: kelola_jadwal.php');
            exit();
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Jadwal - Sistem Tiket Kereta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Tambah Jadwal Kereta</h2>
        <form method="POST">
            <div class="form-group">
                <label for="id_kereta">Nama Kereta</label>
                <select class="form-control" name="id_kereta" id="id_kereta" required>
                    <?php
                    // Menampilkan daftar kereta yang ada di database
                    while ($kereta = mysqli_fetch_assoc($kereta_result)) {
                        echo "<option value='{$kereta['id']}'>{$kereta['nama_kereta']} ({$kereta['jenis']})</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="asal">Asal</label>
                <input type="text" class="form-control" name="asal" id="asal" required>
            </div>
            <div class="form-group">
                <label for="tujuan">Tujuan</label>
                <input type="text" class="form-control" name="tujuan" id="tujuan" required>
            </div>
            <div class="form-group">
                <label for="waktu_berangkat">Waktu Berangkat</label>
                <input type="datetime-local" class="form-control" name="waktu_berangkat" id="waktu_berangkat" required>
            </div>
            <div class="form-group">
                <label for="waktu_tiba">Waktu Tiba</label>
                <input type="datetime-local" class="form-control" name="waktu_tiba" id="waktu_tiba" required>
            </div>
            <div class="form-group">
                <label for="harga">Harga</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input type="text" class="form-control" name="harga" id="harga" required>
                </div>
            </div>

            <script>
                document.getElementById('harga').addEventListener('input', function (e) {
                    var value = e.target.value;

                    // Hapus karakter non-angka selain titik dan koma
                    value = value.replace(/[^0-9,]/g, '');

                    // Cek apakah ada koma dan bagi angka menjadi dua bagian (sebelum dan sesudah koma)
                    if (value.includes(',')) {
                        var parts = value.split(',');
                        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Format ribuan
                        value = parts[0] + ',' + (parts[1] ? parts[1].substring(0, 2) : '00'); // Batasi dua angka setelah koma
                    } else {
                        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Format ribuan
                        value += ',00'; // Tambahkan ",00" jika belum ada koma
                    }

                    e.target.value = value;
                });
            </script>

            <button type="submit" class="btn btn-primary">Tambah Jadwal</button>
        </form>
    </div>
</body>
</html>
