<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include("php/config.php");

if(!isset($_SESSION['valid'])){
    header("Location: index.php");
    exit();
}

// Clear any previous pending booking when returning to this page
unset($_SESSION['pending_booking']);

$facility_id = isset($_GET['facility_id']) ? intval($_GET['facility_id']) : 0;
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

if ($facility_id === 0) {
    header("Location: facilities.php");
    exit();
}

// Fetch facility details
$facility_query = "SELECT * FROM facilities WHERE id = ?";
$stmt = $con->prepare($facility_query);
$stmt->bind_param("i", $facility_id);
$stmt->execute();
$facility_result = $stmt->get_result();
$facility = $facility_result->fetch_assoc();

if (!$facility) {
    header("Location: facilities.php");
    exit();
}

// Fetch booked slots for the selected date and facility
$booked_slots_query = "SELECT time_slot FROM bookings WHERE booking_date = ? AND facility_id = ? AND status != 'cancelled'";
$stmt = $con->prepare($booked_slots_query);
$stmt->bind_param("si", $date, $facility_id);
$stmt->execute();
$booked_slots_result = $stmt->get_result();
$booked_slots = array();
while ($row = $booked_slots_result->fetch_assoc()) {
    $booked_slots[] = $row['time_slot'];
}

// Define all possible time slots
$all_slots = array(
    '08:00:00' => '8 AM',
    '09:00:00' => '9 AM',
    '10:00:00' => '10 AM',
    '11:00:00' => '11 AM',
    '12:00:00' => '12 PM',
    '13:00:00' => '1 PM',
    '14:00:00' => '2 PM',
    '15:00:00' => '3 PM',
    '16:00:00' => '4 PM',
    '17:00:00' => '5 PM',
    '18:00:00' => '6 PM',
    '19:00:00' => '7 PM',
    '20:00:00' => '8 PM',
    '21:00:00' => '9 PM',
    '22:00:00' => '10 PM',
    '23:00:00' => '11 PM'
);

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['selected_slots']) && is_array($_POST['selected_slots'])) {
        $selected_slots = $_POST['selected_slots'];
        $user_id = $_SESSION['id'];
        $total_price = count($selected_slots) * $facility['hourly_rate'];

        // Check if selected slots are still available
        $unavailable_slots = array();
        foreach ($selected_slots as $slot) {
            if (in_array($slot, $booked_slots)) {
                $unavailable_slots[] = $slot;
            }
        }

        if (empty($unavailable_slots)) {
            $_SESSION['pending_booking'] = [
                'facility_id' => $facility_id,
                'facility_name' => $facility['name'],
                'date' => $date,
                'slots' => $selected_slots,
                'total_price' => $total_price
            ];
            header("Location: payment.php");
            exit();
        } else {
            $error_message = "The following slots are no longer available: " . implode(', ', $unavailable_slots) . ". Please select different slots.";
        }
    } else {
        $error_message = "Please select at least one time slot.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book <?php echo htmlspecialchars($facility['name']); ?> - IIUM SportsHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .time-slot {
            width: 80px;
            height: 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            user-select: none;
        }
        .time-slot:not(.booked):hover {
            transform: scale(1.05);
            background-color: #e5e7eb;
        }
        .time-slot.selected {
            background-color: #3B82F6 !important;
            color: white !important;
        }
        .time-slot.booked {
            background-color: #E11D48 !important;
            color: white !important;
            cursor: not-allowed;
        }
        .time {
            font-size: 24px;
            font-weight: bold;
            line-height: 1;
            pointer-events: none;
        }
        .period {
            font-size: 14px;
            margin-top: 4px;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-white">
    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <a href="facilities.php" class="flex items-center text-xl mb-8 hover:text-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </a>

        <h2 class="text-2xl font-bold mb-4">Select Time Slots</h2>
        <p class="mb-4">Facility: <?php echo htmlspecialchars($facility['name']); ?></p>
        <p class="mb-4">Date: <?php echo date('F d, Y', strtotime($date)); ?></p>

        <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p><?php echo $error_message; ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['booking_error'])): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p><?php echo $_SESSION['booking_error']; ?></p>
            </div>
            <?php unset($_SESSION['booking_error']); ?>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?facility_id=$facility_id&date=$date"; ?>" method="post" id="bookingForm">
            <div class="grid grid-cols-4 gap-4 mb-8">
                <?php foreach ($all_slots as $slot => $display_time): ?>
                    <?php
                    $is_booked = in_array($slot, $booked_slots);
                    $class = $is_booked ? 'booked' : 'bg-white hover:bg-gray-50';
                    list($time, $period) = explode(' ', $display_time);
                    ?>
                    <div class="time-slot <?php echo $class; ?> border" data-slot="<?php echo $slot; ?>">
                        <input type="checkbox" name="selected_slots[]" value="<?php echo $slot; ?>" 
                               <?php echo $is_booked ? 'disabled' : ''; ?> class="hidden" id="slot-<?php echo $slot; ?>">
                        <span class="time"><?php echo $time; ?></span>
                        <span class="period"><?php echo $period; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="w-full bg-yellow-400 text-black font-semibold py-3 px-6 rounded-lg hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50">
                Book
            </button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slots = document.querySelectorAll('.time-slot:not(.booked)');
            slots.forEach(slot => {
                slot.addEventListener('click', function(e) {
                    if (!this.classList.contains('booked')) {
                        this.classList.toggle('selected');
                        const checkbox = this.querySelector('input[type="checkbox"]');
                        checkbox.checked = !checkbox.checked;
                    }
                });
            });

            document.getElementById('bookingForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const selectedSlots = document.querySelectorAll('.time-slot.selected');
                if (selectedSlots.length === 0) {
                    alert('Please select at least one time slot.');
                } else {
                    const form = this;
                    // Clear all checkboxes first
                    form.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    // Then check only the selected ones
                    selectedSlots.forEach(slot => {
                        const checkbox = slot.querySelector('input[type="checkbox"]');
                        checkbox.checked = true;
                    });
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>