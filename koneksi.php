<?php
$host = 'localhost';
$dbname = 'u374195687_inkptatif';
$user = 'u374195687_root';
$passwrod = '@IlooqstrasiHZ0113';

try {
  $connect = new PDO("mysql:host=$host;dbname=$dbname", $user, $passwrod);
  $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
  die("Error Message: " . $e->getMessage());
}
