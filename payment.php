<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include("php/config.php");

if(!isset($_SESSION['valid']) || !isset($_SESSION['pending_booking'])){
    header("Location: facilities.php");
    exit();
}

$booking = $_SESSION['pending_booking'];
$user_id = $_SESSION['id'];

// Fetch facility details
$facility_query = "SELECT * FROM facilities WHERE id = ?";
$stmt = $con->prepare($facility_query);
$stmt->bind_param("i", $booking['facility_id']);
$stmt->execute();
$facility_result = $stmt->get_result();
$facility = $facility_result->fetch_assoc();

if (!$facility) {
    header("Location: facilities.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if payment method is selected
    if (!isset($_POST['payment_method'])) {
        $error_message = "Please select a payment method.";
    } else {
        $payment_method = $_POST['payment_method'];
        
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

            // Check if payment_method column exists
            $check_column_query = "SHOW COLUMNS FROM payments LIKE 'payment_method'";
            $column_result = $con->query($check_column_query);
            
            if ($column_result->num_rows > 0) {
                // Column exists, use the updated query
                $insert_payment_query = "INSERT INTO payments (user_id, amount, payment_date, payment_status, payment_method) VALUES (?, ?, NOW(), 'completed', ?)";
                $payment_stmt = $con->prepare($insert_payment_query);
                $payment_stmt->bind_param("ids", $user_id, $booking['total_price'], $payment_method);
            } else {
                // Column doesn't exist, use the old query
                $insert_payment_query = "INSERT INTO payments (user_id, amount, payment_date, payment_status) VALUES (?, ?, NOW(), 'completed')";
                $payment_stmt = $con->prepare($insert_payment_query);
                $payment_stmt->bind_param("id", $user_id, $booking['total_price']);
            }
            
            $result = $payment_stmt->execute();
            if (!$result) {
                throw new Exception("Failed to insert payment: " . $payment_stmt->error);
            }

            // Commit transaction
            $con->commit();

            // Clear pending booking from session
            unset($_SESSION['pending_booking']);

            // Redirect to confirmation page
            header("Location: booking_confirmation.php");
            exit();
        } catch (Exception $e) {
            // Rollback transaction on error
            $con->rollback();
            $error_message = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - IIUM SportsHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h2 class="text-2xl font-bold mb-4">Payment Details</h2>
                <p class="mb-4"><strong>Facility:</strong> <?php echo htmlspecialchars($booking['facility_name']); ?></p>
                <p class="mb-4"><strong>Date:</strong> <?php echo date('F d, Y', strtotime($booking['date'])); ?></p>
                <p class="mb-4"><strong>Time Slots:</strong></p>
                <ul class="list-disc list-inside mb-4">
                    <?php foreach ($booking['slots'] as $slot): ?>
                        <li><?php echo htmlspecialchars($slot); ?></li>
                    <?php endforeach; ?>
                </ul>
                <p class="mb-6"><strong>Total Amount:</strong> RM<?php echo number_format($booking['total_price'], 2); ?></p>

                <?php if (isset($error_message)): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        <p><?php echo $error_message; ?></p>
                    </div>
                <?php endif; ?>

                <form action="" method="post" id="paymentForm">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Select Payment Method</h3>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="payment_method" value="credit_card" class="form-radio h-5 w-5 text-yellow-400" required>
                                <span class="ml-2">Credit Card</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="payment_method" value="debit_card" class="form-radio h-5 w-5 text-yellow-400" required>
                                <span class="ml-2">Debit Card</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="payment_method" value="online_banking" class="form-radio h-5 w-5 text-yellow-400" required>
                                <span class="ml-2">Online Banking</span>
                            </label>
                        </div>
                    </div>

                    <div id="cardDetails" class="mb-6 hidden">
                        <h3 class="text-lg font-semibold mb-2">Card Details</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="cardNumber" class="block text-sm font-medium text-gray-700">Card Number</label>
                                <input type="text" id="cardNumber" name="cardNumber" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50" placeholder="1234 5678 9012 3456" maxlength="19">
                            </div>
                            <div class="flex space-x-4">
                                <div class="flex-1">
                                    <label for="expiryDate" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                                    <input type="text" id="expiryDate" name="expiryDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50" placeholder="MM/YY" maxlength="5">
                                </div>
                                <div class="flex-1">
                                    <label for="cvv" class="block text-sm font-medium text-gray-700">CVV</label>
                                    <input type="text" id="cvv" name="cvv" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50" placeholder="123" maxlength="4">
                                </div>
                            </div>
                            <div>
                                <label for="cardholderName" class="block text-sm font-medium text-gray-700">Cardholder Name</label>
                                <input type="text" id="cardholderName" name="cardholderName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50" placeholder="John Doe">
                            </div>
                        </div>
                    </div>

                    <div id="onlineBankingDetails" class="mb-6 hidden">
                        <h3 class="text-lg font-semibold mb-2">Select Bank</h3>
                        <select name="bank" id="bank" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                            <option value="">Select a bank</option>
                            <option value="maybank">Maybank</option>
                            <option value="cimb">CIMB Bank</option>
                            <option value="publicbank">Public Bank</option>
                            <option value="rhb">RHB Bank</option>
                            <option value="hongleong">Hong Leong Bank</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-yellow-400 text-black font-semibold py-3 px-6 rounded-lg hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50">
                        Confirm Payment
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentForm = document.getElementById('paymentForm');
            const cardDetails = document.getElementById('cardDetails');
            const onlineBankingDetails = document.getElementById('onlineBankingDetails');
            const paymentMethods = document.getElementsByName('payment_method');

            paymentMethods.forEach(method => {
                method.addEventListener('change', function() {
                    if (this.value === 'credit_card' || this.value === 'debit_card') {
                        cardDetails.classList.remove('hidden');
                        onlineBankingDetails.classList.add('hidden');
                    } else if (this.value === 'online_banking') {
                        cardDetails.classList.add('hidden');
                        onlineBankingDetails.classList.remove('hidden');
                    }
                });
            });

            paymentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                // Here you would typically validate the form and process the payment
                // For this example, we'll just submit the form
                this.submit();
            });

            // Simple input masking for card number and expiry date
            document.getElementById('cardNumber').addEventListener('input', function (e) {
                this.value = this.value.replace(/\D/g, '').replace(/(.{4})/g, '$1 ').trim();
            });

            document.getElementById('expiryDate').addEventListener('input', function (e) {
                this.value = this.value.replace(/\D/g, '').replace(/^(\d{2})(\d)/, '$1/$2').substr(0, 5);
            });

            document.getElementById('cvv').addEventListener('input', function (e) {
                this.value = this.value.replace(/\D/g, '');
            });
        });
    </script>
</body>
</html>