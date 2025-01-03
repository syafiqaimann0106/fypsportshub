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
    <title>Booking Confirmation - IIUM SportsHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h2 class="text-2xl font-bold mb-4 text-green-600">Booking Confirmed!</h2>
                <p class="mb-4">Your booking has been successfully confirmed and payment has been processed.</p>
                <p class="mb-4">You can view your booking details in the "My Bookings" section.</p>
                <a href="mybookings.php" class="block w-full bg-yellow-400 text-black text-center font-semibold py-3 px-6 rounded-lg hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50">
                    View My Bookings
                </a>
            </div>
        </div>
    </div>
</body>
</html>