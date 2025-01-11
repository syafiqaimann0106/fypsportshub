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
    <title>Login</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <?php 
            include("php/config.php");
            if(isset($_POST['submit'])){
                $email = mysqli_real_escape_string($con, $_POST['email']);
                $password = $_POST['password'];
                $is_admin = isset($_POST['is_admin']) ? 1 : 0;

                $query = "SELECT * FROM users WHERE email = ?";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if($row = mysqli_fetch_assoc($result)){
                    if(password_verify($password, $row['password'])){
                        $_SESSION['valid'] = $row['email'];
                        $_SESSION['username'] = $row['username'];
                        $_SESSION['id'] = $row['id'];
                        $_SESSION['is_admin'] = $row['is_admin'];
                        
                        if($is_admin && $row['is_admin'] == 1){
                            header("Location: admin_panel.php");
                        } else {
                            header("Location: home.php");
                        }
                        exit();
                    } else {
                        echo "<div class='message'>
                            <p>Wrong Username or Password</p>
                            </div> <br>";
                        echo "<a href='index.php'><button class='btn'>Go Back</button>";
                    }
                } else {
                    echo "<div class='message'>
                        <p>Wrong Username or Password</p>
                        </div> <br>";
                    echo "<a href='index.php'><button class='btn'>Go Back</button>";
                }
            } else {
            ?>
            <header>Login</header>
            <div class="logo">
                <p><a href="home.php"><img src="img\logop.png" alt="Logo" width="50" height="auto"></a></p>
                <p>IIUM SportsHub</p>
            </div>
            <form action="" method="post">
                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>

                <div class="field">
                    <label for="is_admin" class="checkbox-label">
                        <input type="checkbox" name="is_admin" id="is_admin">
                        Login as Admin
                    </label>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Login" required>
                </div>
                <div class="links">
                    Don't have account? <a href="register.php">Sign Up Now</a>
                </div>
            </form>
        </div>
        <?php } ?>
    </div>
</body>
</html>