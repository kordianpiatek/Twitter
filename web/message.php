<?php
require_once '../src/Connection.php';
require_once '../src/Users.php';
require_once '../src/Message.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
}?>
<div>
    <div><a href="../index.php">Home</a></div>
    <div><a href='messages.php'>Messages</a></div>
    <div><a href="user.php">See user</a></div>
    <div><a href="userEdition.php">Edit your profile</a></div>
    <div><a href='login.php?status=logout'>Log Out</a></div>
</div>
<?php

if (isset($_GET['id'])) {
    $messageId = $_GET['id'];

    $message = Message::loadMessageById($conn, $messageId);
    $msgSenderId = $message->getMsgSenderId();
    $msgReceiverId = $message->getMsgReceiverId();
    $creationDate = $message->getCreationDate();
    $text = $message->getText();
    $readed = $message->getReaded();

    $sender = Users::loadUserById($conn, $msgSenderId);
    $receiver = Users::loadUserById($conn, $msgReceiverId);
    $msgSenderName = $sender->getUsername();
    $msgReceiverName = $receiver->getUsername();

    if($msgReceiverId == $_SESSION['user']){
        $message->setReaded();
        $message->saveToDB($conn);
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Message</title>
    </head>
    <body>
    <div>
        <div>
            <?php
            echo "<div>";
            echo " MESSAGE ";
            echo "<a href='user.php?searchUsername=$msgSenderName'>$msgSenderName</a><br>";
            echo "----> <a href='user.php?searchUsername=$msgReceiverName'>$msgReceiverName</a><br>";
            echo "Date- $creationDate<br>";
            echo "</div>";
            echo "<div>$text</div><br>";
            ?>
        </div>
    </div>
    </body>
    </html>
    <?php
} else {
    echo "Message doesn't exist";
}