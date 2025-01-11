<?php
session_start();
include("php/config.php");

if(!isset($_SESSION['valid'])){
    header("Location: index.php");
    exit();
}

// Fetch facilities from the database
$query = "SELECT * FROM facilities";
$result = mysqli_query($con, $query);

// Check for query execution errors
if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

function get_image_path($image_name) {
    $extensions = ['jpg', 'png'];
    foreach ($extensions as $ext) {
        $path = "img/{$image_name}.{$ext}";
        if (file_exists($path)) {
            return $path;
        }
    }
    return "img/placeholder.png";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facilities - IIUM SportsHub</title>
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
        .facility-card {
            border-radius: 24px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .facility-card:hover {
            transform: scale(1.02);
        }
        .search-container {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
        }
        .search-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
        }
    </style>
</head>
<body class="bg-white">
    <?php include 'header.php'; ?>

    <div class="hero-bg text-white py-20">
        <div class="hero-bubble" style="width: 300px; height: 300px; top: -100px; right: -50px; opacity: 0.8;"></div>
        <div class="hero-bubble" style="width: 200px; height: 200px; bottom: -100px; left: 10%; opacity: 0.6;"></div>
        <div class="hero-bubble" style="width: 150px; height: 150px; top: 20%; left: 30%; opacity: 0.4;"></div>
        <div class="container mx-auto px-4 relative z-10">
            <h1 class="text-6xl font-bold mb-8 text-center">FACILITIES</h1>
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search" 
                       class="w-full px-4 py-2 rounded-full text-gray-800 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <i class="fas fa-search search-icon text-gray-400"></i>
            </div>
        </div>
    </div>

    <main class="container mx-auto px-4 py-12">
        <h2 class="text-3xl font-bold text-center mb-12">Quick Access</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php while($facility = mysqli_fetch_assoc($result)): ?>
                <a href="dayslots.php?facility_id=<?php echo $facility['id']; ?>" class="facility-card shadow-lg block">
                    <?php
                    $image_name = pathinfo($facility['image_url'], PATHINFO_FILENAME);
                    $image_path = get_image_path($image_name);
                    ?>
                    <div class="aspect-w-16 aspect-h-9">
                        <img src="<?php echo htmlspecialchars($image_path); ?>" 
                             alt="<?php echo htmlspecialchars($facility['name']); ?>" 
                             class="w-full h-48 object-cover">
                    </div>
                    <div class="p-4 text-center bg-white">
                        <h3 class="font-bold text-lg"><?php echo htmlspecialchars($facility['name']); ?></h3>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
    </main>
    
    <footer class="bg-black text-white py-6 mt-8">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2023 IIUM Sports Facilities. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function searchFacilities() {
            var input = document.getElementById('searchInput');
            var filter = input.value.toUpperCase();
            var grid = document.querySelector('.grid');
            var cards = grid.getElementsByClassName('facility-card');

            for (var i = 0; i < cards.length; i++) {
                var title = cards[i].querySelector('h3');
                var txtValue = title.textContent || title.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    cards[i].style.display = "";
                } else {
                    cards[i].style.display = "none";
                }
            }
        }

        document.getElementById('searchInput').addEventListener('keyup', searchFacilities);
    </script>
</body>
</html>