<?php
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}
include("php/config.php");

$profile_picture = "img/profiledefault.png";
if(isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
    $query_picture = mysqli_query($con, "SELECT profile_picture FROM users WHERE id=$id");
    if($result_picture = mysqli_fetch_assoc($query_picture)){
        if($result_picture['profile_picture']){
            $profile_picture = "uploads/" . $result_picture['profile_picture'];
        }
    }
}
?>

<header class="bg-black text-white">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">
        <div class="flex items-center">
        <img src="img/iium.png" alt="IIUM Logo" class="h-24 w-24 mr-6" width="96" height="96"/>
            <nav class="ml-6">
                <a href="home.php" class="text-white px-4 py-2 hover:underline">Home</a>
                <a href="facilities.php" class="text-white px-4 py-2 hover:underline">Facilities</a>
                <a href="mybookings.php" class="text-white px-4 py-2 hover:underline">MyBookings</a>
                <a href="contactus.php" class="text-white px-4 py-2 hover:underline">Contact Us</a>
                <a href="faq.php" class="text-white px-4 py-2 hover:underline">FAQ</a>
            </nav>
        </div>
        <div>
            <?php if(isset($_SESSION['valid'])): ?>
                <a href="change.php">
                    <img src="<?php echo file_exists($profile_picture) ? htmlspecialchars($profile_picture) : 'img/profiledefault.png'; ?>" alt="Profile Picture" class="w-8 h-8 rounded-full object-cover">
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>