<?php
require_once 'src/Connection.php';
require_once 'src/Users.php';
require_once 'src/Tweet.php';
session_start();


if (!isset($_SESSION['user'])) {
    header("Location: web/login.php");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!empty($_POST['text']) && !trim($_POST['text'])) {
        $userId = $_SESSION['user'];
        $text = $_POST['text'];
        $creationDate = date('Y-m-d H:i:s', time());
        $tweet = new Tweet();
        $tweet->setUserId($userId);
        $tweet->setText($text);
        $tweet->setCreationDate($creationDate);
        $tweet->saveToDB($conn);
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Twitter</title>
</head>
<body>
<div>
    <div class="navbar">
        <div><a href="index.php">Home</a></div>
        <div><a href='web/messages.php'>Messages</a></div>
        <div><a href="web/user.php">See user</a></div>
        <div><a href="web/userEdition.php">Edit your profile</a></div>
        <div><a href='web/login.php?status=logout'>Log Out</a></div>
    </div>

    <div>
        <div>
            <form action="" method="post">

                <textarea name="text" placeholder="Say something ..."></textarea>
                <br>

                <input type="submit" value="tweet">
            </form>
        </div>
        <?php
        $tweets= [];
        $tweets = Tweet::loadAllTweets($conn);
        foreach ($tweets as $v) {
            $tweetId = $v ->getId();
            $userId = $v ->getUserId();
            $tweetText = $v ->getText();
            $creationDate = $v ->getCreationDate();
            $loadedUser = Users::loadUserById($conn, $userId);
            $username = $loadedUser ->getUsername();
            echo "<div>";
            echo "<p>$username</p><br>";
            echo "$creationDate<br>";
            echo "<a href=\"web/tweet.php?tweetId=$tweetId\">$tweetText</a><br><br>";
            echo "</div>";
        }
        ?>
    </div>
</div>
</body>
</html>