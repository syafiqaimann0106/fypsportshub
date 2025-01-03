<?php 
session_start();
include("php/config.php");

if(isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $subject = mysqli_real_escape_string($con, $_POST['subject']);
    $message = mysqli_real_escape_string($con, $_POST['message']);

    // Here you would typically send an email or save to database
    // For this example, we'll just set a success message
    $success_message = "Thank you for your message. We'll get back to you soon!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - IIUM SportsHub</title>
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
            <h1 class="text-4xl font-bold mb-4 text-center">Contact Us</h1>
            <p class="text-xl text-center">Get in touch with the IIUM SportsHub team</p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <?php if(isset($success_message)): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                        <p><?php echo $success_message; ?></p>
                    </div>
                <?php endif; ?>

                <form action="" method="post">
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
                        <input type="text" id="name" name="name" required 
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                        <input type="email" id="email" name="email" required 
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label for="subject" class="block text-gray-700 text-sm font-bold mb-2">Subject:</label>
                        <input type="text" id="subject" name="subject" required 
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-6">
                        <label for="message" class="block text-gray-700 text-sm font-bold mb-2">Message:</label>
                        <textarea id="message" name="message" required rows="5" 
                                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="submit" name="submit" 
                                class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-8 max-w-2xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h2 class="text-2xl font-bold mb-4">Contact Information</h2>
                <p class="mb-2"><strong>Address:</strong> IIUM Sports Complex, International Islamic University Malaysia, 53100 Gombak, Selangor, Malaysia</p>
                <p class="mb-2"><strong>Phone:</strong> +60 3-6196 4000</p>
                <p class="mb-2"><strong>Email:</strong> sportshub@iium.edu.my</p>
                <p class="mb-2"><strong>Operating Hours:</strong> Monday to Friday, 8:00 AM to 10:00 PM</p>
            </div>
        </div>
    </div>

    <footer class="bg-black text-white py-6 mt-8">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2023 IIUM Sports Facilities. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>