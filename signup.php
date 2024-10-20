<?php
require 'config.php';

if (isset($_POST["register"])) {
  if (registrasi($_POST, $_FILES) > 0) {
    echo "
            <script>
                alert('user baru berhasil ditambahkan');
                document.location.href = 'login.php';
            </script>
        ";
  } else {
    echo "
            <script>
                alert('user baru gagal ditambahkan');
            </script>
        ";
    echo mysqli_error($conn);
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rafnet.online</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
  <link rel="icon" type="image/x-icon" href="img/icon.png">
  <link rel="stylesheet" href="css/signup.css" />
</head>

<body>
  <div class="wrapper">
    <section class="form signup">
      <header><img src="img/icon.png" alt="">Rafnet.online</header>
      <p class="dev">Develop By: <a href="https://www.instagram.com/rafiansya__/" target="_blank">rafiansya__</a> and <a href="https://www.instagram.com/reyrnd._/" target="_blank">rer</a></p>
      <form action="" method="POST" enctype="multipart/form-data">
        <div class="name-details">
          <div class="field">
            <label for="fname">First Name</label>
            <input type="text" placeholder="nama depan" name="fname" id="fname" required />
          </div>
          <div class="field">
            <label for="lname">Last Name</label>
            <input type="text" placeholder="nama belakang" name="lname" id="lname" required />
          </div>
          <div class="field">
            <label for="email">Email</label>
            <input type="email" placeholder="masukkan email" name="email" id="email" required />
          </div>
          <div class="field">
            <label for="password">Password</label>
            <input type="password" placeholder="buat password" name="password" id="password" required />
          </div>
          <div class="field">
            <label for="password2">Konfirmasi password</label>
            <input type="password" placeholder="buat password" name="password2" id="password2" required />
          </div>
          <div class="field">
            <label for="status">Status Anda</label>
            <select name="status" id="status" required>
              <option value="">Pilih</option>
              <option value="Jomblo">Jomblo</option>
              <option value="Sudah Berkeluarga">Sudah Berkeluarga</option>
              <option value="Lagi Cari Pacar">Lagi Cari Pacar</option>
              <option value="Sudah Punya Pacar">Sudah Punya Pacar</option>
              <option value="Tidak Ingin Pacaran">Tidak Ingin Pacaran</option>
            </select>
          </div>
          <div class="field image">
            <label for="profil">Pilih Profil</label>
            <input type="file" name="profil" id="profil" required />
          </div>
          <div class="field button">
            <button type="submit" name="register">Buat Akun</button>
          </div>
        </div>
      </form>
      <div class="link">Sudah Daftar? <a href="login.php">login sekarang</a></div>
    </section>
  </div>
</body>

</html>