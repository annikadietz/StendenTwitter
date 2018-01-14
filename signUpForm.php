
        <form method="post" action="signUp.php" enctype="multipart/form-data">
            <div class="line">
                <label>Username:</label> 
                <input type="text" name="username" required="required">
            </div>
            <div class="line">
                <label>Password:</label>
                <input type="password" name="password" required="required">
            </div>
            <div class="line">
                <label>Repeat password:</label>
                <input type="password" name="passwordrep" required="required">
            </div>
            <div class="line">
                <label>E-mail:</label>
                <input type="email" name="email" required="required">
            </div>
            <div class="line">
                <label>Avatar image:</label>
                <input type="file" name="image">
            </div>
            <div class="line" id="button">
                <input type="submit" name="submit" value="Sign up">
            </div>
        </form>
