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

$id = $_GET["chat"];

$users = query("SELECT * FROM users WHERE unique_id = $id")[0];

if (isset($_GET["chat"])) {
    $readchat = "UPDATE messages SET is_read = 1 WHERE incoming_msg_id = '$unique_id' AND outgoing_msg_id = '$id' AND is_read = 0";
    mysqli_query($conn, $readchat);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["action"]) && $_POST["action"] === "send_message") {
        // Mengirim pesan
        if (kirimpesan($_POST) > 0) {
            echo json_encode(['status' => 'success']);
        } else {
            echo mysqli_error($conn);
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
        exit;
    }

    if (isset($_POST["action"]) && $_POST["action"] === "fetch_messages") {
        // Mengambil pesan
        // $outgoing_id = '';
        if ($users["role"] != 'GOD' || ($users["role"] == 'GOD' && $role == "GOD")) {
            $outgoing_id = mysqli_real_escape_string($conn, $_POST['outgoing_id']);
            $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);

            $pesan = query("SELECT * FROM messages
                            LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
                            WHERE (outgoing_msg_id = '$outgoing_id' AND incoming_msg_id = '$incoming_id')
                            OR (outgoing_msg_id = '$incoming_id' AND incoming_msg_id = '$outgoing_id')
                            ORDER BY msg_id ASC");
        }

        if ($users["role"] == 'GOD' && $role == "rakyat") {
            $outgoing_id = $unique_id;

            $pesan = query("SELECT * FROM messages
                            LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
                            WHERE (incoming_msg_id = '$outgoing_id' AND incoming_msg_id = '$id')
                            OR (outgoing_msg_id = '$id' AND incoming_msg_id = '$outgoing_id')
                            ORDER BY msg_id ASC");
        }

        if ($pesan) {
            foreach ($pesan as $pesans) {
                // Format timestamp
                $formattedTimestamp = '';

                $waktupesan = htmlspecialchars($pesans['timestamp_pesan']);
                if ($waktupesan) {
                    $dateTime = new DateTime($waktupesan, new DateTimeZone('Asia/Jakarta'));
                    $dateTime->setTimezone(new DateTimeZone('Asia/Jakarta'));

                    // Format waktu yang telah diubah
                    $formattedTimestamp = $dateTime->format('H:i');
                }

                if ($pesans['outgoing_msg_id'] === $outgoing_id) {
                    echo '<div class="chat outgoing">
                            <div class="details">
                                <p>' . $pesans['msg'] . '<span>' . $formattedTimestamp . '</span></p>
                            </div>
                          </div>
                         ';
                } else {
                    echo '<div class="chat incoming">
                            <img src="img/user/' . $pesans['profil'] . '" alt="">
                                <div class="details">
                                    <p>' . $pesans['msg'] . '<span>' . $formattedTimestamp . '</span></p>
                            </div>
                          </div>
                         ';
                }
            }
        } else {
            echo '';
        }
        exit;
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rafnet.online</title>
    <link rel="stylesheet" href="css/chat.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css" />
    <link rel="icon" type="image/x-icon" href="img/icon.png">
</head>

<body>
    <div class="wrapper">
        <section class="chat-area">
            <header>
                <a href="index.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
                <img src="img/user/<?= $users["profil"]; ?>" alt="">
                <div class="details">
                    <span><?= $users["fname"] . ' ' . $users["lname"] ?></span>
                    <p><?= $users["status"]; ?></p>
                </div>
            </header>
            <div class="chat-box">

            </div>
            <?php if ($users["role"] != 'GOD' || ($users["role"] == 'GOD' && $role == "GOD")) { ?>
                <form action="" method="post" id="messageForm" class="typing-area" enctype="multipart/form-data">
                    <input type="hidden" name="outgoing_id" value="<?= $unique_id ?>">
                    <input type="hidden" name="incoming_id" value="<?= $users["unique_id"]; ?>">
                    <input type="text" name="message" id="message" placeholder="Tulisakan pesan kamu " required />
                    <button type="submit" name="kirimpesanbutton"><i class="fab fa-telegram-plane"></i></button>
                </form>
            <?php } ?>
            <?php if ($users["role"] == 'GOD' && $role == "rakyat") { ?>
                <div class="god-only">
                    <p>never complain to "GOD"🤫</p>
                </div>
            <?php } ?>
        </section>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="javascript/chat.js"></script>
    </script>
</body>

</html>