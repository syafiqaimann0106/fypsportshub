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

// Start transaction
$con->begin_transaction();

try {
    // Check if slots are still available
    $check_availability_query = "SELECT COUNT(*) as count FROM bookings WHERE facility_id = ? AND booking_date = ? AND time_slot IN (" . implode(',', array_fill(0, count($booking['slots']), '?')) . ") AND status = 'confirmed'";
    $check_stmt = $con->prepare($check_availability_query);
    $params = array_merge([$booking['facility_id'], $booking['date']], $booking['slots']);
    $types = str_repeat('s', count($params));
    $check_stmt->bind_param($types, ...$params);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        throw new Exception("One or more selected slots are no longer available. Please try booking again.");
    }

    // Insert booking records
    $insert_booking_query = "INSERT INTO bookings (user_id, facility_id, booking_date, time_slot, status, created_at) VALUES (?, ?, ?, ?, 'confirmed', NOW())";
    $insert_stmt = $con->prepare($insert_booking_query);

    $booking_ids = [];
    foreach ($booking['slots'] as $slot) {
        $insert_stmt->bind_param("iiss", $user_id, $booking['facility_id'], $booking['date'], $slot);
        $result = $insert_stmt->execute();
        if (!$result) {
            throw new Exception("Failed to insert booking: " . $insert_stmt->error);
        }
        $booking_ids[] = $con->insert_id;
    }

    // Insert payment record
    $insert_payment_query = "INSERT INTO payments (booking_id, user_id, amount, payment_date, payment_status) VALUES (?, ?, ?, NOW(), 'completed')";
    $payment_stmt = $con->prepare($insert_payment_query);
    $payment_stmt->bind_param("iid", $booking_ids[0], $user_id, $booking['total_price']);
    $result = $payment_stmt->execute();
    if (!$result) {
        throw new Exception("Failed to insert payment: " . $payment_stmt->error);
    }

    // Commit transaction
    $con->commit();

    // Clear the pending booking from session
    unset($_SESSION['pending_booking']);

    // Redirect to booking success page
    header("Location: booking_success.php");
    exit();
} catch (Exception $e) {
    // Rollback transaction on error
    $con->rollback();
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: booking.php?facility_id=" . $booking['facility_id'] . "&date=" . $booking['date']);
    exit();
}