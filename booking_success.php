<?php
session_start();
if(!isset($_SESSION['valid'])){
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Successful - IIUM SportsHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h2 class="text-2xl font-bold mb-4 text-green-600">Booking Successful!</h2>
                <p class="mb-4">Your booking has been confirmed and payment has been processed successfully.</p>
                <p class="mb-4">You can view your booking details in the <a href="mybookings.php" class="text-blue-600 hover:underline">My Bookings</a> section.</p>
                <a href="facilities.php" class="inline-block bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 focus:outline-none focus:bg-yellow-600">
                    Book Another Facility
                </a>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>