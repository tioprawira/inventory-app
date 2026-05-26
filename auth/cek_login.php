<?php
// Cek apakah session sudah berjalan atau belum sebelum memulainya
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>