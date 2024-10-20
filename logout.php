<?php
session_start();
require 'config.php';

if (isset($_GET['logout'])) {
    $logout = mysqli_real_escape_string($conn, $_GET['logout']);
    $query = "UPDATE users SET sesi = 'Offline' WHERE unique_id = $logout";
    mysqli_query($conn, $query);
}

$_SESSION = [];
session_unset();
session_destroy();

setcookie('number', '', time() - 600000);
setcookie('key', '', time() - 600000);

header("Location: login.php");
exit;
?>