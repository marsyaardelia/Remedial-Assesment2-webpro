<?php

include __DIR__ . '/../koneksi.php';
include __DIR__ . '/../Models/models.php';

class LaporanController {

    private $model;

    public function __construct($conn) {
        $this->model = new LaporanModel($conn);
    }

    public function index() {
        return $this->model->getAll();
    }

    public function getById($id) {
        return $this->model->getById($id);
    }

    public function store() {
        if(isset($_POST['simpan'])){
            $lokasi_sungai    = mysqli_real_escape_string($GLOBALS['conn'], trim($_POST['lokasi_sungai']));
            $waktu_pengukuran = $_POST['waktu_pengukuran'];
            $tinggi_air       = (int)$_POST['tinggi_air'];
            $deskripsi        = mysqli_real_escape_string($GLOBALS['conn'], trim($_POST['deskripsi']));

            if($tinggi_air <= 50){
                $status_banjir = "Aman";
            } elseif($tinggi_air <= 100){
                $status_banjir = "Waspada";
            } else {
                $status_banjir = "Bahaya";
            }

            $foto     = $_FILES['foto_bukti']['name'];
            $namaFile = time() . '_' . basename($foto);
            move_uploaded_file(
                $_FILES['foto_bukti']['tmp_name'],
                __DIR__ . '/../uploads/' . $namaFile
            );

            $this->model->insert(
                $_SESSION['id'],
                $lokasi_sungai,
                $waktu_pengukuran,
                $tinggi_air,
                $status_banjir,
                $deskripsi,
                $namaFile
            );

            header("Location: ../laporan.php");
            exit();
        }
    }

    public function update($id) {
        $lokasi_sungai    = mysqli_real_escape_string($GLOBALS['conn'], trim($_POST['lokasi_sungai']));
        $waktu_pengukuran = $_POST['waktu_pengukuran'];
        $tinggi_air       = (int)$_POST['tinggi_air'];
        $deskripsi        = mysqli_real_escape_string($GLOBALS['conn'], trim($_POST['deskripsi']));

        if($tinggi_air <= 50)       $status_banjir = "Aman";
        elseif($tinggi_air <= 100)  $status_banjir = "Waspada";
        else                         $status_banjir = "Bahaya";

        return $this->model->update($id, $lokasi_sungai, $waktu_pengukuran, $tinggi_air, $status_banjir, $deskripsi);
    }

    public function delete($id) {
        return $this->model->delete($id);
    }
}
