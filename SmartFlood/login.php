<?php
session_start();
include 'koneksi.php';

$error = "";

if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM users WHERE email=?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "s",
        $email
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) > 0){

        $user = mysqli_fetch_assoc($result);

        if(password_verify(
            $password,
            $user['password']
        )){

            $_SESSION['id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['email'] = $user['email'];

            header("Location: dashboard.php");
            exit();

        }else{
            $error = "Password salah";
        }

    }else{
        $error = "Email tidak ditemukan";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container auth-box">
    <h2>Login</h2>
    <?php if(!empty($error)) : ?>
<div class="alert alert-danger">
    <?= $error ?>
</div>
<?php endif; ?>
    <form method="POST">
        <input type="email" name="email" class="form-control mb-3" placeholder="Email">

        <input type="password" name="password" class="form-control mb-3" placeholder="Password">

        <button type="submit" name="login" class="btn btn-success">Login</button>
    </form>

    <a href="register.php">Belum punya akun?</a>
</div>
</body>
</html>