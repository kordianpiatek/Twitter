<?php
require_once '../src/Connection.php';
require_once '../src/Users.php';
require_once '../src/Message.php';
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: web/login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Messages</title>
</head>
<body>
<div>
    <div>
        <div><a href="../index.php">Home</a></div>
        <div><a href='messages.php'>Messages</a></div>
        <div><a href="user.php">See user</a></div>
        <div><a href="userEdition.php">Edit your profile</a></div>
        <div><a href='login.php?status=logout'>Log Out</a></div>
    </div>

    <div>
        <div><b>Messages</b></div>

        <div>
            <b>Messages received</b>
        </div>
            <?php
            $userId = Users::loadUserById($conn, $_SESSION['user']);
            $username = $userId->getUsername();
            $receivedMessages = Message::loadAllMessagesByMsgReceiverId($conn, $_SESSION['user']);
            $sentMessages = Message::loadAllMessagesByMsgSenderId($conn, $_SESSION['user']);
            if ($receivedMessages) {
                foreach ($receivedMessages as $v) {
                    $messageId = $v->getId();
                    $creationDate = $v->getCreationDate();
                    $text = $v->getText();
                    $msgSenderId = $v->getMsgSenderId();
                    $readed = $v->getReaded();
                    $sender = Users::loadUserById($conn, $msgSenderId);
                    $senderName = $sender->getUsername();
                    echo "<div>";
                    if($readed = 0){
                        echo "<b><a href='message.php?id=$messageId' >See this message</a></b><br>";
                    }elseif($readed = 1){
                        echo "<a href='message.php?id=$messageId'>See this message</a><br>";
                    }
                    echo $senderName . "<br>";
                    echo $creationDate  . "<br>";
                    if($text <= 30 ){
                        echo $text;
                    }else{
                        $shortText = substr($text,0,30);
                        echo $shortText . "....";
                    }
                    echo "</div>";

                }
            } else {
                echo "No received messages";
            }
            ?>
        <div>
            <b>Messages Sent</b>
        </div>
        <?php
        if ($sentMessages) {
            foreach ($sentMessages as $v) {
                $messageId = $v->getId();
                $creationDate = $v->getCreationDate();
                $text = $v->getText();
                $msgReceiverId = $v->getMsgReceiverId();
                $receiver = Users::loadUserById($conn, $msgReceiverId);
                $receiverName = $receiver->getUsername();
                echo "<div>";
                echo "<a href='message.php?id=$messageId' >See this message</a><br>";
                echo $receiverName . "<br>";
                echo $creationDate . "<br>";
                if($text <= 30 ){
                    echo $text;
                }else{
                    $shortText = substr($text,0,30);
                    echo $shortText . "....";
                }
                echo "</div>";



            }
        } else {
            echo "No sent messages";
        }
        ?>
    </div>
</div>
</body>
</html>
