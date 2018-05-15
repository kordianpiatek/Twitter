<?php

require_once '../src/Connection.php';
require_once '../src/Users.php';
require_once '../src/Tweet.php';
require_once '../src/Comment.php';

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: web/login.php");
}

?><div class="navbar">
    <div><a href="../index.php">Home</a></div>
    <div><a href='messages.php'>Messages</a></div>
    <div><a href="user.php">See user</a></div>
    <div><a href="userEdition.php">Edit your profile</a></div>
    <div><a href='login.php?status=logout'>Log Out</a></div>
</div>
    <div>
    <form class="userSearch" method="get" action="">
        <input type="text" name="searchUsername"><br>
        <button type="submit" value="search">search</button>
    </form>
</div>
<?php
if(isset($_GET['searchUsername'])){
    $username = $_GET['searchUsername'];
    $user = Users::loadUserByUsername($conn, $username);
    $userId = $user->getId();
    $tweetsByUser = Tweet::loadAllTweetsByUserId($conn, $userId);
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>User</title>
    </head>
    <body>
    <div>
        <?php
        echo "<div>$username</div>";
        echo "<div>
        <form method='post' action=''>
        <input type='text' name='msgText'><br>
            <button type='submit' value='$userId'>Send message</button>
        </form>
    </div>";
        if ($tweetsByUser) {
            echo "<div><b>This user tweets</b></div>";
            foreach ($tweetsByUser as $v) {
                $tweetId = $v->getId();
                $creationDate = $v->getCreationDate();
                $text = $v->getText();
                $comments = Comment::loadAllCommentsByTweetId($conn, $tweetId);
                echo "<div>";
                echo "<a href='tweet.php?id=$tweetId'>Show tweet</a>";
                echo "//// $creationDate<br>";
                echo "$text<br>";
                foreach($comments as $comment)
                {
                    $id = $comment->getId();
                    $user = $comment->getUserId();
                    $creationDate = $comment->getCreationDate();
                    $text = $comment->getText();
                    $commentUser = Users::loadUserById($conn, $user);
                    $commentUserName = $commentUser->getUsername();
                    echo "<div>";
                    echo $commentUserName . "<br>";
                    echo $creationDate . "<br>";
                    echo $text . "<br><br>";
                    echo "</div>";

                }
                echo "</div>";
            }
        } else {
            echo "<div>This user didn't post any tweet</div>";
        }
        ?>
    </div>
    </body>
    </html>
    <?php
}
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if (!empty($_POST['msgText'])) {
        $msgSenderId = $_SESSION['user'];
        $msgReceiverId = $userId;
        $text = $_POST['msgText'];
        $creationDate = date('Y-m-d H:i:s', time());

        $message = new Message();

        $message->setMsgSenderId($msgSenderId);
        $message->setMsgReceiverId($msgReceiverId);
        $message->setCreationDate($creationDate);
        $message->setText($text);
        $message->saveToDB($conn);

        if ($message->getId() != -1) {
            echo "Message sent";
        }
    }
}