<?php

$conn = mysqli_connect("localhost", "root", "", "appchat");

function query($query)
{
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function registrasi($data, $file)
{
    global $conn;

    $fname = stripslashes($data["fname"]);
    $lname = stripslashes($data["lname"]);
    $email = strtolower(stripslashes($data["email"]));
    $status = htmlspecialchars($data["status"]);
    $password = mysqli_real_escape_string($conn, $data["password"]);
    $password2 = mysqli_real_escape_string($conn, $data["password2"]);

    $profil = uploadprofil($file);

    if (uploadprofil($file)) {
        $sesi = "Online";
        $uniqueid = rand(time(), 10000000);
    }

    if (!$profil) {
        return false;
    }

    $result = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");

    if (mysqli_fetch_assoc($result)) {
        echo "
            <script>
                alert('email sudah dipakai!');
            </script>
        ";
        return false;
    }

    if ($password !== $password2) {
        echo "
            <script>
                alert('password tidak sesuai!');
            </script>
        ";
        return false;
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users VALUES ('', '$uniqueid', '$fname', '$lname', '$email', '$password', '$profil', '$status', '$sesi', 'rakyat', NOW())";

    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

function uploadprofil($file)
{

    $profil = $file["profil"]["name"];
    $simpanprofil = $file["profil"]["tmp_name"];
    $ukuranprofil = $file['profil']['size'];
    $error = $file['profil']['error'];

    // mengecek apakahada file diupload
    if ($error === 4) {
        echo "<script>
                alert('pilih gambar terlebih dahulu');
              </script>";
        return false;
    }

    // mengecek tipe file
    $allowed = ['jpg', 'jpeg', 'png'];
    $ekstensi = explode('.', $profil);
    $ekstensi = strtolower(end($ekstensi));

    if (!in_array($ekstensi, $allowed)) {
        echo "<script>
                alert('Format profil salah!');
              </script>";
        return false;
    }

    if ($ukuranprofil > 1000000) {
        echo "<script>
                alert('Ukuran profil terlalu besar!');
              </script>";
        return false;
    }

    $namaprofilbaru = uniqid();
    $namaprofilbaru .= '.';
    $namaprofilbaru .= $ekstensi;

    move_uploaded_file($simpanprofil, "img/user/" . $namaprofilbaru);

    // if(move_uploaded_file($simpanprofil, "img/user/" . $namaprofilbaru)) {
    //     $status = "Active now";
    //     $uniqueid = rand(time(), 10000000);
    // }


    return $namaprofilbaru;
}

function kirimpesan($data)
{
    global $conn;
    // $id = $sesi['unique_id'];

    $incomingid = mysqli_real_escape_string($conn, $data["incoming_id"]);
    $outgoingid = mysqli_real_escape_string($conn, $data["outgoing_id"]);
    $message = mysqli_real_escape_string($conn, $data["message"]);

    $query = "INSERT INTO messages VALUES ('', '$incomingid', '$outgoingid', '$message', '', NOW())";

    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

function deleteuser($id)
{
    global $conn;
    mysqli_query($conn, "DELETE FROM users WHERE user_id = $id");

    return mysqli_affected_rows($conn);
}

function editrole($id, $role)
{
    global $conn;
    mysqli_query($conn, "UPDATE users SET role='$role' WHERE user_id = $id");

    return mysqli_affected_rows($conn);
}
?>