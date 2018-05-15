<?php

class Message
{
    private $id;
    private $msgSenderId;
    private $msgReceiverId;
    private $creationDate;
    private $text;
    private $readed;

    public function __construct()
    {
        $this->id = -1;
        $this->msgSenderId = 0;
        $this->msgReceiverId = 0;
        $this->creationDate = '';
        $this->text = '';
        $this->readed = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setMsgSenderId($msgSenderId)
    {
        $this->msgSenderId = $msgSenderId;
    }

    public function getMsgSenderId()
    {
        return $this->msgSenderId;
    }

    public function setMsgReceiverId($msgReceiverId)
    {
        $this->msgReceiverId = $msgReceiverId;
    }

    public function getMsgReceiverId()
    {
        return $this->msgReceiverId;
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

    public function setReaded()
    {
        $this->readed = 1;
    }

    public function getReaded()
    {
        return $this->readed;
    }

    static public function loadMessageById(PDO $conn, $id)
    {
        $stmt = $conn->prepare('SELECT * FROM Messages WHERE id=:id');
        $result = $stmt->execute(['id' => $id]);
        if ($result === true && $stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $loadedMessage = new Message();
            $loadedMessage->id = $row['id'];
            $loadedMessage->msgSenderId = $row['msgSenderId'];
            $loadedMessage->msgReceiverId = $row['msgReceiverId'];
            $loadedMessage->text = $row['text'];
            $loadedMessage->creationDate = $row['creationDate'];
            $loadedMessage->readed = $row['readed'];
            return $loadedMessage;
        }
        return null;
    }

    static public function loadAllMessagesByMsgSenderId(PDO $conn, $msgSenderId)
    {
        $ret = [];
        $stmt = $conn->prepare('SELECT * FROM Messages WHERE msgSenderId=:msgSenderId ORDER BY creationDate DESC');
        $result = $stmt->execute(['msgSenderId' => $msgSenderId]);
        if ($result !== false && $stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $loadedMessage = new Message();
                $loadedMessage->id = $row['id'];
                $loadedMessage->msgSenderId = $row['msgSenderId'];
                $loadedMessage->msgReceiverId = $row['msgReceiverId'];
                $loadedMessage->text = $row['text'];
                $loadedMessage->creationDate = $row['creationDate'];
                $loadedMessage->readed = $row['readed'];
                $ret[] = $loadedMessage;
            }
            return $ret;
        }
        return null;
    }

    static public function loadAllMessagesByMsgReceiverId(PDO $conn, $msgReceiverId)
    {
        $ret = [];
        $stmt = $conn->prepare('SELECT * FROM Messages WHERE msgReceiverId=:msgReceiverId ORDER BY creationDate DESC');
        $result = $stmt->execute(['msgReceiverId' => $msgReceiverId]);
        if ($result !== false && $stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $loadedMessage = new Message();
                $loadedMessage->id = $row['id'];
                $loadedMessage->msgSenderId = $row['msgSenderId'];
                $loadedMessage->msgReceiverId = $row['msgReceiverId'];
                $loadedMessage->text = $row['text'];
                $loadedMessage->creationDate = $row['creationDate'];
                $loadedMessage->readed = $row['readed'];
                $ret[] = $loadedMessage;
            }
            return $ret;
        }
        return null;
    }

    public function saveToDB(PDO $conn)
    {
        if ($this->id == -1) {

            $sql = 'INSERT INTO Messages(msgSenderId, msgReceiverId, text, creationDate, readed) VALUES(:msgSenderId, :msgReceiverId, :text, :creationDate, :readed)';
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute(['msgSenderId' => $this->msgSenderId, 'msgReceiverId' => $this->msgReceiverId, 'text' => $this->text, 'creationDate' => $this->creationDate, 'readed' => $this->readed]);
            if ($result !== false) {
                $this->id = $conn->lastInsertId();
                return true;
            }
        } else {
            $stmt = $conn->prepare('UPDATE Messages SET readed=:readed WHERE  id=:id ');
            $result = $stmt->execute(['readed' => $this->readed, 'id' => $this->id]);
            if ($result === true) {
                return true;
            }
            return false;
        }
    }
}