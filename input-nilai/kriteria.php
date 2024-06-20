<?php
require_once '../koneksi.php';

// Tambahkan header CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json');

try {
  // Ambil parameter dari query string
  $jenis_kategori = isset($_GET['jenis_kategori']) ? $_GET['jenis_kategori'] : "1";
  $jenis_keterangan = isset($_GET['jenis_keterangan']) ? $_GET['jenis_keterangan'] : "1";

  // Ambil data dari tabel KRITERIA
  $stmt = $connect->prepare("SELECT k.id, k.penilaian FROM kriteria k
                            JOIN kategori kat ON kat.id = k.id_kategori
                            JOIN keterangan ket ON ket.id = k.id_keterangan 
                            WHERE kat.jenis = :jenis_kategori AND ket.jenis = :jenis_keterangan");

  $stmt->bindParam(':jenis_kategori', $jenis_kategori);
  $stmt->bindParam(':jenis_keterangan', $jenis_keterangan);
  $stmt->execute();
  $kriteria = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode($kriteria);
} catch (PDOException $e) {
  echo json_encode(['error' => $e->getMessage()]);
}
