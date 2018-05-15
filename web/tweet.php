<?php
require_once '../src/Connection.php';
require_once '../src/Users.php';
require_once '../src/Tweet.php';
require_once '../src/Comment.php';
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: web/login.php");
}

if (isset($_GET['tweetId'])) {
    $tweetId = $_GET['tweetId'];

    $tweet = Tweet::loadTweetById($conn, $tweetId);
    $tweetUserId = $tweet->getUserId();
    $tweetText = $tweet->getText();
    $tweetCreationDate = $tweet->getCreationDate();
    $comments = Comment::loadAllCommentsByTweetId($conn, $tweetId);
    $tweetUser = Users::loadUserById($conn, $tweetUserId);
    $tweetUserName = $tweetUser->getUsername();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newComment = $_POST['comment'];
        if(!empty($newComment) && !trim($newComment)){
            $creationDate = date('Y-m-d H:i:s', time());
            $comment = new Comment();
            $comment->setUserId($_SESSION['user']);
            $comment->setTweetId($tweetId);
            $comment->setText($newComment);
            $comment->setCreationDate($creationDate);
            $comment->saveToDB($conn);
        }
    }

?>

<!DOCTYPE html>
<html>
<head>
    <title>Tweet</title>
</head>
<body>
<div>
    <div class="navbar">
        <div><a href="../index.php">Home</a></div>
        <div><a href='messages.php'>Messages</a></div>
        <div><a href="user.php">See user</a></div>
        <div><a href="userEdition.php">Edit your profile</a></div>
        <div><a href='login.php?status=logout'>Log Out</a></div>
    </div>
    <div>
        <?php
        echo "<div>";
        echo $tweetUserName ."<br>";
        echo "<span>$tweetText</span><br>";
        echo $tweetCreationDate . "<br>";
        echo "</div>";

        if ($comments) {
            echo "<div>Comments</div>";
            foreach ($comments as $v) {
                $id = $v->getId();
                $userId = $v->getUserId();
                $creationDate = $v->getCreationDate();
                $text = $v->getText();
                $commentUser = Users::loadUserById($conn, $userId);
                $commentUserName = $commentUser->getUsername();
                echo "<div>";
                echo $commentUserName . "<br>";
                echo $creationDate . "<br>";
                echo $text . "<br><br>";
                echo "</div>";
            }
        } else {
            echo "<div>This tweet have no comments yet</div>";
        }
        ?>
    </div>
    <div>
        <form action="" method="post" role="form">
            <textarea id="text" name="comment" placeholder="Write new comment..."></textarea>
            <br>
            <input type="submit" value="Comment">
        </form>
    </div>
</div>
</body>
</html>
    <?php
} else {
    echo "Tweet doesn't exist";
}