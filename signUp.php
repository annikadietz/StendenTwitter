<!DOCTYPE html>
<?php
session_start();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" href="style.css" type="text/css">
        <link rel="stylesheet" href="styleIndex.css" type="text/css">
    </head>
    <body>
        <?php
        require "conn.php";
        require "findUser.php";

        if (isset($_POST['submit'])) {
            $username = sanitize($_POST['username']);
            $password = $_POST['password'];
            $passwordrep = $_POST['passwordrep'];
            $email = $_POST['email'];

            //checking wheter passwords are the same
            if ($password == $passwordrep) {
                //checking whether username is still available


                if (usernameAvailable($username)) {
                    //save the image
                    $imagepath = saveUploadedImage();
                    if ($imagepath != "error") {
                        $hashedpassword = password_hash($password, PASSWORD_BCRYPT);
                        $hashedmail = hash(PASSWORD_BCRYPT, $email);
                        $query = "INSERT INTO stenden_user "
                                . "(`userName`, `userPass`, `userEmail`, `name` ";
                        if (isset($_POST['image'])) {
                            $query .= ", `userImagePath`";
                        }
                        $query .= ") "
                                . "VALUES('$username', '$hashedpassword', '$email', '$username' ";
                        if (isset($_POST['image'])) {
                            $query .= ", '$imagepath'";
                        }
                        $query .= ")";
                        $result = mysqli_query($conn, $query);
                        if ($result === FALSE) {
                            echo "<p>Unable to execute the query.</p>"
                            . "<p>Error code " . mysqli_errno($conn)
                            . ": " . mysqli_error($conn) . "</p>";
                        } else {
                            $_SESSION['loggedIn'] = true;
                            $_SESSION['userID'] = getUserId($username);
                            //echo "<p>Welcome to Stenden Twitter, $username! We are glad to have you here."
                            //. "Go back to the main page <a href='index.php'>here</a>.</p>";
                            header('location: index.php');
                        }
                    } else {
                        header('Location: signUp.php?image');
                    }
                } else {
                    header('Location: signUp.php?user');
                }
            } else {
                header('Location: signUp.php?password');
            }
        }

        function saveUploadedImage() {
            if (!empty($_POST['image'])) {
                if ((($_FILES["image"]["type"] == "image/jpg") || ($_FILES["image"]["type"] == "image/jpeg") || ($_FILES["image"]["type"] == "image/pjpeg")) && ($_FILES["image"]["size"] < 50000000)) {

                    if ($_FILES["image"]["error"] > 0) {
                        echo "Return Code: " . $_FILES["image"]["error"] . "<br />";
                    } else {

                        // Checks if the file already exists, if it does not, it copies the file to the specified folder.
                        if (file_exists("upload/" . $_FILES["image"]["name"])) {
                            echo $_FILES["image"]["name"] . " already exists. ";
                        } else {
                            $newfilename = date('dmYHis') . str_replace(" ", "", basename($_FILES["image"]["name"]));
                            move_uploaded_file($_FILES["image"]["tmp_name"], "images/user/" . $newfilename);
                            $imagepath = "images/user/" . $newfilename;
                            return $imagepath;
                        }
                    }
                } else {
                    return "error";
                }
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
                        <img src="images/layout/thumb.png">
                    </div>
                    <div class="formWrap">
                        <h2>Sign up</h2>
                        <?php
                        if (isset($_GET['image'])) {
                            echo "<p>Wasn't able to save image. Please upload a different one.</p>";
                        } else if (isset($_GET['user'])) {
                            echo "<p>The username is already used. Please choose a different one.</p>";
                        } else if (isset($_GET['password'])) {
                            echo "<p>Your password was not the same.</p>";
                        }
                        require 'signUpForm.php';
                        ?>
                    </div>
                </div>

                <h2>Not signed up yet?<br>
                    <a href="login.php">Log in here!</a></h2>
            </div>
        </div>

    </body>
</html>
