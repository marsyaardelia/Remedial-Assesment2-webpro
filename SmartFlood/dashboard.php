<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

$total   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE user_id='$user_id'"));
$aman    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE user_id='$user_id' AND status_banjir='Aman'"));
$waspada = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE user_id='$user_id' AND status_banjir='Waspada'"));
$bahaya  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE user_id='$user_id' AND status_banjir='Bahaya'"));
$terbaru = mysqli_query($conn, "SELECT * FROM laporan WHERE user_id='$user_id' ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['nama']) ?> 👋</h2>
    <p class="subtitle">Berikut adalah ringkasan laporan ketinggian air</p>

    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card dashboard-card p-3">
                <div class="icon-circle blue">📊</div>
                <div class="card-content">
                    <h5>Total Laporan</h5>
                    <h2><?= $total['total'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-card p-3">
                <div class="icon-circle green">😊</div>
                <div class="card-content">
                    <h5>Aman</h5>
                    <h2><?= $aman['total'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-card p-3">
                <div class="icon-circle orange">😐</div>
                <div class="card-content">
                    <h5>Waspada</h5>
                    <h2><?= $waspada['total'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-card p-3">
                <div class="icon-circle red">☹️</div>
                <div class="card-content">
                    <h5>Bahaya</h5>
                    <h2><?= $bahaya['total'] ?></h2>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mt-5">📈 Laporan Terbaru</h3>

    <table class="table table-bordered">
        <tr>
            <th>Lokasi</th>
            <th>Waktu</th>
            <th>Tinggi Air</th>
            <th>Status</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($terbaru)): ?>
        <tr>
            <td><?= htmlspecialchars($row['lokasi_sungai']) ?></td>
            <td><?= $row['waktu_pengukuran'] ?></td>
            <td><?= $row['tinggi_air'] ?> cm</td>
            <td>
                <?php
                $badge = ['Aman'=>'success','Waspada'=>'warning','Bahaya'=>'danger'];
                $b = $badge[$row['status_banjir']] ?? 'secondary';
                ?>
                <span class="badge bg-<?= $b ?>"><?= $row['status_banjir'] ?></span>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div class="text-center mt-4">
        <a href="laporan.php" class="btn btn-primary btn-lg">📄 Lihat Semua Laporan</a>
    </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
