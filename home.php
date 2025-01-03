<?php 
session_start();
include("php/config.php");

if(!isset($_SESSION['valid'])){
   header("Location: index.php");
}
?>

<html>
<head>
 <title>IIUM Sports Facilities</title>
 <script src="https://cdn.tailwindcss.com"></script>
 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
 <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>
 <style>
     body {
         font-family: 'Roboto', sans-serif;
     }
 </style>
</head>
<body class="bg-gray-100">
 <header class="bg-black text-white">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">
        <div class="flex items-center">
            <img src="img/iium.png" alt="IIUM Logo" class="h-10 w-10" width="50" height="50"/>
            <nav class="ml-6">
                <a href="home.php" class="text-white px-4 py-2 hover:underline">Home</a>
                <a href="facilities.php" class="text-white px-4 py-2 hover:underline">Facilities</a>
                <a href="mybookings.php" class="text-white px-4 py-2 hover:underline">MyBookings</a>
                <a href="contactus.php" class="text-white px-4 py-2 hover:underline">Contact Us</a>
                <a href="faq.php" class="text-white px-4 py-2 hover:underline">FAQ</a>
            </nav>
        </div>
        <div>
            <a href="change.php">
                <?php
                $profile_picture = "img/default_profile.png";
                if(isset($_SESSION['id'])) {
                    $id = $_SESSION['id'];
                    $query_picture = mysqli_query($con, "SELECT profile_picture FROM users WHERE id=$id");
                    if($result_picture = mysqli_fetch_assoc($query_picture)){
                        if($result_picture['profile_picture']){
                            $profile_picture = "uploads/" . $result_picture['profile_picture'];
                        }
                    }
                }
                ?>
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="h-10 w-10 rounded-full object-cover">
            </a>
        </div>
    </div>
