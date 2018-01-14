<?php

$db_name = "twitter";
//assign the connection and selected database to a variable
$conn = mysqli_connect("localhost", "root", "");
if ($conn === FALSE) {
    echo "<p>Unable to connect to the database server.</p>"
    . "<p>Error code "
    . mysqli_errno($conn) . ": " . mysqli_error($conn) . "</p>";
} else {
//select the database
    $db = mysqli_select_db($conn, $db_name);

    if ($db === FALSE) {
        echo "<p>Unable to connect to the database server.</p>"
        . "<p>Error code " . mysqli_errno($conn) . ": " . mysqli_error($conn) . "</p>";
        mysqli_close($conn);
        $conn = FALSE;
    }
}
?>