<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("php/config.php");

// Debug: Print POST data
echo "<pre>POST: "; print_r($_POST); echo "</pre>";
// Debug: Print SESSION data
echo "<pre>SESSION: "; print_r($_SESSION); echo "</pre>";

if(!isset($_SESSION['valid']) || $_SERVER['REQUEST_METHOD'] !== 'POST'){
    header("Location: facilities.php");
    exit();
}

$facility_id = isset($_POST['facility_id']) ? intval($_POST['facility_id']) : 0;
$date = isset($_POST['date']) ? $_POST['date'] : '';
$selected_slots = isset($_POST['selected_slots']) ? $_POST['selected_slots'] : [];

if ($facility_id === 0 || empty($date) || empty($selected_slots)) {
    header("Location: facilities.php");
    exit();
}

$booking = [
    'facility_id' => $facility_id,
    'date' => $date,
    'slots' => $selected_slots,
    'total_price' => isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0,
    'facility_name' => isset($_POST['facility_name']) ? $_POST['facility_name'] : ''
];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['id'];

    // Start transaction
    $con->begin_transaction();

    try {
        // Insert booking records
        $insert_booking_query = "INSERT INTO bookings (user_id, facility_id, booking_date, time_slot, status, created_at) VALUES (?, ?, ?, ?, 'confirmed', NOW())";
        $insert_stmt = $con->prepare($insert_booking_query);

        foreach ($booking['slots'] as $slot) {
            $insert_stmt->bind_param("iiss", $user_id, $booking['facility_id'], $booking['date'], $slot);
            $result = $insert_stmt->execute();
            if (!$result) {
                throw new Exception("Failed to insert booking: " . $insert_stmt->error);
            }
        }

        // Insert payment record
        $insert_payment_query = "INSERT INTO payments (user_id, amount, payment_date, payment_status) VALUES (?, ?, NOW(), 'completed')";
        $payment_stmt = $con->prepare($insert_payment_query);
        $payment_stmt->bind_param("id", $user_id, $booking['total_price']);
        $result = $payment_stmt->execute();
        if (!$result) {
            throw new Exception("Failed to insert payment: " . $payment_stmt->error);
        }

        // Commit transaction
        $con->commit();

        // Clear the pending booking from session
        //unset($_SESSION['pending_booking']);  //Removed as pending booking is not set in this updated code.

        // Redirect to a success page
        header("Location: booking_success.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $con->rollback();
        $error_message = "An error occurred: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation - IIUM SportsHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <header class="bg-black text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <img src="img/iiumlogo.png" alt="Logo" class="h-12 w-12 mr-4">
                <h1 class="text-2xl font-bold">IIUM SportsHub</h1>
            </div>
            <nav>
                <a href="home.php" class="text-white hover:text-yellow-300 mr-4">Home</a>
                <a href="facilities.php" class="text-white hover:text-yellow-300 mr-4">Facilities</a>
                <a href="mybookings.php" class="text-white hover:text-yellow-300">My Bookings</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto mt-8 p-4">
        <div class="bg-white rounded-lg shadow-md p-6 max-w-md mx-auto">
            <h2 class="text-2xl font-bold mb-4 text-center">Payment Confirmation</h2>
            <div class="mb-4">
                <p class="font-semibold">Facility: <?php echo htmlspecialchars($booking['facility_name']); ?></p>
                <p>Date: <?php echo date('F d, Y', strtotime($booking['date'])); ?></p>
                <p>Time Slots:</p>
                <ul class="list-disc list-inside">
                    <?php foreach ($booking['slots'] as $slot): ?>
                        <li><?php echo htmlspecialchars($slot); ?></li>
                    <?php endforeach; ?>
                </ul>
                <p class="mt-4 font-semibold">Total Amount: RM<?php echo number_format($booking['total_price'], 2); ?></p>
            </div>
            <?php if (isset($error_message)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p><?php echo $error_message; ?></p>
                </div>
            <?php endif; ?>
            <form action="" method="post" class="mt-6">
                <div class="mb-4">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                    <select id="payment_method" name="payment_method" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                        <option value="">Select a payment method</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="debit_card">Debit Card</option>
                        <option value="online_banking">Online Banking</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="card_number" class="block text-sm font-medium text-gray-700">Card Number</label>
                    <input type="text" id="card_number" name="card_number" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                </div>
                <div class="mb-4">
                    <label for="expiry_date" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                    <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                </div>
                <div class="mb-4">
                    <label for="cvv" class="block text-sm font-medium text-gray-700">CVV</label>
                    <input type="text" id="cvv" name="cvv" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                </div>
                <div class="text-center">
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded">
                        Confirm Payment
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-gray-200 text-center p-4 mt-8">
        <p>&copy; 2024 IIUM SportsHub. All rights reserved.</p>
    </footer>
</body>
</html>