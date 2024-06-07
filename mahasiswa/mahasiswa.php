<?php

require_once '../koneksi.php';

// Tambahkan header CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Daftar parameter yang diizinkan
$validParams = array('nim');
$params = array_keys($_GET);
$invalidParams = array_diff($params, $validParams);

if (!empty($invalidParams)) {
  // Jika ada parameter selain nim, kembalikan pesan kesalahan
  header('Content-Type: application/json');
  http_response_code(400);
  echo json_encode(["error" => "Invalid parameter(s): " . implode(', ', $invalidParams)]);
} else {
  $nim = isset($_GET['nim']) ? $_GET['nim'] : null;

  if ($nim !== null) {
    // Hanya tampilkan detail jika parameter nim diberikan
    $nimPattern = "%$nim%";
    $statement = $connect->prepare("SELECT m.nama, m.nim, kat.jenis AS kategori,
                                  (
                                  SELECT JSON_ARRAYAGG(JSON_OBJECT('nama', d.nama, 'nip', d.nip))
                                  FROM DOSEN d
                                  JOIN DETAIL dt ON d.nip = dt.nip
                                  WHERE dt.nim = m.nim AND dt.id_keterangan = 1
                                  ) AS pembimbing,
                                  (
                                  SELECT JSON_ARRAYAGG(JSON_OBJECT('nama', d.nama, 'nip', d.nip))
                                  FROM DOSEN d
                                  JOIN DETAIL dt ON d.nip = dt.nip
                                  WHERE dt.nim = m.nim AND dt.id_keterangan = 2
                                  ) AS penguji
                                  FROM MAHASISWA m 
                                  JOIN DETAIL dt ON dt.nim = m.nim
                                  JOIN KATEGORI kat ON kat.id = dt.id_kategori
                                  WHERE m.nim LIKE :nimPattern
                                  GROUP BY m.nim, m.nama, kat.jenis
                                  ORDER BY m.nim");
    $statement->bindParam(":nimPattern", $nimPattern);
    $statement->execute();
    $data = $statement->fetchAll(PDO::FETCH_ASSOC);

    if ($data) {
      foreach ($data as &$mahasiswa) {
        $mahasiswa['pembimbing'] = json_decode($mahasiswa['pembimbing'], true);
        $mahasiswa['penguji'] = json_decode($mahasiswa['penguji'], true);
      }
    }

    header('Content-Type: application/json');
    echo json_encode($data);
  } else {
    // Tampilkan semua data mahasiswa jika parameter nim tidak diberikan
    $statement = $connect->prepare("SELECT * FROM mahasiswa ORDER BY nim");
    $statement->execute();
    $data = $statement->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($data);
  }
}
