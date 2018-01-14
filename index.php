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
            $userID = $_SESSION['userID'];
            $username = getUsername($userID);
            $userImagePath = getUserImagePath($userID);



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
                        <h1>Welcome to <span class="university">Stenden</span> Twitter, <?php echo $username; ?></h1>
                        <div class="menuSubjects">
                            <div class="menuItem">
                                <?php
                                echo "<div class='menuItem'>
                                        <a href='user.php?username=$username'>My page</a>
                                    </div>";
                                ?>
                            </div>
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
                    <div class="dashboardBox">
                        <h3>All messages:</h3>
                        <?php
                        $queryAll = "SELECT * "
                                . "FROM stenden_message "
                                . "ORDER BY msgID DESC";
                        $resultAll = mysqli_query($conn, $queryAll);
                        $numberAll = mysqli_num_rows($resultAll);
                        echo $numberAll;
                        ?>
                    </div>
                    <div class="dashboardBox">
                        <h3>Your messages:</h3>
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
                        <h3>Most active user:</h3>
                        <?php
                        $queryActiveUser = "SELECT userID, count(msgID) as amount "
                                . "FROM stenden_message "
                                . "Group by userID "
                                . "Order by amount DESC";
                        $resultActiveUser = mysqli_query($conn, $queryActiveUser);
                        if ($resultActiveUser === false || mysqli_num_rows($resultActiveUser) == 0) {
                            echo "None";
                        } else {
                            $users = array();
                            while ($row = mysqli_fetch_array($resultActiveUser)) {
                                $users[getUsername($row['userID'])] = $row['amount'];
                            }
                            $userNames = array_keys($users);
                            $username = $userNames[0];
                            echo "<a href='user.php?username=$username'>@$username</a> ($users[$username] tweets)";
                        }
                        ?>
                    </div>
                    <div id="hashtags" class="dashboardBox">
                        <h3>Popular last week:</h3>
                        <?php
                        $messages = array();
                        $queryHashtags = "SELECT message "
                                . "FROM stenden_message "
                                . "WHERE postedOn >= DATE_sub(now(), INTERVAL 1 WEEK)";
                        $resultHashtags = mysqli_query($conn, $queryHashtags);
                        if ($resultHashtags !== false && mysqli_num_rows($resultHashtags) != 0) {
                            while ($rowHashtags = mysqli_fetch_assoc($resultHashtags)) {
                                $messages[] = $rowHashtags['message'];
                            }
                            $longString = implode(" ", $messages);
                            $hashes = detectHashtags($longString);
                            $hashes = array_map('strtolower', $hashes);
                            $hashesAssoc = array_count_values($hashes);
                            array_multisort($hashesAssoc, SORT_DESC, $hashesAssoc);
                            $numberEntries = count($hashesAssoc);
                            if ($numberEntries >= 8) {
                                $hashtags = array_keys($hashesAssoc);
                                for ($i = 0; $i < 8; $i++) {
                                    $hashtag = $hashtags[$i];
                                    $hashtagWord = substr($hashtag, 1);
                                    $amount = $hashesAssoc[$hashtag];
                                    echo "<p><a href='index.php?hashtag=$hashtagWord'>" . $hashtag . "</a> ($amount tweets)</p>";
                                }
                            } else {
                                foreach ($hashesAssoc as $hashtag => $amount) {
                                    $hashtagWord = substr($hashtag, 1);
                                    echo "<p><a href='index.php?hashtag=$hashtagWord'>" . $hashtag . "</a> ($amount tweets)</p>";
                                }
                            }
                        }
                        ?>
                    </div>
                    <div class="dashboardBox">
                        <h3>New</h3> 
                        <?php
                        $queryGetLastTime = "SELECT lastOnIndex "
                                . "from stenden_user "
                                . "where userID = '$userID'";
                        $resultGetLastTime = mysqli_query($conn, $queryGetLastTime) or die("$queryGetLastTime");
                        while ($row = mysqli_fetch_array($resultGetLastTime)) {
                            $date = $row['lastOnIndex'];
                            $date = convertDateToMDY($date);
                        }
                        $now = time();
                        $interval = abs($now - strtotime($date));
                        //echo $interval;
                        $queryNew = "SELECT count(*) as number "
                                . "from stenden_message "
                                . "where postedOn >= DATE_sub(now(), INTERVAL $interval SECOND)";
                        $resultNew = mysqli_query($conn, $queryNew) or die("$queryNew");
                        $number = 0;
                        while ($row = mysqli_fetch_array($resultNew)) {
                            $number = $row['number'];
                        }
                        echo $number;
                        $last = date("Y-m-d H:i:s");
                        $sqlLastOnIndex = "Update stenden_user "
                                . "Set lastOnIndex = '$last' "
                                . "where userID = '$userID'";
                        $resultLastOnIndex = mysqli_query($conn, $sqlLastOnIndex) or die("$sqlLastOnIndex");
                        ?> 
                    </div>
                    <div class="dashboardBox">
                        <h3>Last visit:</h3>
                        <?php
                        $queryUserLoggedIn = "SELECT lastLoggedIn "
                                . "FROM stenden_user "
                                . "WHERE userID = $userID";
                        $resultUserLoggedIn = mysqli_query($conn, $queryUserLoggedIn);
                        $time = mysqli_fetch_assoc($resultUserLoggedIn);
                        echo $time['lastLoggedIn'];
                        ?>
                    </div>
                </div>



                <div class="wrapper-view">
                    <div class="wrapper">

                        <?php
                        $queryNumberFollowing = "SELECT count(*) as number "
                                . "FROM following "
                                . "WHERE followerID = '$userID'";
                        $resultNumberFollowing = mysqli_query($conn, $queryNumberFollowing) or die("$queryNumberFollowing");
                        $numberFollowing = 0;
                        while ($row = mysqli_fetch_assoc($resultNumberFollowing)) {
                            $numberFollowing = $row['number'];
                        }

                        if ($numberFollowing == 0) {
                            echo "<p>You should follow someone to see their tweets! Look at these users: </p>";
                            $queryUsers = "SELECT stenden_user.userID, count(msgID) as amount, userName "
                                    . "FROM stenden_message, stenden_user "
                                    . "WHERE stenden_message.userID = stenden_user.userID "
                                    . "Group by userID "
                                    . "Order by amount DESC";
                            $resultUsers = mysqli_query($conn, $queryUsers) or die("$queryUsers");
                            echo "<p>";
                            while ($row = mysqli_fetch_row($resultUsers)) {
                                $nameUser = $row[2];
                                echo "<a href='user.php?username=$nameUser'>@$nameUser</a><br>";
                            }
                            echo "</p>";
                        }

                        if (isset($_POST['submitSearchPerson'])) {
                            $userSearched = $_POST['search'];
                            echo "<p>Users that match your search:</p>";
                            $queryUsers = "SELECT stenden_user.userID, userName "
                                    . "FROM stenden_user "
                                    . "WHERE userName LIKE '%$userSearched%'";
                            $resultUsers = mysqli_query($conn, $queryUsers) or die("$queryUsers");
                            echo "<p>";
                            while ($row = mysqli_fetch_row($resultUsers)) {
                                $nameUser = $row[1];
                                echo "<a href='user.php?username=$nameUser'>@$nameUser</a><br>";
                            }
                            echo "</p>";
                        } else if(isset($_GET['seeAll']))
                        {
                            echo "<p>All users:</p>";
                            $queryUsers = "SELECT stenden_user.userID, userName "
                                    . "FROM stenden_user ";
                            $resultUsers = mysqli_query($conn, $queryUsers) or die("$queryUsers");
                            echo "<p>";
                            while ($row = mysqli_fetch_row($resultUsers)) {
                                $nameUser = $row[1];
                                echo "<a href='user.php?username=$nameUser'>@$nameUser</a><br>";
                            }
                            echo "</p>";
                        } else {

                            $query = "SELECT * "
                                    . "FROM stenden_message WHERE ";
                            if (isset($_GET['hashtag'])) {
                                $hashtagSearch = $_GET['hashtag'];
                                $query .= "hashtag like '%$hashtagSearch%' AND";
                            } else if (isset($_POST['submitSearch'])) {
                                $hashtagSearch = $_POST['search'];
                                if ($hashtagSearch != "") {
                                    $query .= "hashtag like '%$hashtagSearch%' AND ";
                                }
                            }
                            $query .= "(userID IN (SELECT userID from following where followerID = $userID) "
                                    . "or userID = $userID)"
                                    . "ORDER BY msgID DESC";
                            $result = mysqli_query($conn, $query);

                            if ($result === FALSE) {
                                echo "<p>Unable to execute the query.</p>"
                                . "<p>Error code " . mysqli_errno($conn)
                                . ": " . mysqli_error($conn) . "</p>";
                                echo $query;
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
                        }
                        ?>

                    </div>
                </div>

                <div class="formBox" id="personSearch">
                    <div class="searchPicture">
                        <img src="images/layout/placeholder.png">
                    </div>
                    <div class="formWrap">
                        <form method="POST" action="index.php">
                            <input type="search" name="search" placeholder="@Search">
                            <input type="submit" name="submitSearchPerson">
                        </form>
                        <a href="index.php?seeAll">See all persons</a>
                    </div>
                </div>

                <div class="formBox" id="hashtagSearch">
                    <div class="searchPicture">
                        <img src="images/layout/hashtag.png">
                    </div>
                    <div class="formWrap">
                        <form method="POST" action="index.php">
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
                        header('Location: login.php');
                    }
                    ?>
    </body>
</html>
