<?php
$host = 'localhost';
$dbname = 'inkptatif_v1';
$user = 'root';
$passwrod = '@IlooqstrasiHZ0113';

try {
  $connect = new PDO("mysql:host=$host;dbname=$dbname", $user, $passwrod);
  $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
  die("Error Message: " . $e->getMessage());
}
