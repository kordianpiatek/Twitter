<?php

require_once '../src/Connection.php';
require_once '../src/Users.php';

session_start();
if($_SERVER['REQUEST_METHOD']== 'GET'){
    if($_GET['status'] == 'logout'){
        unset($_SESSION['user']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) and isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $user = Users::loadUserByEmail($conn, $email);
        if (password_verify($password, $user->getPassword())) {
            $_SESSION['user'] = $user->getId();
            header("Location: ../index.php");
        } else {
            $_SESSION['failure'] = 'Failed login';
            header("Location: login.php");
            exit;
        }
    }
} else {
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Login</title>
    </head>
    <body>
    <div>
        <div>
            <h1>Twitter</h1>
            <div>
                <p>Login:</p>
                <form method="POST" action="">
                    <p class="failure">
                        <?php
                        if(isset($_SESSION['failure'])){
                            echo $_SESSION['failure'];
                            unset($_SESSION['failure']);
                        }else{
                            echo "";
                        }
                        ?>
                    </p>
                    <p>
                        <label>
                            E-mail: <input name="email" type="email">
                        </label>
                    </p>
                    <p>
                        <label>
                            Password: <input name="password" type="password">
                        </label>
                    </p>
                    <p>
                        <input type="submit" value="Login">
                    </p>
                </form>
                <a href="register.php">Create new account</a>
            </div>
        </div>
    </div>
    </body>
    </html>

    <?php
}
