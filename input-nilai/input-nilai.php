<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

require_once '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $input = json_decode(file_get_contents('php://input'), true);

  // Validasi input
  if (isset($input['nilai'], $input['id_kriteria'], $input['nip'], $input['nim'], $input['id_kategori'], $input['id_keterangan'])) {
    $nilai = $input['nilai'];
    $id_kriteria = $input['id_kriteria'];
    $nip = $input['nip'];
    $nim = $input['nim'];
    $id_kategori = $input['id_kategori'];
    $id_keterangan = $input['id_keterangan'];

    // Persiapan pernyataan SQL
    $stmt = $connect->prepare("INSERT INTO PENILAIAN (id_kriteria, id_kategori, id_keterangan, nip, nim, nilai) VALUES (:id_kriteria, :id_kategori, :id_keterangan, :nip, :nim, :nilai)");

    try {
      $connect->beginTransaction();

      foreach ($id_kriteria as $index => $id) {
        $stmt->bindParam(':id_kriteria', $id);
        $stmt->bindParam(':id_kategori', $id_kategori);
        $stmt->bindParam(':id_keterangan', $id_keterangan);
        $stmt->bindParam(':nip', $nip);
        $stmt->bindParam(':nim', $nim);
        $stmt->bindParam(':nilai', $nilai[$index]);
        $stmt->execute();
      }

      $connect->commit();
      echo json_encode(["message" => "Nilai berhasil disimpan."]);
    } catch (Exception $e) {
      $connect->rollBack();
      echo json_encode(["error" => "Terjadi kesalahan saat menyimpan nilai: " . $e->getMessage()]);
    }
  } else {
    echo json_encode(["error" => "Data tidak valid."]);
  }
} else {
  echo json_encode(["error" => "Metode permintaan tidak valid."]);
}
