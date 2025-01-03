<?php
session_start();
include("php/config.php");

// Check if the user is logged in and is an admin
if (!isset($_SESSION['valid']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

// Function to get all bookings
function getBookings($con) {
    $query = "SELECT b.id, b.booking_date, b.status, f.name AS facility_name, u.username 
              FROM bookings b 
              JOIN facilities f ON b.facility_id = f.id 
              JOIN users u ON b.user_id = u.id 
              ORDER BY b.booking_date DESC";
    $result = mysqli_query($con, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Function to get all facilities
function getFacilities($con) {
    $query = "SELECT * FROM facilities";
    $result = mysqli_query($con, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Function to get all users
function getUsers($con) {
    $query = "SELECT id, username FROM users";
    $result = mysqli_query($con, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Function to get all reviews
function getReviews($con) {
    $query = "SELECT r.id, r.rating, r.comment, u.username, f.name AS facility_name 
              FROM reviews r 
              JOIN bookings b ON r.booking_id = b.id 
              JOIN users u ON b.user_id = u.id 
              JOIN facilities f ON b.facility_id = f.id 
              ORDER BY r.created_at DESC";
    $result = mysqli_query($con, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_facility'])) {
        $name = mysqli_real_escape_string($con, $_POST['facility_name']);
        $hourly_rate = floatval($_POST['hourly_rate']);
        $query = "INSERT INTO facilities (name, hourly_rate) VALUES (?, ?)";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "sd", $name, $hourly_rate);
        mysqli_stmt_execute($stmt);
    } elseif (isset($_POST['delete_facility'])) {
        $facility_id = intval($_POST['facility_id']);
        $query = "DELETE FROM facilities WHERE id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "i", $facility_id);
        mysqli_stmt_execute($stmt);
    } elseif (isset($_POST['update_facility'])) {
        $facility_id = intval($_POST['facility_id']);
        $name = mysqli_real_escape_string($con, $_POST['facility_name']);
        $hourly_rate = floatval($_POST['hourly_rate']);
        $query = "UPDATE facilities SET name = ?, hourly_rate = ? WHERE id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "sdi", $name, $hourly_rate, $facility_id);
        mysqli_stmt_execute($stmt);
    } elseif (isset($_POST['add_booking'])) {
        $user_id = intval($_POST['user_id']);
        $facility_id = intval($_POST['facility_id']);
        $booking_date = mysqli_real_escape_string($con, $_POST['booking_date']);
        $query = "INSERT INTO bookings (user_id, facility_id, booking_date, status) VALUES (?, ?, ?, 'confirmed')";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "iis", $user_id, $facility_id, $booking_date);
        mysqli_stmt_execute($stmt);
    } elseif (isset($_POST['cancel_booking'])) {
        $booking_id = intval($_POST['booking_id']);
        $query = "UPDATE bookings SET status = 'cancelled' WHERE id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "i", $booking_id);
        mysqli_stmt_execute($stmt);
    }
}

$bookings = getBookings($con);
$facilities = getFacilities($con);
$users = getUsers($con);
$reviews = getReviews($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - IIUM SportsHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .hero-bg {
            background-color: #0a192f;
            position: relative;
            overflow: hidden;
        }
        .hero-bubble {
            background-color: #f6b03e;
            border-radius: 50%;
            position: absolute;
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include 'header.php'; ?>

    <div class="hero-bg text-white py-20">
        <div class="hero-bubble" style="width: 300px; height: 300px; top: -100px; right: -50px; opacity: 0.8;"></div>
        <div class="hero-bubble" style="width: 200px; height: 200px; bottom: -100px; left: 10%; opacity: 0.6;"></div>
        <div class="hero-bubble" style="width: 150px; height: 150px; top: 20%; left: 30%; opacity: 0.4;"></div>
        <div class="container mx-auto px-4 relative z-10">
            <h1 class="text-4xl font-bold mb-4 text-center">Admin Panel</h1>
            <p class="text-xl text-center">Manage bookings, facilities, and reviews</p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Bookings Section -->
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-4">Bookings</h2>
            <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
                <h3 class="text-xl font-bold mb-4">Add Booking</h3>
                <form action="" method="post" class="mb-6">
                    <div class="flex gap-4 mb-4">
                        <select name="user_id" required class="flex-grow p-2 border rounded">
                            <option value="">Select User</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>"><?php echo $user['username']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="facility_id" required class="flex-grow p-2 border rounded">
                            <option value="">Select Facility</option>
                            <?php foreach ($facilities as $facility): ?>
                                <option value="<?php echo $facility['id']; ?>"><?php echo $facility['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="date" name="booking_date" required class="flex-grow p-2 border rounded">
                        <button type="submit" name="add_booking" class="bg-yellow-500 text-black px-4 py-2 rounded hover:bg-yellow-400">Add Booking</button>
                    </div>
                </form>
                
                <h3 class="text-xl font-bold mb-4">Existing Bookings</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-yellow-500 text-black">
                            <tr>
                                <th class="py-2 px-4 text-left">ID</th>
                                <th class="py-2 px-4 text-left">Date</th>
                                <th class="py-2 px-4 text-left">Facility</th>
                                <th class="py-2 px-4 text-left">User</th>
                                <th class="py-2 px-4 text-left">Status</th>
                                <th class="py-2 px-4 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td class="py-2 px-4"><?php echo $booking['id']; ?></td>
                                <td class="py-2 px-4"><?php echo $booking['booking_date']; ?></td>
                                <td class="py-2 px-4"><?php echo $booking['facility_name']; ?></td>
                                <td class="py-2 px-4"><?php echo $booking['username']; ?></td>
                                <td class="py-2 px-4"><?php echo $booking['status']; ?></td>
                                <td class="py-2 px-4">
                                    <?php if ($booking['status'] !== 'cancelled'): ?>
                                        <form action="" method="post" class="inline">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <button type="submit" name="cancel_booking" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">Cancel</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        
        <!-- Facilities Section -->
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-4">Facilities</h2>
            <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
                <h3 class="text-xl font-bold mb-4">Add Facility</h3>
                <form action="" method="post" class="mb-6">
                    <div class="flex gap-4">
                        <input type="text" name="facility_name" placeholder="Facility Name" required class="flex-grow p-2 border rounded">
                        <input type="number" name="hourly_rate" placeholder="Hourly Rate" step="0.01" required class="w-32 p-2 border rounded">
                        <button type="submit" name="add_facility" class="bg-yellow-500 text-black px-4 py-2 rounded hover:bg-yellow-400">Add</button>
                    </div>
                </form>
                
                <h3 class="text-xl font-bold mb-4">Existing Facilities</h3>
                <div class="space-y-4">
                    <?php foreach ($facilities as $facility): ?>
                    <div class="flex items-center gap-4">
                        <form action="" method="post" class="flex-grow flex gap-4">
                            <input type="hidden" name="facility_id" value="<?php echo $facility['id']; ?>">
                            <input type="text" name="facility_name" value="<?php echo $facility['name']; ?>" required class="flex-grow p-2 border rounded">
                            <input type="number" name="hourly_rate" value="<?php echo $facility['hourly_rate']; ?>" step="0.01" required class="w-32 p-2 border rounded">
                            <button type="submit" name="update_facility" class="bg-yellow-500 text-black px-4 py-2 rounded hover:bg-yellow-400">Update</button>
                        </form>
                        <form action="" method="post">
                            <input type="hidden" name="facility_id" value="<?php echo $facility['id']; ?>">
                            <button type="submit" name="delete_facility" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Delete</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        
        <!-- Reviews Section -->
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-4">Reviews</h2>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-yellow-500 text-black">
                        <tr>
                            <th class="py-2 px-4 text-left">ID</th>
                            <th class="py-2 px-4 text-left">Facility</th>
                            <th class="py-2 px-4 text-left">User</th>
                            <th class="py-2 px-4 text-left">Rating</th>
                            <th class="py-2 px-4 text-left">Comment</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td class="py-2 px-4"><?php echo $review['id']; ?></td>
                            <td class="py-2 px-4"><?php echo $review['facility_name']; ?></td>
                            <td class="py-2 px-4"><?php echo $review['username']; ?></td>
                            <td class="py-2 px-4"><?php echo $review['rating']; ?></td>
                            <td class="py-2 px-4"><?php echo $review['comment']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <footer class="bg-black text-white py-6 mt-8">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2023 IIUM Sports Facilities. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>