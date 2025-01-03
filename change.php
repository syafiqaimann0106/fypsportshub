<?php
session_start();
include("php/config.php");
if(!isset($_SESSION['valid'])){
    header("Location: index.php");
    exit();
}

$id = $_SESSION['id'];
$query = mysqli_query($con,"SELECT * FROM users WHERE id=$id");

if($result = mysqli_fetch_assoc($query)){
    $res_Uname = $result['username'];
    $res_Email = $result['email'];
    $res_Phone = isset($result['phone']) ? $result['phone'] : '';
    $res_id = $result['id'];
} else {
    echo "Error: User not found.";
    exit();
}

if(isset($_POST['update'])){
    $uname = mysqli_real_escape_string($con, $_POST['username']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    
    // Check if phone column exists
    $result = mysqli_query($con, "SHOW COLUMNS FROM users LIKE 'phone'");
    $exists = (mysqli_num_rows($result))?TRUE:FALSE;
    
    if($exists) {
        $user_update = mysqli_query($con, "UPDATE users SET username='$uname', email='$email', phone='$phone' WHERE id=$id");
    } else {
        $user_update = mysqli_query($con, "UPDATE users SET username='$uname', email='$email' WHERE id=$id");
    }
    
    if($user_update){
        $_SESSION['username'] = $uname;
        echo "<script>alert('Profile updated successfully!');</script>";
        echo "<script>window.location.href = 'change.php';</script>";
        exit();
    } else {
        echo "<script>alert('Unable to update profile: " . mysqli_error($con) . "');</script>";
    }
}

if(isset($_POST['upload_photo'])){
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    if(isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
        $imageFileType = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
        $target_file = $target_dir . $id . "." . $imageFileType;
        $uploadOk = 1;
    
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            echo "<script>alert('File is not an image.');</script>";
            $uploadOk = 0;
        }
    
        // Check file size
        if ($_FILES["profile_picture"]["size"] > 500000) {
            echo "<script>alert('Sorry, your file is too large.');</script>";
            $uploadOk = 0;
        }
    
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
            $uploadOk = 0;
        }
    
        // If everything is ok, try to upload file
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $new_file_name = $id . "." . $imageFileType;
                $update_photo = mysqli_query($con, "UPDATE users SET profile_picture='$new_file_name' WHERE id=$id");
                if($update_photo){
                    $_SESSION['profile_picture'] = $new_file_name;
                    echo "<script>alert('Profile picture updated successfully!');</script>";
                    echo "<script>window.location.href = 'change.php';</script>";
                    exit();
                } else {
                    echo "<script>alert('Unable to update profile picture in database: " . mysqli_error($con) . "');</script>";
                }
            } else {
                echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
            }
        }
    } else {
        echo "<script>alert('Please select a file to upload.');</script>";
    }
}

$profile_picture = "img/profiledefault.png";
$query_picture = mysqli_query($con, "SELECT profile_picture FROM users WHERE id=$id");
if($result_picture = mysqli_fetch_assoc($query_picture)){
    if($result_picture['profile_picture'] && file_exists("uploads/" . $result_picture['profile_picture'])){
        $profile_picture = "uploads/" . $result_picture['profile_picture'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Profile - IIUM SportsHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include 'header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h2 class="text-3xl font-bold mb-6 text-center">Change Profile Information</h2>
            
            <div class="mb-6 text-center">
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
                <form action="" method="post" enctype="multipart/form-data" class="mb-4">
                    <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-2">Update Profile Picture:</label>
                    <input type="file" name="profile_picture" id="profile_picture" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
                    <button type="submit" name="upload_photo" class="mt-2 bg-yellow-500 text-black px-4 py-2 rounded hover:bg-yellow-400 transition duration-300">Upload Photo</button>
                </form>
            </div>
            
            <div class="text-center mb-6">
                <a href="logout.php" class="bg-yellow-500 text-black px-4 py-2 rounded hover:bg-yellow-400 transition duration-300">Logout</a>
            </div>
    
            <form action="" method="post">
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username:</label>
                    <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($res_Uname); ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-yellow-500 focus:border-yellow-500">
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email:</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($res_Email); ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-yellow-500 focus:border-yellow-500">
                </div>
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone:</label>
                    <input type="tel" name="phone" id="phone" value="<?php echo htmlspecialchars($res_Phone); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-yellow-500 focus:border-yellow-500">
                </div>
                <div class="text-center">
                    <button type="submit" name="update" class="bg-yellow-500 text-black px-4 py-2 rounded hover:bg-yellow-400 transition duration-300">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>