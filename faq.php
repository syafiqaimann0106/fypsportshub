<?php 
session_start();
include("php/config.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - IIUM SportsHub</title>
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
            <h1 class="text-4xl font-bold mb-4 text-center">Frequently Asked Questions</h1>
            <p class="text-xl text-center">Find answers to common questions about IIUM SportsHub</p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto space-y-4">
            <?php
            $faqs = [
                [
                    "question" => "How do I book a sports facility?",
                    "answer" => "To book a sports facility, log in to your account, navigate to the 'Facilities' page, select the desired facility and time slot, and follow the booking process. You'll receive a confirmation email once your booking is complete."
                ],
                [
                    "question" => "Can I cancel or reschedule my booking?",
                    "answer" => "Yes, you can cancel or reschedule your booking up to 24 hours before the reserved time. Go to 'MyBookings' in your account, find the relevant booking, and select the cancel or reschedule option."
                ],
                [
                    "question" => "Are there any fees for using the sports facilities?",
                    "answer" => "Most facilities are free for IIUM students and staff. However, some premium facilities or extended usage may incur a small fee. Check the facility details for specific pricing information."
                ],
                [
                    "question" => "Can non-IIUM members use the sports facilities?",
                    "answer" => "Non-IIUM members can use the facilities, but they need to be accompanied by an IIUM student or staff member. Additional fees may apply for non-members."
                ],
                [
                    "question" => "What should I bring when using the sports facilities?",
                    "answer" => "Please bring your IIUM ID, appropriate sports attire, and any necessary equipment (though some equipment may be available for rent). Don't forget to bring water and a towel."
                ],
                [
                    "question" => "How far in advance can I make a booking?",
                    "answer" => "You can make bookings up to 2 weeks in advance. This policy helps ensure fair access to facilities for all users."
                ]
            ];

            foreach ($faqs as $index => $faq):
            ?>
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <button class="flex items-center justify-between w-full p-4 text-left bg-yellow-500 hover:bg-yellow-400 transition-colors duration-300" onclick="toggleFaq(<?php echo $index; ?>)">
                        <span class="font-semibold"><?php echo htmlspecialchars($faq['question']); ?></span>
                        <svg class="w-6 h-6 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden p-4 bg-white">
                        <p><?php echo htmlspecialchars($faq['answer']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer class="bg-black text-white py-6 mt-8">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2023 IIUM Sports Facilities. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function toggleFaq(index) {
            const answer = document.querySelectorAll('.faq-answer')[index];
            const icon = document.querySelectorAll('.faq-answer')[index].previousElementSibling.querySelector('svg');
            answer.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }
    </script>
</body>
</html>