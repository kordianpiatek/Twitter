<?php
require_once '../src/Connection.php';
require_once '../src/Users.php';

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: web/login.php");
}

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $userProfileId = Users::loadUserById($conn, $_SESSION['user']);
    $userProfileName = $userProfileId->getUsername();
    $userProfileEmail = $userProfileId->getEmail();

    if(isset($_POST['save'])){
        $oldPassword = trim($_POST['oldPassword']);
        $newPassword = trim($_POST['newPassword']);
        $newUsername = trim($_POST['newUsername']);
        $newEmail = trim($_POST['newEmail']);

        if (password_verify($oldPassword, $userProfileId->getPassword())) {
            $userProfileId->setPassword($newPassword);
            $userProfileId->saveToDB($conn);
            if (!$newUsername or !$newEmail or !$newPassword) {
                echo '<p>Wrong data</p>';
                exit;
            } elseif ($newEmail != $userProfileEmail) {
                $stmt = $conn->prepare('SELECT * FROM Users WHERE email=:email');
                $result = $stmt->execute(['email' => $newEmail]);

                if ($result === true && $stmt->rowCount() > 0) {
                    echo "<p>Email has been taken</p>";
                    exit;
                }
            }else {
                $userProfileId->setUsername($newUsername);
                $userProfileId->setEmail($newEmail);
                $saved = $userProfileId->saveToDB($conn);
            }

        } else {
            echo '<p>Wrong password</p>';
            exit;
        }
    }
    if (isset($_POST['delete'])) {
        $deleteUser = $userProfileId->delete($conn);
        if ($deleteUser){
            unset($_SESSION['user']);
            header("Location: login.php");
        }
    }
}



?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit user</title>
</head>
<body>
<div>
    <div><a href="../index.php">Home</a></div>
    <div><a href='messages.php'>Messages</a></div>
    <div><a href="user.php">See user</a></div>
    <div><a href="userEdition.php">Edit your profile</a></div>
    <div><a href='login.php?status=logout'>Log Out</a></div>
</div>
    <div>
        <b>Change your Data  </b>
        <form method="POST" action="">
            <p>
                <label>Username:
                <input name="newUsername" type="text" value="newUsername"></label>
            </p>
            <p>
                <label>Email:
                    <input name="newEmail" type="email" value="newEmail"></label>
            </p>
            <p><label>Password:
                <input name="oldPassword" type="password"></label>
            </p>
            <p><label>New password:
                    <input name="newPassword" type="password"></label>
            </p>
            <p>
                <label><input type="submit" name="save" value="Save new data"></label>
            </p>
        </form>
    </div>
    <div >
        <b>Delete your account:</b>
        <form method='POST' action=''>
            <label><input type="submit" name="delete" value="Delete your profile">Delete</label>
        </form>
    </div>
</body>
</html>
