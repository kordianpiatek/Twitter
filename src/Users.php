<?php

class Users
{

    private $id;
    private $username;
    private $hashPass;
    private $email;

    public function __construct()
    {
        $this->id = -1;
        $this->username = '';
        $this->email = '';
        $this->hashPass = '';
    }

    public function getId()
    {
        return $this->id;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }
    public function getUsername()
    {
        return $this->username;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setPassword($password)
    {
        $newHashPass = password_hash($password, PASSWORD_BCRYPT);
        $this->hashPass = $newHashPass;
    }

    public function getPassword()
    {
        return $this->hashPass;
    }

    public function saveToDB(PDO $conn)
    {
        if ($this->id == -1) {

            $sql = 'INSERT INTO Users(username, email, hashPass) VALUES(:username, :email, :hashPass)';
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute(['username' => $this->username, 'email' => $this->email, 'hashPass' => $this->hashPass]);
            if ($result !== false) {
                $this->id = $conn->lastInsertId();
                return true;
            }
        } else {
            $stmt = $conn->prepare('UPDATE Users SET email=:email, username=:username, hashPass=:hashPass WHERE  id=:id ');
            $result = $stmt->execute(['email' => $this->email, 'username' => $this->username, 'hashPass' => $this->hashPass, 'id' => $this->id]);
            if ($result === true) {
                return true;
            }
        }
        return false;
    }

    static public function loadUserById(PDO $conn, $id)
    {
        $stmt = $conn->prepare('SELECT * FROM Users WHERE id=:id');
        $result = $stmt->execute(['id' => $id]);
        if ($result === true && $stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $loadedUser = new Users();
            $loadedUser->id = $row['id'];
            $loadedUser->username = $row['username'];
            $loadedUser->hashPass = $row['hashPass'];
            $loadedUser->email = $row['email'];
            return $loadedUser;
        }
        return null;
    }

    static public function loadUserByEmail(PDO $conn, $email)
    {
        $stmt = $conn->prepare('SELECT * FROM Users WHERE email=:email');
        $result = $stmt->execute(['email' => $email]);
        if ($result === true && $stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $loadedUser = new Users();
            $loadedUser->id = $row['id'];
            $loadedUser->username = $row['username'];
            $loadedUser->hashPass = $row['hashPass'];
            $loadedUser->email = $row['email'];
            return $loadedUser;
        }
        return null;
    }

    static public function loadUserByUsername(PDO $conn, $username)
    {
        $stmt = $conn->prepare('SELECT * FROM Users WHERE username=:username');
        $result = $stmt->execute(['username' => $username]);
        if ($result === true && $stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $loadedUser = new Users();
            $loadedUser->id = $row['id'];
            $loadedUser->username = $row['username'];
            $loadedUser->hashPass = $row['hashPass'];
            $loadedUser->email = $row['email'];
            return $loadedUser;
        }
        return null;
    }

    static public function loadAllUsers(PDO $conn)
    {
        $ret = [];
        $sql = "SELECT * FROM Users";
        $result = $conn->query($sql);
        if ($result !== false && $result->rowCount() > 0) {
            foreach ($result as $row) {
                $loadedUser = new Users();
                $loadedUser->id = $row['id'];
                $loadedUser->username = $row['username'];
                $loadedUser->hashPass = $row['hashPass'];
                $loadedUser->email = $row['email'];
                $ret[] = $loadedUser;
            }
        }
        return $ret;
    }

    public function delete(PDO $conn)
    {
        if ($this->id != -1) {
            $stmt = $conn->prepare('DELETE FROM Users WHERE id=:id');
            $result = $stmt->execute(['id' => $this->id]);
            if ($result === true) {
                return true;
            }
            return false;
        }
        return true;
    }
}