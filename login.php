<?php
session_start();
require 'config.php';


if (isset($_COOKIE['number']) && isset($_COOKIE['key'])) {
  $id = $_COOKIE['number'];
  $key = $_COOKIE['key'];

  //ambil username berdasarkan id
  $result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id");

  $row = mysqli_fetch_assoc($result);

  //cek cookie dan username
  if ($key === hash('sha256', $row['email'])) {
    $_SESSION['login'] = true;
  }
}

if (isset($_SESSION["login"])) {
  $_SESSION['user_id'] = $row['user_id'];
  $_SESSION['fname'] = $row['fname'];
  $_SESSION['lname'] = $row['lname'];
  $_SESSION['unique_id'] = $row['unique_id'];
  $_SESSION['role'] = $row['role'];
  header("Location: index.php");
  exit;
}

if (isset($_POST["login"])) {
  $email = $_POST["email"];
  $password = $_POST["password"];

  $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");

  if (mysqli_num_rows($result) === 1) {

    $row = mysqli_fetch_assoc($result);

    if (password_verify($password, $row["password"])) {

      $query = mysqli_query($conn, "UPDATE users SET sesi = 'Online' WHERE email = '$email'");
      //set session
      $_SESSION["login"] = true;
      $_SESSION['user_id'] = $row['user_id'];
      $_SESSION['fname'] = $row['fname'];
      $_SESSION['lname'] = $row['lname'];
      $_SESSION['unique_id'] = $row['unique_id'];
      $_SESSION['role'] = $row['role'];

      // set cookies
      setcookie('number', $row['user_id'], time() + 600000);
      setcookie('key', hash('sha256', $row['email']), time() + 600000);

      header("Location: index.php");
      exit;
    }
  }

  $error = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rafnet.online</title>
  <link rel="stylesheet" href="css/login.css" />
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />
  <link rel="icon" type="image/x-icon" href="img/icon.png">
</head>

<body oncopy="return false;" oncut="return false;" onpaste="return false;">
  <div class="wrapper">
    <div class="info">
      <div class="infowrapper">
        <div class="top">
          <h1><img src="img/icon.png" alt="">Rafnet.online</h1>
          <span class="close material-symbols-rounded">close</span>
        </div>
        <p class="desc"><span>Raffnet.Online</span> merupakan website chat secara realtime, memungkinkan kita untuk bisa berkomunikasi kepada seluruh pengguna tampa harus saling mengikuti ataupun saling save.</p>
        <div class="desc2">
          <span>Developer</span>
          <p>@rafiansya_ & rer</p>
        </div>
        <div class="bottom">Sekian</div>
      </div>
    </div>
    <section class="form">
      <header><img src="img/icon.png" alt="">Rafnet.online</header>
      <p class="dev">Develop By: <a href="https://www.instagram.com/rafiansya__/" target="_blank">rafiansya__</a> and <a href="https://www.instagram.com/reyrnd._/" target="_blank">rer</a></p>
      <form action="" method="POST" enctype="multipart/form-data">
        <div class="field input">
          <label for="email">Email</label>
          <input type="email" placeholder="masukkan email" name="email" id="email" required />
        </div>
        <div class="field input">
          <label for="password">Password</label>
          <input type="password" placeholder="masukkan password" name="password" id="password" required />
        </div>
        <div class="field button">
          <button type="submit" name="login">Lanjut chattingan</button>
        </div>
      </form>
      <div class="link">Belum Daftar? <a href="signup.php">Daftar sekarang</a></div>
    </section>
  </div>
  <?php if (isset($error)) : ?>
    <div class="toast">
      <div class="toast-content">
        <i class="uil uil-exclamation-triangle"></i>
        <div class="message">
          <p class="text">Tidak bisa login !!</p>
          <p class="text">username/password salah</p>
        </div>
      </div>
      <i class="uil uil-times close"></i>
      <div class="progress"></div>
    </div>
  <?php endif; ?>
  <script src="javascript/login.js"></script>
  <script src="javascript/index.js"></script>
</body>

</html>