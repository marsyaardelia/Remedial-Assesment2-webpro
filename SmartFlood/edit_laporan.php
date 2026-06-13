<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit();
}

$id   = (int)$_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM laporan WHERE id='$id'"));

if(!$data){
    die("Data tidak ditemukan.");
}

$error = "";

if(isset($_POST['update'])){
    $lokasi_sungai    = trim($_POST['lokasi_sungai']);
    $waktu_pengukuran = $_POST['waktu_pengukuran'];
    $tinggi_air       = $_POST['tinggi_air'];
    $deskripsi        = trim($_POST['deskripsi']);

    // Tentukan status otomatis dari tinggi air
    if($tinggi_air >= 0 && $tinggi_air <= 50){
        $status_banjir = "Aman";
    } elseif($tinggi_air >= 51 && $tinggi_air <= 100){
        $status_banjir = "Waspada";
    } else {
        $status_banjir = "Bahaya";
    }

    $foto = $data['foto_bukti']; // default pakai foto lama

    if(!empty($_FILES['foto_bukti']['name'])){
        $namaFileBaru = $_FILES['foto_bukti']['name'];
        $allowed      = ['jpg','jpeg','png'];
        $ext          = strtolower(pathinfo($namaFileBaru, PATHINFO_EXTENSION));

        if(!in_array($ext, $allowed)){
            $error = "Format file harus JPG, JPEG, atau PNG";
        } elseif($_FILES['foto_bukti']['size'] > 2000000){
            $error = "Ukuran file maksimal 2MB";
        } else {
            // Hapus file lama
            if(file_exists('uploads/' . $data['foto_bukti'])){
                unlink('uploads/' . $data['foto_bukti']);
            }
            $foto = time() . '_' . basename($namaFileBaru);
            move_uploaded_file($_FILES['foto_bukti']['tmp_name'], 'uploads/' . $foto);
        }
    }

    if(empty($error)){
        $lokasi_sungai    = mysqli_real_escape_string($conn, $lokasi_sungai);
        $waktu_pengukuran = mysqli_real_escape_string($conn, $waktu_pengukuran);
        $deskripsi        = mysqli_real_escape_string($conn, $deskripsi);

        mysqli_query($conn, "UPDATE laporan SET
            lokasi_sungai    = '$lokasi_sungai',
            waktu_pengukuran = '$waktu_pengukuran',
            tinggi_air       = '$tinggi_air',
            status_banjir    = '$status_banjir',
            deskripsi        = '$deskripsi',
            foto_bukti       = '$foto'
            WHERE id='$id'");

        header("Location: laporan.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Laporan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2>Edit Laporan</h2>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Lokasi Sungai</label>
            <input type="text" name="lokasi_sungai" value="<?= htmlspecialchars($data['lokasi_sungai']) ?>" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Waktu Pengukuran</label>
            <input type="datetime-local" name="waktu_pengukuran"
                value="<?= date('Y-m-d\TH:i', strtotime($data['waktu_pengukuran'])) ?>"
                class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Tinggi Air (cm)</label>
            <input type="number" name="tinggi_air" value="<?= $data['tinggi_air'] ?>" class="form-control" min="0">
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($data['deskripsi']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Foto Bukti (kosongkan jika tidak ingin ganti)</label>
            <input type="file" name="foto_bukti" class="form-control" accept=".jpg,.jpeg,.png">
            <small class="text-muted">Foto saat ini:
                <a href="uploads/<?= $data['foto_bukti'] ?>" target="_blank"><?= $data['foto_bukti'] ?></a>
            </small>
        </div>
        <button type="submit" name="update" class="btn btn-success">Update</button>
        <a href="laporan.php" class="btn btn-secondary ms-2">Batal</a>
    </form>
</div>

<?php include 'footer.php'; ?>

</body>
</html>