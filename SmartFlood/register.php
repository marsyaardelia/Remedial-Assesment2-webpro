<?php
session_start();
include 'koneksi.php';

$error = "";
$success = "";

if(isset($_POST['register'])){

    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if(empty($nama) || empty($email) || empty($password)){
        $error = "Semua field wajib diisi";
    }
    else{

        $stmt = mysqli_prepare(
            $conn,
            "SELECT id FROM users WHERE email=?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $email
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result) > 0){

            $error = "Email sudah terdaftar";

        }else{

            $hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO users(nama,email,password)
                 VALUES(?,?,?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sss",
                $nama,
                $email,
                $hash
            );

            if(mysqli_stmt_execute($stmt)){
                $success = "Registrasi berhasil";
            }else{
                $error = "Registrasi gagal";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container auth-box">
    <h2>Register</h2>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if(!empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="nama" class="form-control mb-3" placeholder="Nama Lengkap" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
        <input type="email" name="email" class="form-control mb-3" placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        <input type="password" name="password" class="form-control mb-3" placeholder="Password (min 6 karakter)">
        <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
    </form>

    <p class="text-center mt-3"><a href="login.php">Sudah punya akun? Login</a></p>
</div>
</body>
</html>