</header>
 <section class="relative">
 <img src="img/uia.png" alt="Aerial view of IIUM campus" class="w-full h-96 object-cover" width="1920" height="600"/>
     <div class="absolute inset-0 flex flex-col justify-center items-center text-white">
         <h1 class="text-4xl font-bold">IIUM SPORTS FACILITIES</h1>
         <a href="facilities.php">
             <button class="mt-4 bg-yellow-500 text-black px-6 py-2 rounded">Book Now</button>
         </a>
     </div>
 </section>
 <section class="container mx-auto py-12 px-6">
     <h2 class="text-3xl font-bold mb-4">Background</h2>
     <div class="flex flex-wrap">
         <div class="w-full lg:w-2/3">
             <p class="mb-4">The sports section in IIUM was established in 1983. Since then, sports have become vibrant activities in IIUM. In 1990, the sports activities in IIUM have drastically increased in term of quantity and quality. In 1991, the role of sports section became more demanding when IIUM was indirectly involved with the World University Sports Federation (FISU), Asian University Sports Federation (AUSF) and Asean University Sports Council (AUSC).</p>
             <p class="mb-4">The primary purpose of Sports Development Centre (SDC) is to provide opportunities for students and staff to pursue their sports and recreational interest. The focus of SDC programmes are blending and learning new motor skill while coordinating all sports activities, monitor the progress of IIUM sports development and supervise the maintenance of venues and sports facilities of the university.</p>
             <p class="mb-4">IIUM Gombak has two (2) Sports Complexes, each for male and female community. They are located at the northern side of the campus. The university has also sports facilities in other campuses i.e Kuantan, Gombak and Petaling Jaya. The sports facilities are available for all students and staff of the IIUM as well as visitors. All bookings can be made through the office of SDC with minimal fees charge.</p>
         </div>
         <div class="w-full lg:w-1/3 lg:pl-6">
             <img src="img/group.png" alt="Group of IIUM students in sports attire" class="rounded-lg mt-4 lg:mt-0" width="450" height="450"/>
         </div>
     </div>
 </section>
 <section class="bg-white py-12">
     <div class="container mx-auto px-6">
         <h2 class="text-3xl font-bold mb-4">IIUM SPORTS FACILITIES</h2>
         <div class="flex flex-wrap">
             <div class="w-full lg:w-1/2">
                 <table class="table-auto w-full mb-6">
                     <thead>
                         <tr>
                             <th class="px-4 py-2">FACILITIES</th>
                             <th class="px-4 py-2">MALE SPORTS COMPLEX</th>
                             <th class="px-4 py-2">FEMALE SPORTS COMPLEX</th>
                         </tr>
                     </thead>
                     <tbody>
                         <tr>
                             <td class="border px-4 py-2">Multipurpose Hall</td>
                             <td class="border px-4 py-2">1</td>
                             <td class="border px-4 py-2">1</td>
                         </tr>
                         <tr>
                             <td class="border px-4 py-2">Badminton Court</td>
                             <td class="border px-4 py-2">6</td>
                             <td class="border px-4 py-2">4</td>
                         </tr>
                         <tr>
                             <td class="border px-4 py-2">Squash Court</td>
                             <td class="border px-4 py-2">4</td>
                             <td class="border px-4 py-2">3</td>
                         </tr>
                         <tr>
                             <td class="border px-4 py-2">Tennis Court</td>
                             <td class="border px-4 py-2">4</td>
                             <td class="border px-4 py-2">3</td>
                         </tr>
                         <tr>
                             <td class="border px-4 py-2">Netball Court</td>
                             <td class="border px-4 py-2">2</td>
                             <td class="border px-4 py-2">2</td>
                         </tr>
                         <tr>
                             <td class="border px-4 py-2">Basketball Court</td>
                             <td class="border px-4 py-2">2</td>
                             <td class="border px-4 py-2">1</td>
                         </tr>
                         <tr>
                             <td class="border px-4 py-2">Stadium with Track & Field</td>
                             <td class="border px-4 py-2">1</td>
                             <td class="border px-4 py-2">1</td>
                         </tr>
                         <tr>
                             <td class="border px-4 py-2">Football Field/Rugby Field</td>
                             <td class="border px-4 py-2">2</td>
                             <td class="border px-4 py-2">1</td>
                         </tr>
                         <tr>
                             <td class="border px-4 py-2">Swimming Pool</td>
                             <td class="border px-4 py-2">1</td>
                             <td class="border px-4 py-2">1</td>
                         </tr>
                         <tr>
                             <td class="border px-4 py-2">Futsal Court (Outdoor)</td>
                             <td class="border px-4 py-2">1</td>
                             <td class="border px-4 py-2">1</td>
                         </tr>
                         <tr>
                             <td class="border px-4 py-2">Mugger/Cricket</td>
                             <td class="border px-4 py-2">1</td>
                             <td class="border px-4 py-2">1</td>
                         </tr>
                     </tbody>
                 </table>
             </div>
             <div class="w-full lg:w-1/2">
                 <h3 class="text-2xl font-bold mb-4">Access</h3>
                 <p class="mb-4">Students and IIUM community are the primary users of the Sports Complexes at the discretion of the Deputy Rector, Student Affairs and the Director of Sports and Recreational Centre. Outside users may reserve and apply to use the facilities at other times.</p>
                 <p class="mb-4">The scheduling of facilities are subject to University and individual usage policies. All users are expected to comply by the rules and regulations established for the use of each facility by the Sports and Recreational Centre.</p>
             </div>
         </div>
     </div>
 </section>
 <section class="bg-gray-200 py-12">
     <div class="container mx-auto px-6">
         <h2 class="text-3xl font-bold mb-4">Vision</h2>
         <p class="mb-4">To become an excellent sports and recreational centre that provides states-of art facilities and services and develop athletes with holistic personality.</p>
     </div>
 </section>
 <section class="container mx-auto py-12 px-6">
     <h2 class="text-3xl font-bold mb-4">Mission</h2>
     <div class="flex flex-wrap">
         <div class="w-full lg:w-1/2">
             <div class="flex items-center mb-4">
                 <span class="text-yellow-500 text-4xl font-bold mr-4">S</span>
                 <p>Sports culture as a norm to IIUM community.</p>
             </div>
             <div class="flex items-center mb-4">
                 <span class="text-yellow-500 text-4xl font-bold mr-4">P</span>
                 <p>Providing excellent facilities and services.</p>
             </div>
             <div class="flex items-center mb-4">
                 <span class="text-yellow-500 text-4xl font-bold mr-4">O</span>
                 <p>Organizing sports event at all levels.</p>
             </div>
             <div class="flex items-center mb-4">
                 <span class="text-yellow-500 text-4xl font-bold mr-4">R</span>
                 <p>Realizing the vision of producing athletes with holistic personality.</p>
             </div>
             <div class="flex items-center mb-4">
                 <span class="text-yellow-500 text-4xl font-bold mr-4">T</span>
                 <p>Teampower at its best.</p>
             </div>
             <div class="flex items-center mb-4">
                 <span class="text-yellow-500 text-4xl font-bold mr-4">S</span>
                 <p>Sports hub and referral centre to all.</p>
             </div>
         </div>
     </div>
 </section>
 <footer class="bg-black text-white py-6">
     <div class="container mx-auto px-6 text-center">
         <p>&copy; 2023 IIUM Sports Facilities. All rights reserved.</p>
     </div>
 </footer>
</body>
</html>