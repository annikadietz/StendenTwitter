<!DOCTYPE html>

<?php
session_start();
require 'findUser.php';
require 'conn.php';
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Stenden Twitter</title>
        <link rel="stylesheet" href="style.css" type="text/css">
        <link rel="stylesheet" href="styleIndex.css" type="text/css">
    </head>
    <body>
        <?php
        if (isset($_SESSION['loggedIn'])) {
            if (isset($_GET['username'])) {

                $userID = $_SESSION['userID'];
                $username = getUsername($userID);
                $userImagePath = getUserImagePath($userID);
                $userMail = getUserMail($userID);
                $userFullname = getUserFullName($userID);
                
                $usernamePage = $_GET['username'];
                if (getUserId($usernamePage) == $userID) {
                    $userIDPage = getUserId($usernamePage);
                    $userImagePathPage = getUserImagePath($userIDPage);

                    if (isset($_POST['newMessage'])) {
                        if (isset($_POST['tag'])) {
                            $message = $_POST['tag'] . " " . $_POST['message'];
                        } else {
                            $message = $_POST['message'];
                        }
                        sendMessage($userID, $message);
                    }
                    ?>
                    <div id="content">
                        <div class="heading">
                            <div class="logo">
                                <img src="images/layout/Logo.png" alt="logo">
                            </div>
                            <div class="header">
                                <h1><?php echo $userFullnamePage; ?></h1>
                                <div class="menuSubjects">
                                    <div class="menuItem">
                                        <a href="index.php">Home</a>
                                    </div>
                                    <?php
                                    if ($userID != $userIDPage) {
                                        echo "<div class='menuItem'>
                                        <a href='user.php?username=$username'>My page</a>
                                    </div>";
                                    }
                                    ?>
                                    <div class="menuItem">
                                        <a href="logout.php">log out</a>
                                    </div>
                                </div>
                            </div>
                            <div class="userImage">
                                <?php
                                echo "<img src='$userImagePath' alt='image'>";
                                ?>
                            </div>
                        </div>

                        <div id="dashboard" class="formBox">
                            <?php
                            if ($userID == $userIDPage) {
                                //My Page
                                ?>
                                <div class="dashboardBox">
                                    <?php
                                    $userFullnamePage = getUserFullName($userIDPage);
                                        echo "<h3>$userFullnamePage</h3>"
                                        . "<p>@$usernamePage</p>";
                                    ?>
                                </div>
                                <div class="dashboardBox">
                                    Number Following
                                </div>
                                <div class="dashboardBox">
                                    Number Follower
                                </div>
                                <div id="hashtags" class="dashboardBox">
                                    <div class="userImage">
                                        <?php
                                        echo "<img src='$userImagePath' alt='image'>";
                                        ?>
                                    </div>
                                </div>
                                <div class="dashboardBox">
                                    <h3>Tweets:</h3 >
                                    <?php
                                    $queryUserMsg = "SELECT * "
                                            . "FROM stenden_message "
                                            . "WHERE userID = $userID "
                                            . "ORDER BY msgID DESC";
                                    $resultUserMsg = mysqli_query($conn, $queryUserMsg);
                                    $numberUser = mysqli_num_rows($resultUserMsg);
                                    echo $numberUser;
                                    ?>
                                </div>
                                <div class="dashboardBox">
                                    Currently editing

                                </div>
                        <?php
                        }
                        ?>
                            
                    </div>



                    <div class="wrapper-view">
                        <div class="wrapper">
                            <h2>Username: @<?php echo $username; ?></h2>
                            <form action="user.php?username=<?php echo $username; ?>" method="post" enctype="multipart/form-data">
                                <label>Name:</label><input type="text" name="name" value="<?php echo $userFullname; ?>"><br>
                                <label>E-mail:</label><input type="email" name="mail" value="<?php echo $userMail; ?>"> <br>
                                <label>Old password:</label><input type="password" name="oldPassword"><br>
                                <label>New password:</label><input type="password" name="newPassword"><br>
                                <label>Repeat new password: </label><input type="password" name="repNewPassword"><br>
                                <label>Change profile picutre: </label> <input type="file" name="image" id="image"><br>
                                <input type="submit" value="Save" name="update">
                            </form>

                        </div>
                    </div>


                </div>
                <?php
            } else {
                header("Location: user.php?username=$username");
            }
        } else {
            echo "something is wrong.";
        }
    } else {
        header('Location: login.php');
    }
    ?>
</body>
</html>
