<?php
session_start();
$error = '';
$validate = '';
require('koneksi.php');

// Cek apakah sudah login
if (isset($_SESSION['pengguna'])) {
    header('Location: pengguna/index.php');
    exit();
} 

if (isset($_POST['submit'])) {
    $nama = mysqli_real_escape_string($conn, stripslashes($_POST['nama']));
    $email = mysqli_real_escape_string($conn, stripslashes($_POST['email']));
    $password = mysqli_real_escape_string($conn, stripslashes($_POST['password']));

    if (!empty(trim($nama)) && !empty(trim($email)) && !empty(trim($password)) ) {
        if (cek_email($email, $conn) == 0) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (nama, email, password, role) 
                      VALUES ('$nama', '$email', '$hashed_password', 'pengguna')";
            $result = mysqli_query($conn, $query);

            if ($result) {
                $_SESSION['email'] = $email; // Simpan hanya email
                // Tidak menyimpan role di session
                header('Location: login.php'); // Arahkan ke halaman login
                exit();
            } else {
                $error = 'Pendaftaran gagal, coba lagi! ' . mysqli_error($conn);
            }
        } else {
            $error = 'Email sudah terdaftar!';
        }
    } else {
        $error = 'Data tidak boleh kosong!';
    }
}

function cek_email($email, $conn) {
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt === false) {
        die("Penyusunan query gagal: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "s", $email);
    if (!mysqli_stmt_execute($stmt)) {
        die("Eksekusi query gagal: " . mysqli_stmt_error($stmt));
    }
    $result = mysqli_stmt_get_result($stmt);
    $num_rows = mysqli_num_rows($result);
    mysqli_stmt_close($stmt);
    return $num_rows;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            background-color: #f39c12;
            border: none;
            transition: all 0.3s ease;
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
    <section class="container-fluid mb-4">
        <section class="row justify-content-center">
            <section class="col-12 col-sm-6 col-md-4">
                <form class="form-container" action="register.php" method="POST">
                    <h4 class="text-center font-weight-bold">Sign-Up</h4>
                    <?php if ($error != ''): ?>
                        <div class="alert alert-danger" role="alert"><?= $error; ?></div>
                    <?php endif; ?>
                    <?php if ($validate != ''): ?>
                        <div class="alert alert-danger" role="alert"><?= $validate; ?></div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="username">Nama Lengkap</label>
                        <input type="text" class="form-control" id="username" name="nama" placeholder="Masukkan nama lengkap" required>
                    </div>
                    <div class="form-group">
                        <label for="inputpassword">Email</label>
                        <input type="email" class="form-control" id="inputEmail" name="email" placeholder="Masukkan Email" required>
                    </div>
                    <div class="form-group">
                        <label for="inputpassword">Password</label>
                        <input type="password" class="form-control" id="inputpassword" name="password" placeholder="Masukkan password" required>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary btn-block">Register</button>

                    <div class="form-footer mt-2">
                        <p>Sudah punya akun? <a href="login.php">Login</a></p>
                    </div>
                </form>
            </section>
        </section>
    </section>

    <!-- Bootstrap JS, jQuery, dan Popper.js -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
