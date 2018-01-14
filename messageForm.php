<form method="post" action="index.php">
    <p><textarea name="message" placeholder="What are your doing?"></textarea></p><?php
    $path = "{$_SERVER['REQUEST_URI']}";
    if (!(strpos($path, "StendenTwitter/user.php") === false)) {
        if ($userID != $userIDPage) {
            echo "<input type='hidden' name='tag' value='@$usernamePage'>";
        }
    }
    ?>
    <p><input type="submit" name="newMessage" value="Send Tweet!"></p>
</form>


