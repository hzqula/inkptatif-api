<?php

require_once '../koneksi.php';

// Tambahkan header CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Daftar parameter yang diizinkan
$validParams = array('nip');
$params = array_keys($_GET);
$invalidParams = array_diff($params, $validParams);

if (!empty($invalidParams)) {
  // Jika ada parameter selain nim, kembalikan pesan kesalahan
  header('Content-Type: application/json');
  http_response_code(400);
  echo json_encode(["error" => "Invalid parameter(s): " . implode(', ', $invalidParams)]);
} else {
  $nip = isset($_GET['nip']) ? $_GET['nip'] : null;

  if ($nip !== null) {
    // Hanya tampilkan detail jika parameter nim diberikan
    $nipPattern = "%$nip%";
    $statement = $connect->prepare("SELECT d.nama, d.nip,
                                        (
                                          SELECT JSON_ARRAYAGG(JSON_OBJECT('nama', m.nama, 'nim', m.nim, 'kategori', kat.jenis))
                                          FROM mahasiswa m
                                          JOIN detail dt ON m.nim = dt.nim
                                          JOIN keterangan ket ON ket.id = dt.id_keterangan
                                          JOIN kategori kat ON kat.id = dt.id_kategori
                                          WHERE ket.id = 1 AND dt.nip = d.nip
                                        ) AS dibimbing,
                                        (
                                          SELECT JSON_ARRAYAGG(JSON_OBJECT('nama', m.nama, 'nim', m.nim, 'kategori', kat.jenis))
                                          FROM mahasiswa m
                                          JOIN detail dt ON m.nim = dt.nim
                                          JOIN keterangan ket ON ket.id = dt.id_keterangan
                                          JOIN kategori kat ON kat.id = dt.id_kategori
                                          WHERE ket.id = 2 AND dt.nip = d.nip
                                        ) AS diuji
                                        FROM dosen d WHERE d.nip LIKE :nipPattern
                                        ORDER BY d.nip");
    $statement->bindParam(":nipPattern", $nipPattern);
    $statement->execute();
    $data = $statement->fetchAll(PDO::FETCH_ASSOC);

    if ($data) {
      foreach ($data as &$dosen) {
        // Pengecekan apakah nilai dibimbing dan diuji adalah null
        // Jika null, maka diisi dengan array kosong
        $dosen['dibimbing'] = $dosen['dibimbing'] !== null ? json_decode($dosen['dibimbing'], true) : [];
        $dosen['diuji'] = $dosen['diuji'] !== null ? json_decode($dosen['diuji'], true) : [];
      }
    }


    header('Content-Type: application/json');
    echo json_encode($data);
  } else {
    // Tampilkan semua data mahasiswa jika parameter nim tidak diberikan
    $statement = $connect->prepare("SELECT * FROM dosen ORDER BY nip");
    $statement->execute();
    $data = $statement->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($data);
  }
}
