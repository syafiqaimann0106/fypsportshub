<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Register</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <?php 
            include("php/config.php");
            if(isset($_POST['submit'])){
                $username = mysqli_real_escape_string($con, $_POST['username']);
                $email = mysqli_real_escape_string($con, $_POST['email']);
                $password = $_POST['password'];
                $confirm_password = $_POST['confirm_password'];

                // Verify the unique email
                $verify_query = mysqli_query($con, "SELECT email FROM users WHERE email='$email'");

                if(mysqli_num_rows($verify_query) != 0){
                    echo "<div class='message'>
                        <p>This email is already in use. Please try another one!</p>
                    </div> <br>";
                    echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
                } elseif($password !== $confirm_password) {
                    echo "<div class='message'>
                        <p>Passwords do not match. Please try again!</p>
                    </div> <br>";
                    echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
                } else {
                    // Hash the password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Insert new user into the database
                    $insert_query = "INSERT INTO users(username, email, password) VALUES(?, ?, ?)";
                    $stmt = mysqli_prepare($con, $insert_query);
                    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashed_password);
                    
                    if(mysqli_stmt_execute($stmt)){
                        echo "<div class='message success'>
                            <p>Registration successful!</p>
                        </div> <br>";
                        echo "<a href='index.php' class='btn'>Login Now</a>";
                        exit();
                    } else {
                        echo "<div class='message'>
                            <p>Registration failed. Please try again.</p>
                        </div> <br>";
                        echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
                    }
                }
            } else {
            ?>
            <header>Sign Up</header>
            <div class="logo">
                <p><a href="home.php"><img src="img\logop.png" alt="Logo" width="50" height="auto"></a></p>
                <p>IIUM SportsHub</p>
            </div>
            <form action="" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" autocomplete="off" required>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Register" required>
                </div>
                <div class="links">
                    Already a member? <a href="index.php">Sign In</a>
                </div>
            </form>
        </div>
        <?php } ?>
    </div>
</body>
</html>