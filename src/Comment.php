<?php

class Comment
{
    private $id;
    private $userId;
    private $tweetId;
    private $creationDate;
    private $text;

    public function __construct()
    {
        $this->id = -1;
        $this->userId = 0;
        $this->tweetId = 0;
        $this->creationDate = '';
        $this->text = '';
    }

    public function getId()
    {
        return $this->id;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setTweetId($tweetId)
    {
        $this->tweetId = $tweetId;
    }

    public function getTweetId()
    {
        return $this->tweetId;
    }

    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    public function getCreationDate()
    {
        return $this->creationDate;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getText()
    {
        return $this->text;
    }

    static public function loadCommentById(PDO $conn, $id)
    {
        $stmt = $conn->prepare('SELECT * FROM Comments WHERE id=:id');
        $result = $stmt->execute(['id' => $id]);
        if ($result === true && $stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $loadedComment = new Comment();
            $loadedComment->id = $row['id'];
            $loadedComment->userId = $row['user_id'];
            $loadedComment->tweetId = $row['tweet_id'];
            $loadedComment->text = $row['text'];
            $loadedComment->creationDate = $row['creationDate'];
            return $loadedComment;
        }
        return null;
    }

    static public function loadAllCommentsByTweetId(PDO $conn, $tweetId)
    {
        $ret = [];
        $stmt = $conn->prepare('SELECT * FROM Comments WHERE tweet_id=:tweet_id ORDER BY creationDate DESC');
        $result = $stmt->execute(['tweet_id' => $tweetId]);
        if ($result !== false && $stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $loadedComment = new Comment();
                $loadedComment->id = $row['id'];
                $loadedComment->userId = $row['user_id'];
                $loadedComment->tweetId = $row['tweet_id'];
                $loadedComment->text = $row['text'];
                $loadedComment->creationDate = $row['creationDate'];
                $ret[] = $loadedComment;
            }
            return $ret;
        }
        return null;
    }

    public function saveToDB(PDO $conn)
    {
        if ($this->id == -1) {

            $sql = 'INSERT INTO Comments(user_id, tweet_id, text, creationDate) VALUES(:user_id, :tweet_id, :text, :creationDate)';
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute(['user_id' => $this->userId, 'tweet_id' => $this->tweetId, 'text' => $this->text, 'creationDate' => $this->creationDate]);
            if ($result !== false) {
                $this->id = $conn->lastInsertId();
                return true;
            }
        }
        return false;
    }
}