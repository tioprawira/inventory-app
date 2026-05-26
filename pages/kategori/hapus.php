<?php
include '../../auth/cek_login.php';
include '../../config/database.php';

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM kategori WHERE id=?");
$stmt->execute([$id]);

header('Location: index.php');
?>