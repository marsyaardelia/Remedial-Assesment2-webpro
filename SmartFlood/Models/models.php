<?php

class LaporanModel {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAll() {
        return mysqli_query($this->conn, "SELECT * FROM laporan ORDER BY id DESC");
    }

    public function getByUser($user_id) {
        return mysqli_query($this->conn, "SELECT * FROM laporan WHERE user_id='$user_id' ORDER BY id DESC");
    }

    public function getById($id) {
        $result = mysqli_query($this->conn, "SELECT * FROM laporan WHERE id='$id'");
        return mysqli_fetch_assoc($result);
    }

    public function insert($user_id, $lokasi_sungai, $waktu_pengukuran, $tinggi_air, $status_banjir, $deskripsi, $foto) {
        return mysqli_query($this->conn, "
            INSERT INTO laporan(user_id, lokasi_sungai, waktu_pengukuran, tinggi_air, status_banjir, deskripsi, foto_bukti)
            VALUES('$user_id','$lokasi_sungai','$waktu_pengukuran','$tinggi_air','$status_banjir','$deskripsi','$foto')
        ");
    }

    public function update($id, $lokasi_sungai, $waktu_pengukuran, $tinggi_air, $status_banjir, $deskripsi, $foto = null) {
        if($foto) {
            return mysqli_query($this->conn, "
                UPDATE laporan SET
                    lokasi_sungai='$lokasi_sungai',
                    waktu_pengukuran='$waktu_pengukuran',
                    tinggi_air='$tinggi_air',
                    status_banjir='$status_banjir',
                    deskripsi='$deskripsi',
                    foto_bukti='$foto'
                WHERE id='$id'
            ");
        } else {
            return mysqli_query($this->conn, "
                UPDATE laporan SET
                    lokasi_sungai='$lokasi_sungai',
                    waktu_pengukuran='$waktu_pengukuran',
                    tinggi_air='$tinggi_air',
                    status_banjir='$status_banjir',
                    deskripsi='$deskripsi'
                WHERE id='$id'
            ");
        }
    }

    public function delete($id) {
        return mysqli_query($this->conn, "DELETE FROM laporan WHERE id='$id'");
    }
}