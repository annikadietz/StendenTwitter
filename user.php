<!DOCTYPE html>

<?php
session_start();
require 'findUser.php';
require 'conn.php';

function saveUploadedImage() {
    $target_dir = "images/user/";
    $target_file = $target_dir . date('dmYHis') . str_replace(" ", "", basename($_FILES["image"]["name"]));
    ;
    $uploadOK = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    //check if image file is a actual image or fake image
    if (isset($_POST['update'])) {
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check == false) {
            echo "size";
            $uploadOK = 0;
        }
        //check if file already exists
        if (file_exists($target_file)) {
            echo "file exists";
            $uploadOK = 0;
        }
        //check file size
        if ($_FILES["image"]["size"] > 50000000) {
            echo "file size";
            $uploadOK = 0;
        }
        //allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            echo "format";
            $uploadOK = 0;
        }
        //check if there was any error
        if ($uploadOK == 0) {

            echo "error";
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                return $target_file;
            } else {
                echo "There was an error saving the file.";
            }
        }
    }
}
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


                if (isset($_POST['update'])) {
                    if (!empty($_POST['name']) || !empty($_POST['mail']) || !empty($_POST['oldPassword']) || !empty($_POST['newPassword']) || !empty($_POST['repNewPassword']) || !empty($_POST['image'])) {

                        if (!empty($newName = $_POST['name'])) {
                            $field = "name";
                            update($field, $newName, $userID);
                        }
                        if (!empty($newMail = $_POST['mail'])) {
                            $field = "userEmail";
                            update($field, $newMail, $userID);
                        }
                        if (!empty($_POST['oldPassword']) && isset($_POST['newPassword']) && isset($_POST['repNewPassword'])) {
                            $oldPassword = $_POST['oldPassword'];
                            $password = $_POST['newPassword'];
                            $passwordRep = $_POST['repNewPassword'];
                            
                            if(password_verify($oldPassword, getPassword($userID)))
                            {
                                if($password == $passwordRep)
                                {
                                    $hashedpassword = password_hash($password, PASSWORD_BCRYPT);
                                    $field = "userPass";
                                    update($field, $hashedpassword, $userID);
                                }
                            }
                        }
                        if (file_exists($_FILES['image']['tmp_name']) || is_uploaded_file($_FILES['image']['tmp_name'])) {
                            $newImagepath = saveUploadedImage();
                            $field = "userImagePath";
                            update($field, $newImagepath, $userID);
                        }
                    }
                }
                $username = getUsername($userID);
                $userImagePath = getUserImagePath($userID);
                $usernamePage = $_GET['username'];
                if (getUserId($usernamePage)) {
                    $userIDPage = getUserId($usernamePage);
                    $userImagePathPage = getUserImagePath($userIDPage);
                    $userFullnamePage = getUserFullName($userIDPage);

                    if (isset($_POST['newMessage'])) {
                        $message = $_POST['message'];
                        sendMessage($userID, $message);
                    }
                    if (isset($_POST['submitFollow'])) {
                        $queryFollow = "INSERT INTO following VALUES('$userIDPage', '$userID')";
                        $resultFollow = mysqli_query($conn, $queryFollow) OR die;
                    }
                    if (isset($_POST['submitUnfollow'])) {
                        $queryUnfollow = "DELETE From following where userID = '$userIDPage' AND followerID = '$userID'";
                        $resultUnfollow = mysqli_query($conn, $queryUnfollow) OR die;
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
                                    <h3>Following</h3>
                                    <?php
                                    $queryNumberFollowing = "SELECT count(*) as number "
                                            . "FROM following "
                                            . "WHERE followerID = '$userIDPage'";
                                    $resultNumberFollowing = mysqli_query($conn, $queryNumberFollowing) or die("$queryNumberFollowing");
                                    while($row = mysqli_fetch_assoc($resultNumberFollowing)) 
                                    {
                                        $numberFollowing = $row['number'];
                                        echo $numberFollowing;
                                    }
                                    
                                    ?>
                                </div>
                                <div class="dashboardBox">
                                    <h3>Follower</h3>
                                    <?php
                                    $queryNumberFollowing = "SELECT count(*) as number "
                                            . "FROM following "
                                            . "WHERE userID = '$userIDPage'";
                                    $resultNumberFollowing = mysqli_query($conn, $queryNumberFollowing) or die("$queryNumberFollowing");
                                    while($row = mysqli_fetch_assoc($resultNumberFollowing)) 
                                    {
                                        $numberFollowing = $row['number'];
                                        echo $numberFollowing;
                                    }
                                    
                                    ?>
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
                                    <?php
                                    echo "<a href='edit.php?username=$username'>Edit your profile</a>"
                                    ?>

                                </div>
                                <?php
                            } else {
                                //any other page
                                ?>
                                <div class="dashboardBox">
                                    <?php
                                    $userFullnamePage = getUserFullName($userIDPage);
                                    echo "<h3>$userFullnamePage</h3>"
                                    . "<p>@$usernamePage</p>";
                                    ?>
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
                                    <?php
                                    echo "<form method='post' action='user.php?username=$usernamePage'>";
                                    if (isFollowing($userID, $userIDPage)) {
                                        echo "<input type='submit' name='submitUnfollow' value='Unfollow'>";
                                    } else {
                                        echo "<input type='submit' name='submitFollow' value='Follow'>";
                                    }
                                    ?>
                                    </form>
                                </div>
                                <div id="hashtags" class="dashboardBox">
                                    <div class="userImage">
                                        <?php
                                        echo "<img src='$userImagePathPage' alt='image'>";
                                        ?>
                                    </div>
                                </div>
                                <div class="dashboardBox">
                                    <h3>Following</h3>
                                    <?php
                                    $queryNumberFollowing = "SELECT count(*) as number "
                                            . "FROM following "
                                            . "WHERE followerID = '$userIDPage'";
                                    $resultNumberFollowing = mysqli_query($conn, $queryNumberFollowing) or die("$queryNumberFollowing");
                                    while($row = mysqli_fetch_assoc($resultNumberFollowing)) 
                                    {
                                        $numberFollowing = $row['number'];
                                        echo $numberFollowing;
                                    }
                                    
                                    ?>
                                </div>
                                <div class="dashboardBox">
                                    <h3>Follower</h3>
                                    <?php
                                    $queryNumberFollowing = "SELECT count(*) as number "
                                            . "FROM following "
                                            . "WHERE userID = '$userIDPage'";
                                    $resultNumberFollowing = mysqli_query($conn, $queryNumberFollowing) or die("$queryNumberFollowing");
                                    while($row = mysqli_fetch_assoc($resultNumberFollowing)) 
                                    {
                                        $numberFollowing = $row['number'];
                                        echo $numberFollowing;
                                    }
                                    
                                    ?>

                                </div>
                                <?php
                            }
                            ?>
                        </div>

                        <div class="wrapper-view">
                            <div class="wrapper">
                                <?php
                                $query = "SELECT * "
                                        . "FROM stenden_message WHERE ";
                                if (isset($_GET['hashtag'])) {
                                    $hashtagSearch = $_GET['hashtag'];
                                    $query .= "hashtag like '%$hashtagSearch%' AND ";
                                } else if (isset($_POST['submitSearch'])) {
                                    $hashtagSearch = $_POST['search'];
                                    if ($hashtagSearch != "") {
                                        $query .= "hashtag like '%$hashtagSearch%' AND ";
                                    }
                                }
                                $query .= "userID = $userIDPage "
                                        . "ORDER BY msgID DESC";
                                $result = mysqli_query($conn, $query);

                                if ($result === FALSE) {
                                    echo "<p>Unable to execute the query.</p>"
                                    . "<p>Error code " . mysqli_errno($conn)
                                    . ": " . mysqli_error($conn) . "</p>";
                                } else {
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $msgUserID = $row['userID'];
                                            $msgUsername = getUsername($msgUserID);
                                            $message = createHashtagLinks(createTaggingLinks($row['message']));
                                            $msgUserImagePath = getUserImagePath($msgUserID);
                                            $timestamp = $row['postedOn'];

                                            echo "<div class='commentLine'>";
                                            echo "<div class='commentUserimage'><img src='$msgUserImagePath'></div>";
                                            echo "<div class='comment'>";
                                            echo "<div class='commenUsername'><a href='user.php?username=$msgUsername'>$msgUsername</a></div><p>";
                                            echo $message . "</p>"
                                            . "<p id='timestamp'>$timestamp</p></div></div>";
                                        }
                                    } else {
                                        echo "<p>Sorry, but there are no tweets.</p>";
                                    }
                                }
                                ?>

                            </div>
                        </div>

                        <div class="formBox" id="hashtagSearch">
                            <div class="searchPicture">
                                <img src="images/layout/hashtag.png">
                            </div>
                            <div class="formWrap">
                                <?php
                                echo "<form method='post' action='user.php?username=$usernamePage'>";
                                ?>
                                <input type="search" name="search" placeholder="#Search">
                                <input type="submit" name="submitSearch">
                                </form>
                                <a href="index.php">See all tweets</a>
                            </div>
                        </div>

                        <div id="message" class="formBox">
                            <div class="userImage">
                                <?php
                                echo "<img src='$userImagePath'>";
                                ?>
                            </div>
                            <div class="formWrap">
                                <?php
                                require 'messageForm.php';
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                } else {
                    echo "User doesn't exist.";
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
