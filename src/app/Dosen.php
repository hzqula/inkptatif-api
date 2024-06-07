<?php

namespace App\app;

use App\config\Database;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Dosen
{
  protected \PDO $connection;

  public function __construct()
  {
    $db = new Database();
    $this->connection = $db->getConnection();
  }

  /**
   * Login via API
   * @return void
   */
  public function login()
  {
    header('Content-Type: application/json');

    // Tangkap data input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Jika input kosong, coba ambil dari POST
    if (is_null($data)) {
      $data = $_POST;
    }

    // Log untuk debugging
    error_log("Data diterima: " . print_r($data, true));

    if (!isset($data['nip']) || !isset($data['password'])) {
      echo json_encode(['message' => 'Invalid input']);
      exit();
    }

    $nip = $data['nip'];
    $password = $data['password'];

    // Log data untuk debugging
    error_log("NIP: $nip, Password: $password");

    $query = 'SELECT * FROM DOSEN WHERE nip = :nip';
    $statement = $this->connection->prepare($query);
    $statement->bindValue(':nip', $nip);
    $statement->execute();

    if ($statement->rowCount() === 0) {
      echo json_encode(['message' => 'Invalid username or password']);
      exit();
    }

    $result = $statement->fetch(\PDO::FETCH_OBJ);

    if ($result->password !== $password) { // Adjust this if password is encrypted
      echo json_encode(['message' => 'Invalid username or password']);
      exit();
    }

    $data = [
      'nip' => $result->nip,
      'nama' => $result->nama,
    ];

    $token = JWT::encode($data, \App\app\AppJwt::JWT_TOKEN_SECRET, 'HS256');
    echo json_encode(['token' => $token]);
  }


  /**
   * Melihat info user yang mengakses berdasarkan JWT
   * @return void
   */
  public function get()
  {
    $allHeaders = getallheaders();
    if (!isset($allHeaders['Authorization'])) {
      http_response_code(401);
      exit();
    }

    list(, $token) = explode(' ', $allHeaders['Authorization']);

    $decoded = JWT::decode($token, new Key(AppJwt::JWT_TOKEN_SECRET, 'HS256'));
    $dosen = [
      'nip' => $decoded->nip,
      'nama' => $decoded->nama,
    ];

    header('Content-Type: application/json');
    echo json_encode($dosen);
  }
}
