<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit();
}

function statusBanjir($tinggi_air){
    if($tinggi_air >= 0 && $tinggi_air <= 50){
        return "Aman";
    } elseif($tinggi_air >= 51 && $tinggi_air <= 100){
        return "Waspada";
    }
    return "Bahaya";
}

$error = "";

if(isset($_POST['simpan'])){

    $lokasi_sungai    = trim($_POST['lokasi_sungai']);
    $waktu_pengukuran = $_POST['waktu_pengukuran'];
    $tinggi_air       = $_POST['tinggi_air'];
    $deskripsi        = trim($_POST['deskripsi']);

    if(empty($lokasi_sungai)){
        $error = "Lokasi wajib diisi";
    } elseif(empty($waktu_pengukuran)){
        $error = "Waktu pengukuran wajib diisi";
    } elseif($tinggi_air === "" || !is_numeric($tinggi_air)){
        $error = "Tinggi air wajib diisi dengan angka";
    } elseif(empty($_FILES['foto_bukti']['name'])){
        $error = "Foto bukti wajib diupload";
    } else {

        $status_banjir = statusBanjir((int)$tinggi_air);

        $foto    = $_FILES['foto_bukti']['name'];
        $allowed = ['jpg','jpeg','png'];
        $ext     = strtolower(pathinfo($foto, PATHINFO_EXTENSION));

        if(!in_array($ext, $allowed)){
            $error = "Format file harus JPG, JPEG, atau PNG";
        } elseif($_FILES['foto_bukti']['size'] > 2000000){
            $error = "Ukuran file maksimal 2MB";
        } else {
            // Buat nama file unik agar tidak tabrakan
            $namaFile = time() . '_' . basename($foto);
            move_uploaded_file(
                $_FILES['foto_bukti']['tmp_name'],
                'uploads/' . $namaFile
            );

            $lokasi_sungai    = mysqli_real_escape_string($conn, $lokasi_sungai);
            $waktu_pengukuran = mysqli_real_escape_string($conn, $waktu_pengukuran);
            $deskripsi        = mysqli_real_escape_string($conn, $deskripsi);
            $user_id          = $_SESSION['id'];

            mysqli_query($conn, "
                INSERT INTO laporan(
                    user_id, lokasi_sungai, waktu_pengukuran,
                    tinggi_air, status_banjir, deskripsi, foto_bukti
                )
                VALUES(
                    '$user_id', '$lokasi_sungai', '$waktu_pengukuran',
                    '$tinggi_air', '$status_banjir', '$deskripsi', '$namaFile'
                )
            ");

            header("Location: laporan.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Laporan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2>Tambah Laporan</h2>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Lokasi Sungai</label>
            <input type="text" name="lokasi_sungai" placeholder="Contoh: Sungai Citarum" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Waktu Pengukuran</label>
            <input type="datetime-local" name="waktu_pengukuran" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Tinggi Air (cm)</label>
            <input type="number" name="tinggi_air" placeholder="Contoh: 75" class="form-control" min="0">
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" placeholder="Kondisi di lapangan..." class="form-control" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Foto Bukti (JPG/JPEG/PNG, maks 2MB)</label>
            <input type="file" name="foto_bukti" class="form-control" accept=".jpg,.jpeg,.png">
        </div>
        <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
        <a href="laporan.php" class="btn btn-secondary ms-2">Batal</a>
    </form>
</div>

<?php include 'footer.php'; ?>

</body>
</html>

