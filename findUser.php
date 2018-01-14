<?php

function getUsername($id) {
    require 'conn.php';
    $query = "SELECT userName "
            . "FROM stenden_user "
            . "WHERE userID = $id";
    $result = mysqli_query($conn, $query);
    if ($result === false) {
        echo "<p>Unable to execute the query.</p>"
        . "<p>Error code " . mysqli_errno($conn)
        . ": " . mysqli_error($conn) . "</p>";
    } else {
        foreach (mysqli_fetch_assoc($result) as $row => $name) {
            return $name;
        }
    }
}

function getUserImagePath($id) {
    require 'conn.php';
    $query = "SELECT userImagePath "
            . "FROM stenden_user "
            . "WHERE userID = $id";
    $result = mysqli_query($conn, $query);
    if ($result === false) {
        echo "<p>Unable to execute the query.</p>"
        . "<p>Error code " . mysqli_errno($conn)
        . ": " . mysqli_error($conn) . "</p>";
    } else {
        foreach (mysqli_fetch_assoc($result) as $row => $path) {
            return $path;
        }
    }
}

function getUserId($username) {
    require "conn.php";
    if (usernameAvailable($username) == false) {
        $query = "SELECT userID "
                . "FROM stenden_user "
                . "WHERE userName = '$username'";
        $result = mysqli_query($conn, $query);
        if ($result === FALSE) {
            echo "<p>Unable to execute the query.</p>"
            . "<p>Error code " . mysqli_errno($conn)
            . ": " . mysqli_error($conn) . "</p>";
            return false;
        } else {
            foreach (mysqli_fetch_array($result) as $row => $id) {
                return $id;
            }
        }
    } else {
        return false;
    }
}

function getUserMail($id) {
    require 'conn.php';
    $query = "SELECT userEmail "
            . "FROM stenden_user "
            . "WHERE userID = $id";
    $result = mysqli_query($conn, $query);
    if ($result === false) {
        echo "<p>Unable to execute the query.</p>"
        . "<p>Error code " . mysqli_errno($conn)
        . ": " . mysqli_error($conn) . "</p>";
    } else {
        foreach (mysqli_fetch_assoc($result) as $row => $mail) {
            return $mail;
        }
    }
}

function getUserFullName($id) {
    require 'conn.php';
    $queryNames = "SELECT name "
            . "FROM stenden_user "
            . "WHERE userID = $id";
    $resultNames = mysqli_query($conn, $queryNames);
    if (!$resultNames === false) {
        $row = mysqli_fetch_row($resultNames);
        $name = $row[0];
        return $name;
    }
}

function usernameAvailable($name) {
    require "conn.php";
    $query = "SELECT userName "
            . "FROM stenden_user "
            . "WHERE userName = '$name'";
    $result = mysqli_query($conn, $query);
    if ($result === FALSE) {
        echo "<p>Unable to execute the query.</p>"
        . "<p>Error code " . mysqli_errno($conn)
        . ": " . mysqli_error($conn) . "</p>";
    } else {
        if (mysqli_num_rows($result) == 0) {
            return true;
        } else {
            return false;
        }
    }
}

function sendMessage($userID, $message) {
    require 'conn.php';
    $date = date("Y-m-d H:i:s");
    $hashtagList = implode(" ", detectHashtags($message));
    sanitize($message);
    $query = "INSERT INTO stenden_message "
            . "(`userID`, `message`, `hashtag`, `postedOn`) "
            . "VALUES ('$userID', '$message', '$hashtagList', '$date')";
    $result = mysqli_query($conn, $query);
    if ($result === FALSE) {
        echo "<p>Unable to execute the query.</p>"
        . "<p>Error code " . mysqli_errno($conn)
        . ": " . mysqli_error($conn) . "</p>";
    }
}

function detectHashtags($string) {
    $singleWords = explode(" ", $string);
    $hashes = array();
    foreach ($singleWords as $word) {
        if (substr($word, 0, 1) == "#") {
            $hashes[] = $word;
        }
    }
    return $hashes;
}

function sanitize($data) {
    return htmlentities($data, ENT_QUOTES, "UTF-8");
}

function createTaggingLinks($message) {
    if (!(strpos($message, "@") === false)) {
        $position = strpos($message, "@");
        $beginning = substr($message, 0, $position);
        $editString = substr($message, $position);
        $words = explode(" ", $editString);
        foreach ($words as &$word) {
            if (substr($word, 0, 1) == "@") {
                $username = substr($word, 1);
                $username = preg_replace('/[^a-z0-9 ]/i', '', $username);
                $word = "<a href='user.php?username=$username'>$word</a>";
            }
        }
        $message = $beginning . implode(" ", $words);
    }
    return $message;
}

function createHashtagLinks($message) {
    if (!(strpos($message, "#") === false)) {
        $position = strpos($message, "#");
        $beginning = substr($message, 0, $position);
        $editString = substr($message, $position);
        $words = explode(" ", $editString);
        foreach ($words as &$word) {
            if (substr($word, 0, 1) == "#") {
                $hashtag = substr($word, 1);
                $hashtag = preg_replace('/[^a-z0-9 ]/i', '', $hashtag);
                $path = "{$_SERVER['REQUEST_URI']}";
                $word = "<a href='$path&hashtag=$hashtag'><span class='hashtagLink'>$word</span></a>";
            }
        }
        $message = $beginning . implode(" ", $words);
    }
    return $message;
}

function isFollowing($follower, $user) { //returns true if logged in user(follower) is following the other person
    require 'conn.php';
    $query = "SELECT * from following where userID = $user AND followerID = $follower";
    $result = mysqli_query($conn, $query);
    if (!$result === false && mysqli_num_rows($result) > 0) {
        return true;
    } else {
        return false;
    }
}

function update($field, $newAttribute, $userID) {
    require 'conn.php';
    $update = "UPDATE stenden_user "
            . "SET $field = '$newAttribute' "
            . "WHERE userID = $userID";
    $result = mysqli_query($conn, $update) or die("$update");
}

function getPassword($userID) {
    require 'conn.php';
    $query = "SELECT userPass "
            . "FROM stenden_user "
            . "WHERE userID = $userID";
    $result = mysqli_query($conn, $query);
    if ($result === FALSE) {
        echo "<p>Unable to execute the query.</p>"
        . "<p>Error code " . mysqli_errno($conn)
        . ": " . mysqli_error($conn) . "</p>";
        return false;
    } else {
        foreach (mysqli_fetch_assoc($result) as $rows => $pass) {
            return $pass;
        }
    }
}

function convertDateToMDY($date) {
    $half = explode(" ", $date);
    $datum = explode("-", $half[0]);
    $year = $datum[1] . "/" . $datum[2] . "/" . $datum[0];
    $finalDate = $year . " " . $half[1];
    return $finalDate;
}

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

