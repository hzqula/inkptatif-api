<?php

require_once '../koneksi.php';

// Tambahkan header CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Daftar parameter yang diizinkan
$validParams = array('id', 'nama', 'nim');
$params = array_keys($_GET);
$invalidParams = array_diff($params, $validParams);

if (!empty($invalidParams)) {
  // Jika ada parameter selain id atau nama, kembalikan pesan kesalahan
  header('Content-Type: application/json');
  http_response_code(400);
  echo json_encode(["error" => "Invalid parameter(s): " . implode(', ', $invalidParams)]);
} else {
  $id = isset($_GET['id']) ? $_GET['id'] : null;
  $nama = isset($_GET['nama']) ? $_GET['nama'] : null;
  $nim = isset($_GET['nim']) ? $_GET['nim'] : null;

  if ($id !== null) {
    // Tampilkan detail berdasarkan id
    $idPattern = "%$id%";
    $statement = $connect->prepare("SELECT s.id, s.judul, s.tempat, s.tanggal, m.nama AS nama, m.nim, kat.jenis AS kategori,
                                        (
                                          SELECT JSON_ARRAYAGG(JSON_OBJECT('nama', d.nama, 'nip', d.nip))
                                          FROM DOSEN d
                                          JOIN DETAIL dt ON d.nip = dt.nip
                                          WHERE dt.id_seminar = s.id AND dt.id_keterangan = 1
                                        ) AS pembimbing,
                                        (
                                          SELECT JSON_ARRAYAGG(JSON_OBJECT('nama', d.nama, 'nip', d.nip))
                                          FROM DOSEN d
                                          JOIN DETAIL dt ON d.nip = dt.nip
                                          WHERE dt.id_seminar = s.id AND dt.id_keterangan = 2
                                        ) AS penguji
                                        FROM SEMINAR s
                                        JOIN DETAIL dt ON dt.id_seminar = s.id
                                        JOIN MAHASISWA m ON m.nim = dt.nim
                                        JOIN KATEGORI kat ON kat.id = dt.id_kategori
                                        WHERE s.id LIKE :idPattern
                                        GROUP BY s.id, s.judul, m.nama, m.nim
                                        ORDER BY s.id");
    $statement->bindParam(":idPattern", $idPattern);
    $statement->execute();
    $data = $statement->fetchAll(PDO::FETCH_ASSOC);

    if ($data) {
      foreach ($data as &$seminar) {
        $seminar['pembimbing'] = $seminar['pembimbing'] ? json_decode($seminar['pembimbing'], true) : [];
        $seminar['penguji'] = $seminar['penguji'] ? json_decode($seminar['penguji'], true) : [];
      }
    }

    header('Content-Type: application/json');
    echo json_encode($data);
  } elseif ($nama !== null) {
    // Tampilkan detail berdasarkan nama
    $namePattern = "%$nama%";
    $statement = $connect->prepare("SELECT s.id, s.judul, s.tempat, s.tanggal, m.nama AS nama, m.nim, kat.jenis AS kategori,
                                        (
                                          SELECT JSON_ARRAYAGG(JSON_OBJECT('nama', d.nama, 'nip', d.nip))
                                          FROM DOSEN d
                                          JOIN DETAIL dt ON d.nip = dt.nip
                                          WHERE dt.id_seminar = s.id AND dt.id_keterangan = 1
                                        ) AS pembimbing,
                                        (
                                          SELECT JSON_ARRAYAGG(JSON_OBJECT('nama', d.nama, 'nip', d.nip))
                                          FROM DOSEN d
                                          JOIN DETAIL dt ON d.nip = dt.nip
                                          WHERE dt.id_seminar = s.id AND dt.id_keterangan = 2
                                        ) AS penguji
                                        FROM SEMINAR s
                                        JOIN DETAIL dt ON dt.id_seminar = s.id
                                        JOIN KATEGORI kat ON kat.id = dt.id_kategori
                                        JOIN MAHASISWA m ON m.nim = dt.nim
                                        WHERE m.nama LIKE :namePattern
                                        GROUP BY s.id, s.judul, m.nama, m.nim
                                        ORDER BY s.id");

    $statement->bindParam(":namePattern", $namePattern);
    $statement->execute();
    $data = $statement->fetchAll(PDO::FETCH_ASSOC);

    if ($data) {
      foreach ($data as &$seminar) {
        $seminar['pembimbing'] = $seminar['pembimbing'] ? json_decode($seminar['pembimbing'], true) : [];
        $seminar['penguji'] = $seminar['penguji'] ? json_decode($seminar['penguji'], true) : [];
      }
    }

    header('Content-Type: application/json');
    echo json_encode($data);
  } elseif ($nim !== null) {
    // Tampilkan detail berdasarkan nim
    $nimPattern = "%$nim%";
    $statement = $connect->prepare("SELECT s.id, s.judul, s.tempat, s.tanggal, m.nama AS nama, m.nim, kat.jenis AS kategori,
                                        (
                                          SELECT JSON_ARRAYAGG(JSON_OBJECT('nama', d.nama, 'nip', d.nip))
                                          FROM DOSEN d
                                          JOIN DETAIL dt ON d.nip = dt.nip
                                          WHERE dt.id_seminar = s.id AND dt.id_keterangan = 1
                                        ) AS pembimbing,
                                        (
                                          SELECT JSON_ARRAYAGG(JSON_OBJECT('nama', d.nama, 'nip', d.nip))
                                          FROM DOSEN d
                                          JOIN DETAIL dt ON d.nip = dt.nip
                                          WHERE dt.id_seminar = s.id AND dt.id_keterangan = 2
                                        ) AS penguji
                                        FROM SEMINAR s
                                        JOIN DETAIL dt ON dt.id_seminar = s.id
                                        JOIN KATEGORI kat ON kat.id = dt.id_kategori
                                        JOIN MAHASISWA m ON m.nim = dt.nim
                                        WHERE m.nim LIKE :nimPattern
                                        GROUP BY s.id, s.judul, m.nama, m.nim
                                        ORDER BY s.id");

    $statement->bindParam(":nimPattern", $nimPattern);
    $statement->execute();
    $data = $statement->fetchAll(PDO::FETCH_ASSOC);

    if ($data) {
      foreach ($data as &$seminar) {
        $seminar['pembimbing'] = $seminar['pembimbing'] ? json_decode($seminar['pembimbing'], true) : [];
        $seminar['penguji'] = $seminar['penguji'] ? json_decode($seminar['penguji'], true) : [];
      }
    }

    header('Content-Type: application/json');
    echo json_encode($data);
  } else {
    // Tampilkan semua data seminar jika tidak ada parameter yang diberikan
    $statement = $connect->prepare("SELECT DISTINCT s.id, s.judul, s.tempat, s.tanggal, m.nama AS nama, m.nim, kat.jenis AS kategori
                                    FROM SEMINAR s
                                    JOIN DETAIL dt ON dt.id_seminar = s.id
                                    JOIN MAHASISWA m ON m.nim = dt.nim
                                    JOIN KATEGORI kat ON kat.id = dt.id_kategori
                                    ORDER BY s.id");
    $statement->execute();
    $data = $statement->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($data);
  }
}
