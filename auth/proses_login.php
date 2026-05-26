<?php
session_start();

include '../config/database.php';

if(isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = $conn->prepare("
        SELECT *
        FROM users
        WHERE username=?
    ");

    $query->execute([$username]);

    $user = $query->fetch(PDO::FETCH_ASSOC);

    if($user) {
        if(password_verify($password, $user['password'])) {

            $_SESSION['login'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['level'] = $user['level'];

            if($user['level'] == 'admin') {
                header('Location: ../admin/dashboard.php');
                exit;
            } else {
                header('Location: ../staff/dashboard.php');
                exit;
            }
        } else {
            header('Location: login.php?pesan=gagal');
            exit;
        }
    } else {
        header('Location: login.php?pesan=gagal');
        exit;
    }
}
?>