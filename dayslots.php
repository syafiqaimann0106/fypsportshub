<?php
session_start();
include("php/config.php");

if(!isset($_SESSION['valid'])){
  header("Location: index.php");
  exit();
}

$facility_id = isset($_GET['facility_id']) ? intval($_GET['facility_id']) : 0;

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

// Generate dates for the next 12 days
$dates = array();
for ($i = 0; $i < 12; $i++) {
  $date = date('Y-m-d', strtotime("+$i days"));
  $dates[] = array(
      'day' => date('D', strtotime($date)),
      'date' => date('d', strtotime($date)),
      'month' => date('M', strtotime($date)),
      'full' => $date
  );
}

// Define the time slots array before it's used
$all_slots = array(
  '08:00:00', '09:00:00', '10:00:00', '11:00:00', '12:00:00',
  '13:00:00', '14:00:00', '15:00:00', '16:00:00', '17:00:00',
  '18:00:00', '19:00:00', '20:00:00', '21:00:00', '22:00:00'
);

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
  <title><?php echo htmlspecialchars($facility['name']); ?> - IIUM SportsHub</title>
  <script src="https://cdn.tailwindcss.com"></script>
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
      .date-card {
          background-color: #f6b03e;
          border-radius: 12px;
          transition: transform 0.3s ease;
      }
      .date-card:hover {
          transform: scale(1.05);
          background-color: #e5a438;
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
          <h1 class="text-5xl font-bold mb-8">FACILITIES</h1>
      </div>
  </div>

  <main class="container mx-auto px-4 py-8">
      <?php
      $image_name = pathinfo($facility['image_url'], PATHINFO_FILENAME);
      $image_path = get_image_path($image_name);
      ?>
      <div class="mb-12">
          <img src="<?php echo htmlspecialchars($image_path); ?>" 
               alt="<?php echo htmlspecialchars($facility['name']); ?>" 
               class="w-full h-96 object-cover rounded-lg">
      </div>

      <h2 class="text-4xl font-bold text-center mb-6"><?php echo htmlspecialchars($facility['name']); ?></h2>

      <!-- New section for facility description and price -->
      <div class="bg-gray-100 p-6 rounded-lg mb-12">
          <p class="text-lg mb-4"><?php echo htmlspecialchars($facility['description'] ?? 'No description available.'); ?></p>
          <p class="text-xl font-semibold">Price: RM<?php echo number_format($facility['hourly_rate'], 2); ?> per hour</p>
      </div>

      <h3 class="text-2xl font-bold text-center mb-6">Available Dates</h3>
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
          <?php foreach ($dates as $date): ?>
              <a href="booking.php?facility_id=<?php echo $facility['id']; ?>&date=<?php echo $date['full']; ?>" 
                 class="date-card p-4 text-center text-white hover:shadow-lg">
                  <div class="text-lg font-bold"><?php echo $date['day']; ?></div>
                  <div class="text-3xl font-bold"><?php echo $date['date']; ?></div>
                  <div class="text-lg"><?php echo $date['month']; ?></div>
              </a>
          <?php endforeach; ?>
      </div>

      <div class="text-center">
          <a href="facilities.php" class="inline-block px-8 py-3 bg-black text-white rounded-lg hover:bg-gray-800">Back</a>
      </div>
  </main>

  <footer class="bg-black text-white py-6 mt-8">
      <div class="container mx-auto px-6 text-center">
          <p>&copy; 2023 IIUM Sports Facilities. All rights reserved.</p>
      </div>
  </footer>
</body>
</html>

