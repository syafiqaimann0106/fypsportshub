<?php
session_start();
include("php/config.php");

if(!isset($_SESSION['valid'])){
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['id'];

// Handle booking cancellation
if(isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    $cancel_query = "UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'confirmed'";
    $cancel_stmt = $con->prepare($cancel_query);
    $cancel_stmt->bind_param("ii", $booking_id, $user_id);
    $cancel_stmt->execute();
    header("Location: mybookings.php");
    exit();
}

// Handle review submission
if(isset($_POST['submit_review'])) {
    $booking_id = $_POST['booking_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $review_query = "INSERT INTO reviews (booking_id, user_id, rating, comment) VALUES (?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment)";
    $review_stmt = $con->prepare($review_query);
    $review_stmt->bind_param("iiis", $booking_id, $user_id, $rating, $comment);
    $review_stmt->execute();
    header("Location: mybookings.php");
    exit();
}

// Fetch bookings with facility information and reviews
$query = "SELECT b.id, b.booking_date, b.time_slot, b.status, f.name AS facility_name, 
          r.rating, r.comment
          FROM bookings b 
          JOIN facilities f ON b.facility_id = f.id 
          LEFT JOIN reviews r ON b.id = r.booking_id
          WHERE b.user_id = ? 
          ORDER BY b.booking_date DESC, b.time_slot ASC";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - IIUM SportsHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .hero-bg {
            background-color: #001B3D;
            position: relative;
            overflow: hidden;
        }
        .hero-bubble {
            position: absolute;
            background-color: #FDB338;
            border-radius: 50%;
            z-index: 0;
        }
    </style>
</head>
<body class="bg-white">
    <?php include 'header.php'; ?>

    <!-- Hero Section -->
    <div class="hero-bg text-white">
        <div class="hero-bubble" style="width: 300px; height: 300px; top: -100px; right: -50px; opacity: 0.8;"></div>
        <div class="hero-bubble" style="width: 200px; height: 200px; bottom: -100px; left: 10%; opacity: 0.6;"></div>
        <div class="hero-bubble" style="width: 150px; height: 150px; top: 20%; left: 30%; opacity: 0.4;"></div>
        <div class="relative container mx-auto px-4 py-16 text-center">
            <h1 class="text-5xl font-bold mb-8">MY BOOKINGS</h1>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <?php if ($result->num_rows > 0): ?>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-6 py-3 text-left text-sm font-semibold">Venue</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Date</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Time</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while ($booking = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($booking['facility_name']); ?></td>
                            <td class="px-6 py-4"><?php echo date('d/m/Y', strtotime($booking['booking_date'])); ?></td>
                            <td class="px-6 py-4"><?php echo date('h:i A', strtotime($booking['time_slot'])); ?></td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-green-500 text-white rounded-full text-sm">
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <?php if($booking['status'] == 'confirmed'): ?>
                                    <form action="" method="post" class="inline">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <button type="submit" name="cancel_booking" 
                                                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 mr-2">
                                            Cancel
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <?php if($booking['rating']): ?>
                                    <button onclick="openViewReviewModal(<?php echo $booking['id']; ?>, <?php echo $booking['rating']; ?>, '<?php echo addslashes($booking['comment']); ?>')" 
                                            class="bg-[#FDB338] text-black px-4 py-2 rounded hover:bg-[#FDB338]/80">
                                        View Review
                                    </button>
                                <?php else: ?>
                                    <button onclick="openReviewModal(<?php echo $booking['id']; ?>)" 
                                            class="bg-[#FDB338] text-black px-4 py-2 rounded hover:bg-[#FDB338]/80">
                                        Review
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-600">You have no bookings yet.</p>
        <?php endif; ?>

        <!-- Back Button -->
        <div class="mt-8 text-center">
            <a href="facilities.php" class="bg-black text-white px-8 py-3 rounded-md hover:bg-gray-800 inline-block">
                Back
            </a>
        </div>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center p-4">
        <div class="bg-white p-8 rounded-lg max-w-md w-full">
            <h2 class="text-2xl font-bold mb-4">Write a Review</h2>
            <form action="" method="post">
                <input type="hidden" id="reviewBookingId" name="booking_id" value="">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Rating</label>
                    <div class="flex space-x-2">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star text-gray-300 cursor-pointer star-rating" data-rating="<?php echo $i; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" id="ratingInput" name="rating" value="">
                </div>
                <div class="mb-4">
                    <label for="comment" class="block text-sm font-medium mb-2">Comment</label>
                    <textarea id="comment" name="comment" rows="3" class="w-full border rounded-md p-2" required></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeReviewModal()" 
                            class="px-4 py-2 border rounded-md hover:bg-gray-100">
                        Cancel
                    </button>
                    <button type="submit" name="submit_review" 
                            class="bg-[#FDB338] text-black px-4 py-2 rounded-md hover:bg-[#FDB338]/80">
                        Submit Review
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Review Modal -->
    <div id="viewReviewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center p-4">
        <div class="bg-white p-8 rounded-lg max-w-md w-full">
            <h2 class="text-2xl font-bold mb-4">Your Review</h2>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Rating</label>
                <div id="viewRating" class="flex space-x-2"></div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Comment</label>
                <p id="viewComment" class="text-gray-700"></p>
            </div>
            <div class="flex justify-end">
                <button onclick="closeViewReviewModal()" 
                        class="px-4 py-2 border rounded-md hover:bg-gray-100">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        function openReviewModal(bookingId) {
            document.getElementById('reviewBookingId').value = bookingId;
            document.getElementById('reviewModal').classList.remove('hidden');
            document.getElementById('reviewModal').classList.add('flex');
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').classList.remove('flex');
            document.getElementById('reviewModal').classList.add('hidden');
            // Reset form
            document.getElementById('ratingInput').value = '';
            document.getElementById('comment').value = '';
            document.querySelectorAll('.star-rating').forEach(star => {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            });
        }

        function openViewReviewModal(bookingId, rating, comment) {
            const viewRatingElement = document.getElementById('viewRating');
            viewRatingElement.innerHTML = '';
            for (let i = 1; i <= 5; i++) {
                const star = document.createElement('i');
                star.classList.add('fas', 'fa-star');
                star.classList.add(i <= rating ? 'text-yellow-400' : 'text-gray-300');
                viewRatingElement.appendChild(star);
            }
            document.getElementById('viewComment').textContent = comment;
            document.getElementById('viewReviewModal').classList.remove('hidden');
            document.getElementById('viewReviewModal').classList.add('flex');
        }

        function closeViewReviewModal() {
            document.getElementById('viewReviewModal').classList.remove('flex');
            document.getElementById('viewReviewModal').classList.add('hidden');
        }

        document.querySelectorAll('.star-rating').forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.dataset.rating;
                document.getElementById('ratingInput').value = rating;
                document.querySelectorAll('.star-rating').forEach((s, index) => {
                    if (index < rating) {
                        s.classList.remove('text-gray-300');
                        s.classList.add('text-yellow-400');
                    } else {
                        s.classList.remove('text-yellow-400');
                        s.classList.add('text-gray-300');
                    }
                });
            });
        });
    </script>
</body>
</html>