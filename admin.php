<?php
session_start();
require 'config.php';

$fnameuser = $_SESSION['fname'];
$lnameuser = $_SESSION['lname'];
$unique_id = $_SESSION['unique_id'];
$role = $_SESSION['role'];
$namauser = $fnameuser . ' ' . $lnameuser;

if (!isset($_SESSION["login"]) || $role != 'GOD') {
    echo json_encode([]);
    header("Location: index.php");
    exit;
}

$account = query("SELECT * FROM users WHERE unique_id = '$unique_id'")[0];

if (isset($_POST['action']) && $_POST['action'] == 'fetch_users') {

    // Query untuk mengambil data user yang berkomunikasi dengan pengguna saat ini
    $users = query("SELECT u.*, 
                       MAX(m.timestamp_pesan) AS last_message_time,
                       COUNT(CASE WHEN m.incoming_msg_id = '$unique_id' AND m.is_read = 0 THEN 1 END) AS unread_count
                FROM users u
                LEFT JOIN messages m 
                  ON (u.unique_id = m.incoming_msg_id OR u.unique_id = m.outgoing_msg_id)
                  AND (m.incoming_msg_id = '$unique_id' OR m.outgoing_msg_id = '$unique_id')
                WHERE u.unique_id != '$unique_id'
                GROUP BY u.unique_id
                ORDER BY last_message_time DESC, u.fname ASC;");


    if ($users) {
        foreach ($users as $user) {
            $data_pesan = query("SELECT * FROM messages
                            WHERE (incoming_msg_id = '{$user['unique_id']}' OR outgoing_msg_id = '{$user['unique_id']}')
                            AND (incoming_msg_id = '$unique_id' OR outgoing_msg_id = '$unique_id')
                            ORDER BY timestamp_pesan DESC LIMIT 1;");

            $editrole = '';
            $deleteuser = '';

            if ($user['role'] == "rakyat") {
                if ($user["user_id"] != 1) {
                    $editrole = '<a href="roleedit.php?edit=' . $user["user_id"] . '&role=GOD" class="button">
                                    <i class="role fas fa-address-card"></i>
                                </a>';
                    $deleteuser = '<a href="deleteuser.php?delete=' . $user["user_id"] . '" class="button">
                                        <i class="delete fas fa-trash"></i>
                                    </a>';
                }
            } else {
                if ($user["user_id"] != 1) {
                    $editrole = '<a href="roleedit.php?edit=' . $user["user_id"] . '&role=rakyat" class="button">
                                    <i class="rolegod fas fa-address-card"></i>
                                </a>';
                    $deleteuser = '<a href="deleteuser.php?delete=' . $user["user_id"] . '" class="button">
                                        <i class="delete fas fa-trash"></i>
                                    </a>';
                }
            };

            echo '<div class="temancontent">
                    <div class="content">
                        <img src="img/user/' . htmlspecialchars($user['profil']) . '" alt="">
                        <div class="details">
                        <span class="temanitem">' . htmlspecialchars($user['fname']) . ' ' . htmlspecialchars($user['lname']) . '</span>
                        </div>
                    </div>
                    <div class="action">
                        ' . $editrole . '
                        ' . $deleteuser . '
                    </div>
                  </div>';
        }
    } else {
        echo '<p class="no-user">Belum ada user kocag...</p>';
    }

    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rafnet.online</title>
    <link rel="stylesheet" href="css/admin.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css" />
    <link rel="icon" type="image/x-icon" href="img/icon.png">
</head>

<body>
    <div class="wrapper">
        <section class="users">
            <div class="top">
                <img src="img/icon.png" alt="">
                <h1>Rafnet.online</h1>
            </div>
            <header>
                <div class="content">
                    <img src="img/user/<?= $account["profil"]; ?>" alt="">
                    <div class="details">
                        <span><?= $namauser ?></span>
                        <p><?= $account["status"]; ?></p>
                    </div>
                </div>
                <div class="action">
                    <a href="index.php"><i class="admin fas fa-user"></i></a>
                    <a href="logout.php?logout=<?= $account["unique_id"]; ?>" class="logout">Keluar</a>
                </div>
            </header>
            <div class="search">
                <span class="text">Cari admin untuk memulai chat</span>
                <input type="text" name="" id="findteman" placeholder="masukkan nama untuk mencari teman..." onkeyup="searchteman()">
                <button><i class="fas fa-search"></i></button>
            </div>
            <div class="users-list">

            </div>
            <div id="pagenotfound-teman">
                <p>Gaada kocag</p>
            </div>
            <div class="bottom">
                <h1>Enjoy Sobat</h1>
            </div>
        </section>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="javascript/admin.js"></script>
</body>

</html>