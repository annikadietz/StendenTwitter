<?php
session_start();
require 'conn.php';
$date = date("Y-m-d H:i:s");
$sql = "Update stenden_user "
        . "Set lastLoggedIn = '$date' "
        . "where userID = {$_SESSION['userID']}";
$result = mysqli_query($conn, $sql);
$_SESSION = array();
$_SESSION['loggedIn'] = false;	

session_destroy();
header('location: index.php');
?>