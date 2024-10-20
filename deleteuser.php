<?php
session_start();
require 'config.php';

$role = $_SESSION['role'];

if (!isset($_SESSION["login"]) || $role != 'GOD') {
    echo json_encode([]);
    header("Location: index.php");
    exit;
}

$id = $_GET["delete"];


if (deleteuser($id) > 0) {
    echo "
        <script>
            alert('data user berhasil dihapus');
            document.location.href = 'admin.php';
        </script>
        ";
} else {
    echo "
        <script>
            alert('data user gagal dihapus');
            document.location.href = 'admin.php';
        </script>
        ";
}
