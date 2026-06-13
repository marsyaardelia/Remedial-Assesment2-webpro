<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['id'])) {
    header("Location: login.php");
}

$id = $_SESSION['id'];

$data = mysqli_query($conn, "SELECT * FROM laporan WHERE user_id='$id'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

<div class="container mt-5">
    <a href="tambah_laporan.php" class="btn btn-primary mb-3">Tambah Laporan</a>

    <table class="table table-bordered">
        <tr>
            <th>Lokasi</th>
            <th>Waktu</th>
            <th>Tinggi air</th>
            <th>Status</th>
            <th>Deskripsi</th>
            <th>Foto</th>
            <th>Aksi</th>
        </tr>

        <?php while($row = mysqli_fetch_assoc($data)) { ?>

        <tr>
            <td><?php echo $row['lokasi_sungai']; ?></td>
            <td><?php echo $row['waktu_pengukuran']; ?></td>
            <td><?php echo $row['tinggi_air']; ?></td>
            <td><?php echo $row['status_banjir']; ?></td>
            <td><?php echo $row['deskripsi']; ?></td>
            <td>
                <img src="uploads/<?php echo $row['foto_bukti']; ?>" width="100">
            </td>
            <td>
                <a href="edit_laporan.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>

                <a href="hapus_laporan.php?id=<?php echo $row['id']; ?>" class="btn btn-danger">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

<?php include 'footer.php'; ?>

</body>
</html>