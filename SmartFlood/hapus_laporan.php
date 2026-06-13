<?php
include 'koneksi.php';

$id = $_GET['id'];

$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM laporan WHERE id='$id'"));

unlink('uploads/' . $data['foto_bukti']);

mysqli_query($conn, "DELETE FROM laporan WHERE id='$id'");

header("Location: laporan.php");
?>