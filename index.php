<?php
session_start();
require 'config.php';

$fnameuser = $_SESSION['fname'];
$lnameuser = $_SESSION['lname'];
$unique_id = $_SESSION['unique_id'];
$role = $_SESSION['role'];
$namauser = $fnameuser . ' ' . $lnameuser;

if (!isset($_SESSION["login"]) || !isset($fnameuser) || !isset($lnameuser) || !isset($unique_id) || !isset($role)) {
  echo json_encode([]);
  header("Location: login.php");
  exit;
}

$account = query("SELECT * FROM users WHERE unique_id = '$unique_id'")[0];

if (isset($_POST['action']) && $_POST['action'] == 'fetch_users') {
  // Query untuk mengambil data user yang berkomunikasi dengan pengguna saat ini
  $users = query("SELECT u.*, 
                        MAX(m.timestamp_pesan) AS last_message_time,
                        COUNT(CASE WHEN m.incoming_msg_id = '$unique_id' AND m.is_read = 0 THEN 1 END) AS unread_count,
                        MAX(m.msg_id) AS last_message_id
                 FROM users u
                 LEFT JOIN messages m 
                   ON (u.unique_id = m.incoming_msg_id OR u.unique_id = m.outgoing_msg_id)
                   AND (m.incoming_msg_id = '$unique_id' OR m.outgoing_msg_id = '$unique_id')
                 WHERE u.unique_id != '$unique_id'
                 GROUP BY u.unique_id
                 ORDER BY last_message_time DESC, CASE WHEN u.role = 'GOD' THEN 1 ELSE 2 END, u.fname ASC;");

  $response = '';
  if ($users) {
    foreach ($users as $user) {
      $data_pesan = query("SELECT * FROM messages
                                 WHERE (incoming_msg_id = '{$user['unique_id']}' OR outgoing_msg_id = '{$user['unique_id']}')
                                 AND (incoming_msg_id = '$unique_id' OR outgoing_msg_id = '$unique_id')
                                 ORDER BY timestamp_pesan DESC LIMIT 1;");
      $pesan = '';
      $waktupesan = '';
      $pesandarikita = '';

      if ($data_pesan) {
        $pesan = htmlspecialchars($data_pesan[0]['msg']);
        $waktupesan = htmlspecialchars($data_pesan[0]['timestamp_pesan']);
        $pesandarikita = htmlspecialchars($data_pesan[0]['incoming_msg_id']);
      } else {
        $pesan = 'Belum ada pesan...';
      }

      $you = ($user['unique_id'] == $pesandarikita) ? "Kamu: " : "";
      $offline = ($user['sesi'] == 'Offline') ? "offline" : "";

      $formattedTimestamp = '';
      if ($waktupesan) {
        $dateTime = new DateTime($waktupesan, new DateTimeZone('Asia/Jakarta'));
        $dateTime->setTimezone(new DateTimeZone('Asia/Jakarta'));
        $formattedTimestamp = $dateTime->format('H:i');
      }

      $response .= '<a href="chat.php?chat=' . htmlspecialchars($user['unique_id']) . '" class="temancontent" data-message-id="' . htmlspecialchars($user['last_message_id']) . '">
                            <div class="content">
                                <img src="img/user/' . htmlspecialchars($user['profil']) . '" alt="">
                                <div class="details">
                                    <span class="temanitem">' . htmlspecialchars($user['fname']) . ' ' . htmlspecialchars($user['lname']) . '</span>
                                    <div class="chat-text"><p>' . $you . $pesan . '</p><span>' . $formattedTimestamp . '</span></div>
                                </div>
                            </div>
                            <div class="status-dot ' . $offline . '">
                                <i class="fas fa-circle"></i>
                            </div>
                            ' . ($user['unread_count'] > 0 ? '<div class="notification">' . htmlspecialchars($user['unread_count']) . '</div>' : '') . '
                        </a>';
    }
  } else {
    $response .= '<p class="no-user">Belum ada user kocag...</p>';
  }

  echo $response;
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rafnet.online</title>
  <link rel="stylesheet" href="css/users.css" />
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
          <?php if ($role == "GOD") { ?>
            <a href="admin.php"><i class="admin fas fa-user"></i></a>
          <?php } ?>
          <a href="logout.php?logout=<?= $account["unique_id"]; ?>" class="logout">Keluar</a>
        </div>
      </header>
      <div class="search">
        <span class="text">Cari user untuk memulai chat</span>
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
  <script src="javascript/users.js"></script>
  <script src="javascript/index.js"></script>

</body>

</html>