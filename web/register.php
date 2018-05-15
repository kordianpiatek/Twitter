<?php

require_once '../src/Connection.php';
require_once '../src/Users.php';
session_start();

if ('POST' === $_SERVER['REQUEST_METHOD']) {
    if (isset($_POST['username']) and isset($_POST['email']) and isset($_POST['password'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $emailValidation = Users::loadUserByEmail($conn,$email);
        if (!$username or !$email or !$password) {
            $_SESSION['failure2'] = 'Wrong data';
            header("Location: register.php");
            exit;
        }elseif (!empty($emailValidation )) {
            $_SESSION['failure2'] = 'Email is taken';
            header("Location: register.php");
            exit;
        }else {
            $user = new Users();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->saveToDB($conn);
            $_SESSION['user'] = $user->getId();
            header("Location: ../index.php");
        }
    }
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Register</title>
    </head>
    <body>
    <div>
        <div>
            <h1>Twitter</h1>
            <div>
                <p>Register</p>
                <form method="POST" action="">
                    <p>
                        <?php
                        if(isset($_SESSION['failure2'])){
                            echo $_SESSION['failure2'];
                            unset($_SESSION['failure2']);
                        }else{
                            echo "";
                        }
                        ?>
                    </p>
                    <p>
                        <label>Username: <input name="username" type="text"></label>
                    </p>
                    <p>
                        <label>Email: <input name="email" type="email"></label>
                    </p>
                    <p>
                        <label>Password: <input name="password" type="password"></label>
                    </p>
                    <p><label><input type="submit" value="Register"></label></p>
                </form>
                <a href="login.php">Login</a>
            </div>
        </div>
    </div>

    </body>
    </html>

    <?php
}