<?php
ob_start();
session_start();
$error = '';
require('koneksi.php');

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['id_user'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: admin/index.php');
                exit();
            } elseif ($user['role'] === 'pengguna') {
                header('Location: pengguna/index.php');
                exit();
            } else {
                $error = "Role tidak dikenali!";
            }
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak ditemukan!";
    }
}
ob_end_flush();
// Misalnya, $nama adalah variabel yang menyimpan nama pengguna

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | Sistem Tiket Kereta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

<!-- Custom CSS -->
<link rel="stylesheet" href="style.css">

<!-- CSS Custom -->
<style>
    body {
        background: linear-gradient(to right, #1e3c72, #2a5298);
        font-family: 'Poppins', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .form-container {
        background: #fff;
        padding: 35px 30px;
        border-radius: 20px;
        box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 400px;
    }

    .form-container h4 {
        font-weight: 600;
        color: #2c3e50;
        text-align: center;
        margin-bottom: 25px;
    }

    .btn-custom {
    background-color: #007bff; /* Mengubah warna menjadi biru */
    border: none;
    transition: all 0.3s ease;
}

.btn-custom:hover {
    background-color: #0056b3; /* Mengubah warna saat hover menjadi biru lebih gelap */
}


    .btn-custom:hover {
        background-color: #e67e22;
    }

    .form-footer a {
        color: #2980b9;
        text-decoration: none;
        font-weight: 500;
    }

    .form-footer a:hover {
        text-decoration: underline;
    }

    .alert-danger {
        font-size: 14px;
        margin-bottom: 15px;
        border-radius: 8px;
    }
</style>
</head>
<body>

<div class="form-container">
    <h4>Login Sistem Tiket Kereta</h4>

    <?php if ($error != ''): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
    <div class="form-group">
                        <label for="inputpassword">Email</label>
                        <input type="email" class="form-control" id="inputEmail" name="email" placeholder="Masukkan Email" required>
                    </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan kata sandi" required>
        </div>

        <button type="submit" name="submit" class="btn btn-custom btn-block">Masuk</button>

        <div class="form-footer mt-3 text-center">
            <p>Belum punya akun? <a href="register.php">Daftar Sekarang</a></p>
        </div>
    </form>
</div>
    <!-- Bootstrap JS, jQuery, dan Popper.js -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
