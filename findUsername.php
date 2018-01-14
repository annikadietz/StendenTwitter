<?php


function getUsername($id)
{
    require 'conn.php';
    $query = "SELECT userName "
            . "FROM stenden_user "
            . "WHERE userID = $id";
    $result = mysqli_query($conn, $query);
    if ($result === false)
    {
        echo "<p>Unable to execute the query.</p>"
                . "<p>Error code " . mysqli_errno($conn)
                . ": " . mysqli_error($conn) . "</p>";
    }
    else
    {
        foreach(mysqli_fetch_assoc($result) as $row => $name)
        {
            return $name;
        }
    }
    
}

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

