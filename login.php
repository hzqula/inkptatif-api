<?php
include 'vendor/autoload.php';

// Tambahkan header CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
  header('Content-Type: application/json');
  http_response_code(200);
  exit;
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json');

$dosen = new \App\app\Dosen();
//index.php?app=dosen&action=login
//index.php?app=dosen&action=get
$action = $_GET['action'];
$dosen->$action();
