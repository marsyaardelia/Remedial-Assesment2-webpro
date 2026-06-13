<?php
include '../koneksi.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight
if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];


$id = null;
$pathInfo = $_SERVER['PATH_INFO'] ?? '';
if(preg_match('/^\/(\d+)$/', $pathInfo, $match)){
    $id = (int)$match[1];
}

if(!$id && isset($_GET['id'])){
    $id = (int)$_GET['id'];
}

function respond($code, $data) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

function hitungStatus($tinggi_air) {
    if($tinggi_air <= 50)        return "Aman";
    elseif($tinggi_air <= 100)   return "Waspada";
    else                          return "Bahaya";
}

switch($method) {

    case 'GET':
        if($id){
            $row = mysqli_fetch_assoc(
                mysqli_query($conn, "SELECT * FROM laporan WHERE id='$id'")
            );
            if($row){
                respond(200, ['status'=>'success','data'=>$row]);
            } else {
                respond(404, ['status'=>'error','message'=>'Data tidak ditemukan']);
            }
        } else {
            $result = mysqli_query($conn, "SELECT * FROM laporan ORDER BY id DESC");
            $data = [];
            while($row = mysqli_fetch_assoc($result)){
                $data[] = $row;
            }
            respond(200, ['status'=>'success','data'=>$data]);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if(!$input) $input = $_POST;

        $lokasi_sungai    = mysqli_real_escape_string($conn, trim($input['lokasi_sungai'] ?? ''));
        $waktu_pengukuran = mysqli_real_escape_string($conn, $input['waktu_pengukuran'] ?? '');
        $tinggi_air       = (int)($input['tinggi_air'] ?? 0);
        $deskripsi        = mysqli_real_escape_string($conn, trim($input['deskripsi'] ?? ''));
        $user_id          = (int)($input['user_id'] ?? 0);
        $foto_bukti       = mysqli_real_escape_string($conn, $input['foto_bukti'] ?? '');

        if(empty($lokasi_sungai) || empty($waktu_pengukuran) || !$user_id){
            respond(400, ['status'=>'error','message'=>'Field lokasi_sungai, waktu_pengukuran, dan user_id wajib diisi']);
        }

        $status_banjir = hitungStatus($tinggi_air);

        $ok = mysqli_query($conn, "
            INSERT INTO laporan(user_id, lokasi_sungai, waktu_pengukuran, tinggi_air, status_banjir, deskripsi, foto_bukti)
            VALUES('$user_id','$lokasi_sungai','$waktu_pengukuran','$tinggi_air','$status_banjir','$deskripsi','$foto_bukti')
        ");

        if($ok){
            respond(201, ['status'=>'success','message'=>'Data berhasil ditambahkan','id'=>mysqli_insert_id($conn)]);
        } else {
            respond(500, ['status'=>'error','message'=>'Gagal menyimpan data']);
        }
        break;

    case 'PUT':
    case 'PATCH':
        if(!$id) respond(400, ['status'=>'error','message'=>'ID diperlukan']);

        $input = json_decode(file_get_contents('php://input'), true);

        // Ambil data lama
        $lama = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM laporan WHERE id='$id'"));
        if(!$lama) respond(404, ['status'=>'error','message'=>'Data tidak ditemukan']);

        $lokasi_sungai    = mysqli_real_escape_string($conn, trim($input['lokasi_sungai']    ?? $lama['lokasi_sungai']));
        $waktu_pengukuran = mysqli_real_escape_string($conn, $input['waktu_pengukuran']       ?? $lama['waktu_pengukuran']);
        $tinggi_air       = isset($input['tinggi_air']) ? (int)$input['tinggi_air']           : (int)$lama['tinggi_air'];
        $deskripsi        = mysqli_real_escape_string($conn, trim($input['deskripsi']         ?? $lama['deskripsi']));
        $foto_bukti       = mysqli_real_escape_string($conn, $input['foto_bukti']             ?? $lama['foto_bukti']);

        $status_banjir = hitungStatus($tinggi_air);

        $ok = mysqli_query($conn, "
            UPDATE laporan SET
                lokasi_sungai='$lokasi_sungai',
                waktu_pengukuran='$waktu_pengukuran',
                tinggi_air='$tinggi_air',
                status_banjir='$status_banjir',
                deskripsi='$deskripsi',
                foto_bukti='$foto_bukti'
            WHERE id='$id'
        ");

        if($ok){
            respond(200, ['status'=>'success','message'=>'Data berhasil diupdate']);
        } else {
            respond(500, ['status'=>'error','message'=>'Gagal mengupdate data']);
        }
        break;

   
    case 'DELETE':
        if(!$id) respond(400, ['status'=>'error','message'=>'ID diperlukan']);

        $lama = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM laporan WHERE id='$id'"));
        if(!$lama) respond(404, ['status'=>'error','message'=>'Data tidak ditemukan']);

        // Hapus file foto
        $filePath = __DIR__ . '/../uploads/' . $lama['foto_bukti'];
        if($lama['foto_bukti'] && file_exists($filePath)){
            unlink($filePath);
        }

        $ok = mysqli_query($conn, "DELETE FROM laporan WHERE id='$id'");
        if($ok){
            respond(200, ['status'=>'success','message'=>'Data berhasil dihapus']);
        } else {
            respond(500, ['status'=>'error','message'=>'Gagal menghapus data']);
        }
        break;

    default:
        respond(405, ['status'=>'error','message'=>'Method tidak diizinkan']);
}