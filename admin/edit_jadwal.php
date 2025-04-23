<?php
session_start();

// Validasi role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../koneksi.php';

// Ambil data jadwal jika ID ada
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data jadwal yang akan diedit
    $query = "SELECT * FROM jadwal WHERE id = $id";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 0) {
        die("Data jadwal tidak ditemukan.");
    }

    $row = mysqli_fetch_assoc($result);
}

// Proses update jadwal
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $asal = mysqli_real_escape_string($conn, $_POST['asal']);
    $tujuan = mysqli_real_escape_string($conn, $_POST['tujuan']);
    $waktu_berangkat = mysqli_real_escape_string($conn, $_POST['waktu_berangkat']);
    $waktu_tiba = mysqli_real_escape_string($conn, $_POST['waktu_tiba']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);
    $id_kereta = mysqli_real_escape_string($conn, $_POST['id_kereta']);

    // Update data jadwal
    $update_query = "
        UPDATE jadwal
        SET asal = '$asal', tujuan = '$tujuan', waktu_berangkat = '$waktu_berangkat',
            waktu_tiba = '$waktu_tiba', harga = '$harga', id_kereta = '$id_kereta'
        WHERE id = $id
    ";

    if (mysqli_query($conn, $update_query)) {
        header('Location: kelola_jadwal.php?status=updated');
    } else {
        die("Gagal mengupdate jadwal: " . mysqli_error($conn));
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Jadwal - Sistem Tiket Kereta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Add necessary CSS or Bootstrap -->
</head>
<body>

<h2>Edit Jadwal</h2>

<form action="edit_jadwal.php?id=<?= $row['id']; ?>" method="POST">
    <div>
        <label for="asal">Asal</label>
        <input type="text" name="asal" value="<?= $row['asal']; ?>" required>
    </div>
    <div>
        <label for="tujuan">Tujuan</label>
        <input type="text" name="tujuan" value="<?= $row['tujuan']; ?>" required>
    </div>
    <div>
        <label for="waktu_berangkat">Waktu Berangkat</label>
        <input type="datetime-local" name="waktu_berangkat" value="<?= $row['waktu_berangkat']; ?>" required>
    </div>
    <div>
        <label for="waktu_tiba">Waktu Tiba</label>
        <input type="datetime-local" name="waktu_tiba" value="<?= $row['waktu_tiba']; ?>" required>
    </div>
    <div>
        <label for="harga">Harga</label>
        <input type="number" name="harga" value="<?= $row['harga']; ?>" required>
    </div>
    <div>
        <label for="id_kereta">ID Kereta</label>
        <input type="number" name="id_kereta" value="<?= $row['id_kereta']; ?>" required>
    </div>
    <button type="submit">Update Jadwal</button>
</form>

</body>
</html>
