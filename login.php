<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" href="style.css" type="text/css">
        <link rel="stylesheet" href="styleIndex.css" type="text/css">
    </head>
    <body>
        <?php
        require 'findUser.php';
        if (isset($_POST['submit'])) {
            session_start();
            $username = $_POST['username'];
            $userpass = $_POST['password'];
            if (getUserId($username) != false) {
                $userID = getUserId($username);
                $savedpassword = getPassword($userID);
                if(password_verify($userpass, $savedpassword))
                {
                    $_SESSION['loggedIn'] = true;
                    $_SESSION['userID'] = $userID;
                    header('location: index.php');
                }
                else
                {
                    header('Location: login.php?combination');
                }
                        
            } else {
                header("Location: login.php?username=$username");
            }
        }

        
        ?>
        
        <div id="wrapper">
                <div id="main">
                    <div id="logoBox">
                        <img src="images/layout/Logo.png">
                    </div>
                    <h1>Welcome to <span class="university">Stenden</span> Twitter!</h1>
                    <div id="formBox">
                        <div id="placeholder">
                            <img src="images/layout/placeholder.png">
                        </div>
                        <div class="formWrap">
                            <h2>Login</h2>
                            <?php
                            if (isset($_GET['combination'])) {
                                echo "<p>The combination of your username and your password was wrong. "
                                . "Please enter the correct data.</p>";
                            } else if (isset($_GET['username'])) {
                                $username = $_GET['username'];
                                echo "<p>The username $username doesn't exist. Please enter an existing username.</p>";
                            } else if (isset($_GET['enter'])) {
                                echo "<p>Please enter an username and a password.</p>";
                            }
                            require 'loginForm.php';
                            ?>
                        </div>
                    </div>

                    <h2>Not signed up yet?<br>
                        <a href="signUp.php?signup">Sign up here!</a></h2>
                </div>
            </div>
    </body>
</html>