<?php
session_start();
require 'config.php';

$role = $_SESSION['role'];

if (!isset($_SESSION["login"]) || $role != 'GOD') {
    echo json_encode([]);
    header("Location: index.php");
    exit;
}

$id = $_GET["edit"];
$role = $_GET["role"];


if (editrole($id, $role) > 0) {
    echo "
    <script>
        alert('role user diubah');
        document.location.href = 'admin.php';
    </script>
    ";
} else {
    echo "
        <script>
            alert('role user gagal diubah');
            document.location.href = 'admin.php';
        </script>
    ";
}
