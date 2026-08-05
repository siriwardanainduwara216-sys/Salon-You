<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';
$user_name = $_SESSION['user_name'] ?? 'Guest User';

// Fetch services from database
require_once 'config.php';
$services_result = mysqli_query($conn, "SELECT * FROM services ORDER BY id ASC");
$services = [];
while ($row = mysqli_fetch_assoc($services_result)) {
    $services[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salon You - Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        salonDark: '#0F172A',
                        salonSurface: '#1E293B',
                        youAmber: '#F59E0B',
                        youPurple: '#8B5CF6',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .app-page { display: none; }
        .app-page.active { display: block; }
        .animate-fadeIn { animation: fadeIn 0.5s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        @keyframes scrollLeft {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
@keyframes scrollRight {
    0% { transform: translateX(-50%); }
    100% { transform: translateX(0); }
}
.animate-scroll-left {
    animation: scrollLeft 25s linear infinite;
}
.animate-scroll-right {
    animation: scrollRight 25s linear infinite;
}
.animate-scroll-left:hover,
.animate-scroll-right:hover {
    animation-play-state: paused;
}
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col">

    <header class="bg-salonSurface/80 backdrop-blur-md border-b border-slate-800 sticky top-0 z-40 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-3 cursor-pointer" onclick="navigateTo('home')">
            <div class="bg-gradient-to-r from-youAmber to-youPurple w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-youAmber/20">
                <i class="fas fa-sparkles text-white text-lg"></i>
            </div>
            <span class="text-xl font-bold tracking-wider bg-gradient-to-r from-youAmber to-youPurple bg-clip-text text-transparent">SALON YOU</span>
        </div>
        
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-300" id="main-nav">
            <button onclick="navigateTo('home')" class="hover:text-youAmber transition-colors text-white">Home</button>
            <button onclick="navigateTo('services')" class="hover:text-youAmber transition-colors">Services</button>
            <button onclick="navigateTo('gallery')" class="hover:text-youAmber transition-colors">Gallery</button>
            <button onclick="navigateTo('about')" class="hover:text-youAmber transition-colors">About Us</button>
            <button onclick="navigateTo('contact')" class="hover:text-youAmber transition-colors">Contact</button>
        </nav>

        <div class="flex items-center gap-4">
            <div id="guest-auth-actions" class="<?php echo $is_logged_in ? 'hidden' : 'flex'; ?> items-center gap-3">
               <button type="button" onclick="navigateTo('login')" class="text-slate-400 hover:text-youAmber transition-colors duration-200 text-sm font-medium cursor-pointer">Login</button>
                <button type="button" onclick="navigateTo('register')" class="bg-gradient-to-r from-youAmber to-youPurple text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:opacity-90 shadow-md shadow-purple-500/10 transition-all">Register</button>
            </div>
            
            <div id="logged-user-profile" class="<?php echo $is_logged_in ? 'flex' : 'hidden'; ?> items-center gap-3 border-l border-slate-800 pl-4">
                <div class="text-right">
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider" id="badge-role-display"><?php echo htmlspecialchars($user_role); ?></p>
                    <p class="text-sm font-bold text-white" id="name-profile-display"><?php echo htmlspecialchars($user_name); ?></p>
                </div>
                <div id="avatar-circle" class="w-10 h-10 rounded-full bg-gradient-to-tr from-youAmber to-youPurple border-2 border-slate-800 flex items-center justify-center font-bold text-sm text-white">
                    <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                </div>
                <button onclick="processLogout()" class="text-slate-400 hover:text-rose-500 transition-colors ml-2" title="Logout"><i class="fas fa-sign-out-alt text-lg"></i></button>
            </div>
        </div>
    </header>

    <div class="flex-1 flex flex-col">
        <div id="main-application" class="flex-1 flex flex-col">

            <!-- HOME PAGE -->
            <div id="page-home" class="app-page <?php echo !$is_logged_in ? 'active' : ''; ?> flex-1 max-w-7xl w-full mx-auto px-6 py-12 space-y-20">
                <div class="grid lg:grid-cols-2 gap-12 items-center pt-6">
                    <div class="space-y-6">
                        <span class="bg-gradient-to-r from-youAmber/10 to-youPurple/10 border border-youAmber/30 text-youAmber text-xs uppercase font-bold tracking-widest px-4 py-1.5 rounded-full">Welcome to Salon You</span>
                        <h1 class="text-5xl lg:text-6xl font-extrabold tracking-tight leading-none">WELCOME TO<br><span class="bg-gradient-to-r from-youAmber via-orange-500 to-youPurple bg-clip-text text-transparent">SALON YOU</span></h1>
                        <p class="text-slate-400 text-lg">Book your next appointment today.</p>
                        <div class="flex items-center gap-4 pt-2">
                            <button onclick="navigateTo('services')" class="bg-gradient-to-r from-youAmber to-youPurple text-white px-8 py-3.5 rounded-xl font-semibold shadow-lg shadow-youAmber/30 transition-transform hover:scale-[1.02]">View Services</button>
                            <button onclick="handleBookingIntent()" class="bg-slate-800 hover:bg-slate-700 text-white px-8 py-3.5 rounded-xl font-semibold border border-slate-700 transition-colors">Book Appointment</button>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-salonSurface to-slate-800 p-8 rounded-3xl border border-slate-800 shadow-2xl relative">
                        <h3 class="text-xl font-bold mb-4 flex items-center gap-2"><i class="fas fa-sparkles text-amber-400"></i> Active Smart Feature</h3>
                        <p class="text-sm text-slate-400 mb-4">Upload a selfie to instantly match your look using our new visual try-on modules.</p>
                        <button onclick="handleBookingIntent()" class="w-full py-3 bg-slate-900 hover:bg-slate-950 text-youAmber font-bold text-sm rounded-xl border border-slate-800 transition-colors"><i class="fas fa-robot mr-2"></i> Try AI Hairstyle Recommender</button>
                    </div>
                </div>

                <div>
                    <h3 class="text-2xl font-bold mb-6">Featured Staff</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-salonSurface p-6 rounded-2xl border border-slate-800 text-center">
                            <div class="w-20 h-20 bg-gradient-to-tr from-youAmber to-youPurple rounded-full mx-auto mb-4 flex items-center justify-center font-bold text-xl text-white">SF</div>
                            <h4 class="text-lg font-bold text-white">Sarah Fernando</h4>
                            <p class="text-xs text-youAmber font-semibold mb-2">Senior Hair Stylist</p>
                            <p class="text-xs text-slate-400">8 Years Experience</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-2xl font-bold mb-6">Reviews</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-salonSurface p-6 rounded-2xl border border-slate-800">
                            <div class="flex text-amber-400 gap-1 mb-2"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            <p class="text-sm text-slate-300 italic">"The AI Consultation accurately evaluated my face shape and suggested a perfect new look!"</p>
                            <p class="text-xs text-slate-500 mt-4 font-bold">- Verified Customer</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SERVICES PAGE -->
            <div id="page-services" class="app-page flex-1 max-w-7xl w-full mx-auto px-6 py-12">
                <h2 class="text-3xl font-extrabold mb-2 bg-gradient-to-r from-youAmber to-youPurple bg-clip-text text-transparent">Our Services</h2>
                <p class="text-slate-400 mb-8">Premium grooming selections calibrated precisely to your requirements.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php
                    $icons = ['fa-cut', 'fa-paint-brush', 'fa-spa', 'fa-hand-sparkles', 'fa-star', 'fa-magic'];
                    $colors = ['text-youAmber', 'text-youPurple', 'text-pink-400', 'text-emerald-400', 'text-blue-400', 'text-rose-400'];
                    foreach ($services as $i => $service):
                        $duration = $service['duration_mins'] >= 60
                            ? round($service['duration_mins'] / 60, 1) . ' Hour' . ($service['duration_mins'] >= 120 ? 's' : '')
                            : $service['duration_mins'] . ' Minutes';
                        $icon = $icons[$i % count($icons)];
                        $color = $colors[$i % count($colors)];
                    ?>
                    <div class="bg-salonSurface p-6 rounded-2xl border border-slate-800 flex flex-col justify-between">
                        <div>
                            <div class="text-2xl <?php echo $color; ?> mb-3"><i class="fas <?php echo $icon; ?>"></i></div>
                            <h3 class="text-xl font-bold text-white mb-1"><?php echo htmlspecialchars($service['service_name']); ?></h3>
                            <p class="text-xs text-slate-400"><i class="far fa-clock mr-1"></i> <?php echo $duration; ?></p>
                        </div>
                        <div class="mt-6 flex justify-between items-center">
                            <span class="text-2xl font-black text-youAmber">Rs. <?php echo number_format($service['price']); ?></span>
                            <button onclick="handleBookingIntent(<?php echo $service['id']; ?>)" class="px-3 py-1.5 bg-slate-800 text-xs rounded-lg font-bold hover:bg-slate-700">Book</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

  <div id="page-gallery" class="app-page flex-1 w-full mx-auto px-6 py-12 space-y-16">

    <!-- Header -->
    <div class="max-w-7xl mx-auto">
        <span class="text-xs uppercase font-black tracking-widest text-youAmber border border-youAmber/30 bg-youAmber/10 px-3 py-1.5 rounded-full">Our Work</span>
        <h2 class="text-4xl font-extrabold mt-4 bg-gradient-to-r from-youAmber to-youPurple bg-clip-text text-transparent">Transformation Gallery</h2>
        <p class="text-slate-400 mt-2">Every cut tells a story. Explore our finest work.</p>
    </div>

    <!-- SECTION 1: Before & After Carousel -->
    <div class="max-w-7xl mx-auto w-full">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-1 h-6 bg-gradient-to-b from-youAmber to-youPurple rounded-full"></div>
            <h3 class="text-xl font-bold text-white">Before & After Transformations</h3>
        </div>

        <div class="relative overflow-hidden rounded-3xl border border-slate-800 bg-slate-950 shadow-2xl">
            <div class="flex transition-transform duration-500 ease-in-out" id="carousel-track">

                <?php
                $transformations = [
                    ['name' => 'Modern Textured Crop', 'stylist' => 'Sarah Fernando', 'review' => '"AI recommendation was spot on — love my new look!"', 'stars' => 5, 'before_color' => 'from-slate-700 to-slate-900', 'after_color' => 'from-youAmber/30 to-slate-900', 'before_label' => 'Overgrown & Flat', 'after_label' => 'Modern Textured Crop'],
                    ['name' => 'Low Fade Transformation', 'stylist' => 'Sarah Fernando', 'review' => '"Clean, sharp and exactly what I wanted!"', 'stars' => 5, 'before_color' => 'from-slate-700 to-slate-900', 'after_color' => 'from-youPurple/30 to-slate-900', 'before_label' => 'Uneven Growth', 'after_label' => 'Low Fade'],
                    ['name' => 'Classic Quiff', 'stylist' => 'Sarah Fernando', 'review' => '"The quiff looks amazing — very professional!"', 'stars' => 4, 'before_color' => 'from-slate-700 to-slate-900', 'after_color' => 'from-pink-500/30 to-slate-900', 'before_label' => 'Flat Unstyled', 'after_label' => 'Classic Quiff'],
                    ['name' => 'Side Part Style', 'stylist' => 'Sarah Fernando', 'review' => '"Perfect side part — very clean and polished!"', 'stars' => 5, 'before_color' => 'from-slate-700 to-slate-900', 'after_color' => 'from-emerald-500/30 to-slate-900', 'before_label' => 'Messy Unstyled', 'after_label' => 'Side Part'],
                ];
                foreach ($transformations as $t):
                ?>
                <div class="min-w-full">
                    <div class="grid grid-cols-1 md:grid-cols-2">
                        <div class="relative h-80 bg-gradient-to-br <?php echo $t['before_color']; ?> flex items-end overflow-hidden group">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-24 h-24 rounded-full bg-slate-700/50 border-2 border-slate-600 flex items-center justify-center">
                                    <i class="fas fa-user text-4xl text-slate-500"></i>
                                </div>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent"></div>
                            <div class="relative z-10 p-4 w-full">
                                <span class="text-xs font-black text-rose-400 border border-rose-400/30 bg-rose-400/10 px-2 py-1 rounded-md uppercase tracking-widest">Before</span>
                                <p class="text-white font-bold mt-1"><?php echo $t['before_label']; ?></p>
                            </div>
                        </div>
                        <div class="relative h-80 bg-gradient-to-br <?php echo $t['after_color']; ?> flex items-end overflow-hidden group">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-24 h-24 rounded-full bg-youAmber/20 border-2 border-youAmber/40 flex items-center justify-center">
                                    <i class="fas fa-cut text-4xl text-youAmber/70"></i>
                                </div>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent"></div>
                            <div class="relative z-10 p-4 w-full">
                                <span class="text-xs font-black text-emerald-400 border border-emerald-400/30 bg-emerald-400/10 px-2 py-1 rounded-md uppercase tracking-widest">After</span>
                                <p class="text-white font-bold mt-1"><?php echo $t['after_label']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 border-t border-slate-800 flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <h4 class="text-lg font-bold text-white"><?php echo $t['name']; ?></h4>
                            <p class="text-xs text-slate-400 mt-0.5">Styled by <?php echo $t['stylist']; ?></p>
                            <p class="text-sm text-slate-400 mt-2 italic"><?php echo $t['review']; ?></p>
                        </div>
                        <div class="flex gap-1">
                            <?php for($s=1;$s<=5;$s++): ?>
                            <i class="fa<?php echo $s<=$t['stars']?'s':'r'; ?> fa-star text-youAmber text-xs"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>

            <button onclick="moveCarousel(-1)" class="absolute left-3 top-36 z-10 w-10 h-10 bg-black/60 hover:bg-youAmber/20 backdrop-blur-sm border border-slate-700 hover:border-youAmber/40 rounded-full flex items-center justify-center text-white transition-all hover:scale-110">
                <i class="fas fa-chevron-left text-sm"></i>
            </button>
            <button onclick="moveCarousel(1)" class="absolute right-3 top-36 z-10 w-10 h-10 bg-black/60 hover:bg-youAmber/20 backdrop-blur-sm border border-slate-700 hover:border-youAmber/40 rounded-full flex items-center justify-center text-white transition-all hover:scale-110">
                <i class="fas fa-chevron-right text-sm"></i>
            </button>
        </div>

        <div class="flex justify-center gap-2 mt-4" id="carousel-dots">
            <?php foreach($transformations as $i => $t): ?>
            <button onclick="goToSlide(<?php echo $i; ?>)" class="carousel-dot w-2.5 h-2.5 rounded-full transition-all <?php echo $i===0 ? 'bg-youAmber scale-125' : 'bg-slate-600'; ?>"></button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- SECTION 2: Auto-Scrolling Hairstyle Showcase -->
    <div class="w-full overflow-hidden">
        <div class="max-w-7xl mx-auto mb-6">
            <div class="flex items-center gap-3">
                <div class="w-1 h-6 bg-gradient-to-b from-youPurple to-pink-500 rounded-full"></div>
                <h3 class="text-xl font-bold text-white">Hairstyle Showcase</h3>
                <span class="text-xs text-slate-500 font-mono">— auto scrolling</span>
            </div>
        </div>

        <!-- Row 1: Left to Right -->
        <div class="relative overflow-hidden mb-4">
            <div class="flex gap-4 animate-scroll-left" style="width: max-content;">
                <?php
                $styles = [
                    ['name' => 'Modern Crop', 'tag' => 'Trending', 'img' => 'https://images.unsplash.com/photo-1621605815971-fbc98d665033?w=400&q=80'],
                    ['name' => 'Low Fade', 'tag' => 'Popular', 'img' => 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=400&q=80'],
                    ['name' => 'Quiff', 'tag' => 'Classic', 'img' => 'https://images.unsplash.com/photo-1605497788044-5a32c7078486?w=400&q=80'],
                    ['name' => 'Side Part', 'tag' => 'Elegant', 'img' => 'https://images.unsplash.com/photo-1622286342621-4bd786c2447c?w=400&q=80'],
                    ['name' => 'Pompadour', 'tag' => 'Bold', 'img' => 'https://images.unsplash.com/photo-1599351431202-1e0f0137899a?w=400&q=80'],
                    ['name' => 'Buzz Cut', 'tag' => 'Sharp', 'img' => 'https://images.unsplash.com/photo-1567894340315-735d7c361db0?w=400&q=80'],
                ];
                $allStyles = array_merge($styles, $styles); // Duplicated for seamless marquee loop
                foreach($allStyles as $style):
                ?>
                <div class="flex-shrink-0 w-48 h-56 rounded-2xl border border-slate-800 overflow-hidden relative group cursor-pointer hover:border-slate-600 transition-all hover:scale-105">
                    <img src="<?php echo $style['img']; ?>" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                         alt="<?php echo $style['name']; ?>"
                         onerror="this.parentElement.style.background='linear-gradient(135deg,#1e293b,#0f172a)'">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
                    <div class="absolute top-3 right-3 text-[9px] font-black text-youAmber bg-black/60 backdrop-blur-sm border border-youAmber/30 px-2 py-0.5 rounded-full uppercase tracking-wider"><?php echo $style['tag']; ?></div>
                    <div class="absolute bottom-0 inset-x-0 p-3">
                        <p class="text-sm font-bold text-white"><?php echo $style['name']; ?></p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Salon You Signature</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Row 2: Right to Left -->
        <div class="relative overflow-hidden">
            <div class="flex gap-4 animate-scroll-right" style="width: max-content;">
                <?php
                $styles2 = [
                    ['name' => 'Textured Fringe', 'tag' => 'New', 'img' => 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=400&q=80'],
                    ['name' => 'Caesar Cut', 'tag' => 'Classic', 'img' => 'https://images.unsplash.com/photo-1585747860715-2ba37e788b70?w=400&q=80'],
                    ['name' => 'Slick Back', 'tag' => 'Sleek', 'img' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&q=80'],
                    ['name' => 'Undercut', 'tag' => 'Edgy', 'img' => 'https://images.unsplash.com/photo-1562004760-aceed7bb0fe3?w=400&q=80'],
                    ['name' => 'Taper Fade', 'tag' => 'Hot', 'img' => 'https://images.unsplash.com/photo-1493256338651-d82f7acb2b38?w=400&q=80'],
                    ['name' => 'French Crop', 'tag' => 'Chic', 'img' => 'https://images.unsplash.com/photo-1519345182560-3f2917c472ef?w=400&q=80'],
                ];
                $allStyles2 = array_merge($styles2, $styles2); // Duplicated for seamless marquee loop
                foreach($allStyles2 as $style):
                ?>
                <div class="flex-shrink-0 w-48 h-56 rounded-2xl border border-slate-800 overflow-hidden relative group cursor-pointer hover:border-slate-600 transition-all hover:scale-105">
                    <img src="<?php echo $style['img']; ?>" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                         alt="<?php echo $style['name']; ?>"
                         onerror="this.parentElement.style.background='linear-gradient(135deg,#1e293b,#0f172a)'">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
                    <div class="absolute top-3 right-3 text-[9px] font-black text-youPurple bg-black/60 backdrop-blur-sm border border-youPurple/30 px-2 py-0.5 rounded-full uppercase tracking-wider"><?php echo $style['tag']; ?></div>
                    <div class="absolute bottom-0 inset-x-0 p-3">
                        <p class="text-sm font-bold text-white"><?php echo $style['name']; ?></p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Salon You Signature</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- SECTION 3: Style of the Month Spotlight -->
    <div class="max-w-7xl mx-auto w-full">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-1 h-6 bg-gradient-to-b from-youAmber to-orange-500 rounded-full"></div>
            <h3 class="text-xl font-bold text-white">Style of the Month</h3>
            <span class="text-xs font-black text-youAmber bg-youAmber/10 border border-youAmber/20 px-3 py-1 rounded-full uppercase tracking-wider animate-pulse">June 2026</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Spotlight -->
            <div class="lg:col-span-2 bg-gradient-to-br from-youAmber/10 via-slate-950 to-youPurple/10 rounded-3xl border border-youAmber/20 p-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-youAmber/5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-youPurple/5 rounded-full blur-3xl"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-tr from-youAmber to-orange-500 rounded-2xl flex items-center justify-center shadow-lg shadow-youAmber/30">
                            <i class="fas fa-crown text-white text-lg"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-youAmber font-black uppercase tracking-widest">Style of the Month</p>
                            <h4 class="text-2xl font-black text-white">Modern Textured Crop</h4>
                        </div>
                    </div>
                    <p class="text-slate-300 text-sm leading-relaxed mb-6">The Modern Textured Crop has taken Colombo by storm this June. Its versatile styling, low maintenance, and sharp finish make it the most requested cut at Salon You this month.</p>
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="bg-slate-900/60 rounded-xl p-3 border border-slate-800 text-center">
                            <p class="text-2xl font-black text-youAmber">87</p>
                            <p class="text-[10px] text-slate-400 mt-1">Bookings This Month</p>
                        </div>
                        <div class="bg-slate-900/60 rounded-xl p-3 border border-slate-800 text-center">
                            <p class="text-2xl font-black text-youPurple">4.9</p>
                            <p class="text-[10px] text-slate-400 mt-1">Avg Rating</p>
                        </div>
                        <div class="bg-slate-900/60 rounded-xl p-3 border border-slate-800 text-center">
                            <p class="text-2xl font-black text-emerald-400">30m</p>
                            <p class="text-[10px] text-slate-400 mt-1">Avg Duration</p>
                        </div>
                    </div>
                    <button onclick="handleBookingIntent()" class="px-6 py-3 bg-gradient-to-r from-youAmber to-orange-500 text-white text-sm font-bold rounded-xl shadow-lg shadow-youAmber/20 hover:opacity-90 transition-all hover:scale-[1.02]">
                        <i class="fas fa-calendar-plus mr-2"></i> Book This Style
                    </button>
                </div>
            </div>

            <!-- Runner Ups -->
            <div class="space-y-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Runner Ups</p>
                <?php
                $runners = [
                    ['name' => 'Low Fade', 'bookings' => 64, 'rating' => '4.8', 'color' => 'text-youPurple', 'bg' => 'from-youPurple/10'],
                    ['name' => 'Classic Quiff', 'bookings' => 51, 'rating' => '4.7', 'color' => 'text-pink-400', 'bg' => 'from-pink-500/10'],
                    ['name' => 'Side Part', 'bookings' => 43, 'rating' => '4.9', 'color' => 'text-emerald-400', 'bg' => 'from-emerald-500/10'],
                ];
                foreach($runners as $i => $r):
                ?>
                <div class="bg-gradient-to-r <?php echo $r['bg']; ?> to-slate-950 rounded-2xl border border-slate-800 p-4 flex items-center gap-4 hover:border-slate-700 transition-all cursor-pointer">
                    <div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center font-black text-slate-400 text-sm">#<?php echo $i+2; ?></div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-white"><?php echo $r['name']; ?></p>
                        <p class="text-[10px] text-slate-500"><?php echo $r['bookings']; ?> bookings</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold <?php echo $r['color']; ?>"><?php echo $r['rating']; ?></p>
                        <i class="fas fa-star text-youAmber text-[10px]"></i>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- SECTION 4: Customer Reviews -->
    <div class="max-w-7xl mx-auto w-full">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-1 h-6 bg-gradient-to-b from-pink-400 to-rose-500 rounded-full"></div>
            <h3 class="text-xl font-bold text-white">Customer Reviews</h3>
            <div class="flex items-center gap-1 ml-2">
                <i class="fas fa-star text-youAmber text-xs"></i>
                <span class="text-sm font-bold text-youAmber">4.9</span>
                <span class="text-xs text-slate-500 ml-1">from 200+ reviews</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php
            $reviews = [
                ['name' => 'Kamal Perera', 'initials' => 'KP', 'style' => 'Modern Textured Crop', 'rating' => 5, 'text' => 'The AI recommendation was incredibly accurate! Walked in not knowing what I wanted, walked out looking amazing. Sarah is brilliant.', 'color' => 'from-youAmber to-orange-500', 'date' => '2 days ago'],
                ['name' => 'Nuwan Silva', 'initials' => 'NS', 'style' => 'Low Fade', 'rating' => 5, 'text' => 'Best salon experience in Colombo. The booking system is so smooth and the result was exactly what I pictured. Will be back monthly!', 'color' => 'from-youPurple to-violet-500', 'date' => '1 week ago'],
                ['name' => 'Dilshan Fernando', 'initials' => 'DF', 'style' => 'Classic Quiff', 'rating' => 4, 'text' => 'Really professional service. The gallery gave me great inspiration and the stylist nailed the quiff perfectly. Highly recommended!', 'color' => 'from-pink-400 to-rose-500', 'date' => '2 weeks ago'],
                ['name' => 'Asanka Jayawardena', 'initials' => 'AJ', 'style' => 'Side Part', 'rating' => 5, 'text' => 'I uploaded my photo and the AI instantly knew what would suit me. The side part looks clean and sharp. 10/10 experience!', 'color' => 'from-emerald-400 to-teal-500', 'date' => '3 weeks ago'],
                ['name' => 'Tharindu Bandara', 'initials' => 'TB', 'style' => 'Pompadour', 'rating' => 5, 'text' => 'The pompadour transformation was unreal. Came in looking ordinary, left looking like a movie star. Salon You never disappoints!', 'color' => 'from-blue-400 to-cyan-500', 'date' => '1 month ago'],
                ['name' => 'Sachith Rajapaksa', 'initials' => 'SR', 'style' => 'Buzz Cut', 'rating' => 5, 'text' => 'Simple, clean and precise. The buzz cut is exactly what I needed for the summer. Fast service and friendly staff!', 'color' => 'from-rose-400 to-red-500', 'date' => '1 month ago'],
            ];
            foreach($reviews as $review):
            ?>
            <div class="bg-slate-950 rounded-2xl border border-slate-800 p-5 hover:border-slate-700 transition-all group space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr <?php echo $review['color']; ?> flex items-center justify-center text-white text-xs font-black"><?php echo $review['initials']; ?></div>
                        <div>
                            <p class="text-sm font-bold text-white"><?php echo $review['name']; ?></p>
                            <p class="text-[10px] text-slate-500"><?php echo $review['date']; ?></p>
                        </div>
                    </div>
                    <div class="flex gap-0.5">
                        <?php for($s=1;$s<=5;$s++): ?>
                        <i class="fa<?php echo $s<=$review['rating']?'s':'r'; ?> fa-star text-youAmber text-[10px]"></i>
                        <?php endfor; ?>
                    </div>
                </div>
                <p class="text-sm text-slate-300 leading-relaxed italic">"<?php echo $review['text']; ?>"</p>
                <div class="pt-2 border-t border-slate-800">
                    <span class="text-[10px] font-bold text-youAmber bg-youAmber/10 border border-youAmber/20 px-2 py-0.5 rounded-full">
                        <i class="fas fa-cut mr-1"></i><?php echo $review['style']; ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Stats Strip -->
    <div class="max-w-7xl mx-auto w-full">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-salonSurface rounded-2xl border border-slate-800 p-5 text-center group hover:border-youAmber/30 transition-all">
                <p class="text-3xl font-black text-youAmber">500+</p>
                <p class="text-xs text-slate-400 mt-1 font-medium">Transformations Done</p>
            </div>
            <div class="bg-salonSurface rounded-2xl border border-slate-800 p-5 text-center group hover:border-youPurple/30 transition-all">
                <p class="text-3xl font-black text-youPurple">98%</p>
                <p class="text-xs text-slate-400 mt-1 font-medium">Customer Satisfaction</p>
            </div>
            <div class="bg-salonSurface rounded-2xl border border-slate-800 p-5 text-center group hover:border-pink-400/30 transition-all">
                <p class="text-3xl font-black text-pink-400">6+</p>
                <p class="text-xs text-slate-400 mt-1 font-medium">Signature Styles</p>
            </div>
            <div class="bg-salonSurface rounded-2xl border border-slate-800 p-5 text-center group hover:border-emerald-400/30 transition-all">
                <p class="text-3xl font-black text-emerald-400">4.9★</p>
                <p class="text-xs text-slate-400 mt-1 font-medium">Average Rating</p>
            </div>
        </div>
    </div>

</div>
            <!-- ABOUT PAGE -->
            <div id="page-about" class="app-page flex-1 max-w-5xl w-full mx-auto px-6 py-12 text-center space-y-6">
                <h2 class="text-3xl font-extrabold bg-gradient-to-r from-youAmber to-youPurple bg-clip-text text-transparent">About Us</h2>
                <p class="text-slate-300 leading-relaxed max-w-2xl mx-auto">Salon You bridges precision human styling and cutting edge artificial intelligence technology. We craft looks based completely around you.</p>
            </div>

            <!-- CONTACT PAGE -->
            <div id="page-contact" class="app-page flex-1 max-w-md w-full mx-auto px-6 py-12 space-y-6">
                <h2 class="text-3xl font-extrabold text-center bg-gradient-to-r from-youAmber to-youPurple bg-clip-text text-transparent">Contact Us</h2>
                <div class="bg-salonSurface p-6 rounded-2xl border border-slate-800 space-y-4">
                    <p><i class="fas fa-map-marker-alt text-youAmber mr-3"></i> 123 Luxury Lane, Colombo, Sri Lanka</p>
                    <p><i class="fas fa-phone-alt text-youAmber mr-3"></i> +94 11 234 5678</p>
                    <p><i class="fas fa-envelope text-youPurple mr-3"></i> contact@salonyou.com</p>
                </div>
            </div>

            <!-- BOOKING PAGE -->
            <div id="page-booking" class="app-page flex-1 max-w-xl w-full mx-auto px-6 py-12">
                <h2 class="text-2xl font-bold mb-4">Appointment Booking Interface</h2>
                <div class="bg-salonSurface p-6 rounded-2xl border border-slate-800 space-y-4">
                    <div>
                        <label class="block text-xs uppercase text-slate-400 font-bold mb-2">Select Service</label>
                        <select id="booking-service-select" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-sm focus:outline-none focus:border-youAmber text-white">
                            <option value="">-- Select a Service --</option>
                            <?php foreach ($services as $service):
                                $duration = $service['duration_mins'] >= 60
                                    ? round($service['duration_mins'] / 60, 1) . ' hr'
                                    : $service['duration_mins'] . ' mins';
                            ?>
                            <option value="<?php echo $service['id']; ?>">
                                <?php echo htmlspecialchars($service['service_name']); ?> — Rs. <?php echo number_format($service['price']); ?> (<?php echo $duration; ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs uppercase text-slate-400 font-bold mb-2">Preferred Date</label>
                        <input type="date" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-sm focus:outline-none focus:border-youAmber text-white">
                    </div>
                    <div>
                        <label class="block text-xs uppercase text-slate-400 font-bold mb-2">Preferred Time</label>
                        <select class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-sm focus:outline-none focus:border-youAmber text-white">
                            <option value="">-- Select Time --</option>
                            <option>9:00 AM</option>
                            <option>10:00 AM</option>
                            <option>11:00 AM</option>
                            <option>12:00 PM</option>
                            <option>1:00 PM</option>
                            <option>2:00 PM</option>
                            <option>3:00 PM</option>
                            <option>4:00 PM</option>
                            <option>5:00 PM</option>
                        </select>
                    </div>
                    <button onclick="alert('Appointment Submitted Successfully!'); navigateTo('customer-dashboard');" class="w-full py-3 bg-gradient-to-r from-youAmber to-youPurple text-white rounded-xl font-bold text-sm">Confirm Booking</button>
                </div>
            </div>

            <!-- CUSTOMER DASHBOARD -->
            <div id="page-customer-dashboard" class="app-page <?php echo ($is_logged_in && $user_role === 'customer') ? 'active' : ''; ?> flex-1 max-w-7xl w-full mx-auto px-6 py-12 space-y-8">
                <div>
                    <h2 class="text-3xl font-extrabold text-white">Welcome Back, <span class="text-youAmber"><?php echo htmlspecialchars($user_name); ?></span></h2>
                    <p class="text-xs text-slate-400 mt-1">Loyalty Program Tier status configuration</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-br from-amber-950/40 to-salonSurface p-6 rounded-2xl border border-youAmber/20">
                        <h4 class="text-xs uppercase font-bold text-youAmber tracking-wider mb-2">Loyalty Program</h4>
                        <p class="text-3xl font-black text-white">250 Points</p>
                        <p class="text-xs text-slate-400 mt-2">50 More Points Required to Reach Gold Membership</p>
                    </div>
                    <div class="bg-salonSurface p-6 rounded-2xl border border-slate-800">
                        <h4 class="text-xs uppercase font-bold text-youPurple tracking-wider mb-2">Next Appointment</h4>
                        <p class="text-lg font-bold text-white">Haircut</p>
                        <p class="text-xs text-slate-400">Date: June 10 | Time: 2:00 PM</p>
                        <p class="text-xs text-emerald-400 mt-3"><i class="fas fa-check-circle mr-1"></i> Status Tracker: Confirmed</p>
                    </div>
                    <div class="bg-salonSurface p-6 rounded-2xl border border-slate-800 flex flex-col justify-between">
                        <div>
                            <h4 class="text-xs uppercase font-bold text-pink-400 tracking-wider mb-1">Favourite Stylists</h4>
                            <p class="text-sm font-semibold text-slate-200">Sarah Fernando</p>
                        </div>
                        <button onclick="navigateTo('booking')" class="w-full py-2 bg-slate-900 hover:bg-slate-950 text-xs text-youAmber font-bold border border-slate-800 rounded-lg">Quick Book Stylist</button>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-slate-950 via-salonSurface to-slate-950 p-8 rounded-3xl border border-slate-800 space-y-6">
                    <h3 class="text-xl font-bold flex items-center gap-2"><i class="fas fa-robot text-youPurple"></i> Integrated AI Beauty Assistant Hub</h3>
                    <div class="bg-slate-900/40 backdrop-blur-md rounded-2xl p-6 border border-slate-800/80 shadow-xl" id="ai-workspace-panel">
                        <div class="mb-6 bg-slate-950/60 p-4 rounded-xl border border-slate-800/60">
                            <label class="block text-xs font-bold text-slate-400 tracking-wider uppercase mb-2">Upload Profile Photo for Mirror Generation</label>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <input type="file" id="ai-photo-input" accept="image/jpeg, image/png, image/webp" 
                                       class="block w-full text-xs text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-youPurple/20 file:text-youPurple hover:file:bg-youPurple/30 transition cursor-pointer bg-slate-900 rounded-xl border border-slate-800 p-1">
                                <button onclick="analyzeFaceShape();" id="launch-preview-btn" 
                                        class="bg-gradient-to-r from-youAmber to-youPurple hover:opacity-90 text-white text-xs font-bold px-6 py-3 rounded-xl shadow-lg shadow-purple-900/20 transition-all uppercase tracking-wider flex items-center justify-center gap-2 whitespace-nowrap">
                                    <i class="fas fa-magic"></i> Launch Preview
                                </button>
                            </div>
                        </div>
                        <div id="ai-recommender-card" class="min-h-[350px] flex items-center justify-center border border-dashed border-slate-800 rounded-xl bg-slate-950/30 p-6 transition-all duration-300">
                            <div class="text-center space-y-3 max-w-sm">
                                <div class="w-16 h-16 bg-slate-900 border border-slate-800 rounded-2xl flex items-center justify-center mx-auto shadow-inner text-slate-500">
                                    <i class="fas fa-user-astronaut text-2xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-200">Interactive Mirror System Idle</h4>
                                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Please choose a clear portrait selfie and execute processing to apply 360° transparent haircut filter configurations over your facial structure.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STAFF DASHBOARD -->
            <div id="page-staff-dashboard" class="app-page <?php echo ($is_logged_in && $user_role === 'staff') ? 'active' : ''; ?> flex-1 max-w-7xl w-full mx-auto px-6 py-12 space-y-8">
                <div class="border-b border-slate-800 pb-4">
                    <h2 class="text-3xl font-extrabold text-white">Staff Management Workspace</h2>
                </div>
            </div>

            <!-- ADMIN DASHBOARD -->
            <div id="page-admin-dashboard" class="app-page <?php echo ($is_logged_in && $user_role === 'admin') ? 'active' : ''; ?> flex-1 max-w-7xl w-full mx-auto px-6 py-12 space-y-8">
                <div class="border-b border-slate-800 pb-4">
                    <h2 class="text-3xl font-extrabold text-white">Admin Command Center</h2>
                </div>
            </div>

        </div>
    </div>

    <!-- LOGIN MODAL -->
    <div id="page-login" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="bg-salonSurface p-8 rounded-3xl border border-slate-800 max-w-sm w-full relative shadow-2xl space-y-6">
            <button type="button" onclick="document.getElementById('page-login').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-white transition-colors"><i class="fas fa-times text-lg"></i></button>
            <div class="text-center">
                <h3 class="text-2xl font-bold text-white">System Authentication</h3>
                <p class="text-xs text-slate-400 mt-1">Select a role template below to access dashboard panels directly</p>
            </div>
            <div class="space-y-3">
                <button onclick="processLogin('customer')" class="w-full text-left p-3 rounded-xl bg-slate-900 border border-emerald-500/30 flex justify-between items-center group cursor-pointer"><p class="text-sm font-semibold text-white">Log in as Customer</p><i class="fas fa-arrow-right text-emerald-400"></i></button>
                <button onclick="processLogin('staff')" class="w-full text-left p-3 rounded-xl bg-slate-900 border border-blue-500/30 flex justify-between items-center group cursor-pointer"><p class="text-sm font-semibold text-white">Log in as Staff Profile</p><i class="fas fa-arrow-right text-blue-400"></i></button>
                <button onclick="processLogin('admin')" class="w-full text-left p-3 rounded-xl bg-slate-900 border border-rose-500/30 flex justify-between items-center group cursor-pointer"><p class="text-sm font-semibold text-white">Log in as System Admin</p><i class="fas fa-arrow-right text-rose-400"></i></button>
            </div>
        </div>
    </div>

    <!-- REGISTER MODAL -->
    <div id="register" class="hidden fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 p-4">
        <div class="bg-slate-800 p-8 rounded-3xl border border-slate-700 max-w-md w-full relative shadow-2xl">
            <button type="button" onclick="document.getElementById('register').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-white transition-colors"><i class="fas fa-times text-lg"></i></button>
            <h2 class="text-2xl font-bold mb-6 text-white text-center">Create Account</h2>
            <form action="auth.php?action=register" method="POST" class="space-y-4">
                <div><input type="text" name="name" placeholder="Full Name" required class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-youAmber"></div>
                <div><input type="email" name="email" placeholder="Email Address" required class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-youAmber"></div>
                <div><input type="password" name="password" placeholder="Password" required class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-youAmber"></div>
                <button type="submit" class="w-full bg-gradient-to-r from-youAmber to-youPurple text-white py-3.5 rounded-xl font-bold text-sm tracking-wide shadow-lg shadow-purple-500/20">Register Account</button>
            </form>
        </div>
    </div>

    <footer class="bg-slate-950 border-t border-slate-800 px-6 py-6 text-center text-xs text-slate-500 mt-auto">
        <p>&copy; 2026 Salon You System. Custom Crafted Lookbook Operations Environment.</p>
    </footer>

    <script>
        let currentAuthenticatedRole = <?php echo $is_logged_in ? "'".$user_role."'" : "null"; ?>;

        function processLogin(role) {
            fetch(`auth.php?action=login`, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: `role=${role}` })
            .then(response => response.json())
            .then(data => { if (data.success) { window.location.reload(); } else { alert(data.message || "Authentication failed"); } })
            .catch(error => alert("An error occurred during authentication."));
        }

        function processLogout() {
            window.location.href = "logout.php";
        }

        function navigateTo(pageId) {
            if (pageId === 'register' || pageId === 'login') {
                const modal = document.getElementById(pageId === 'login' ? 'page-login' : 'register');
                if (modal) modal.classList.remove('hidden');
                return;
            }
            const targetSection = document.getElementById('page-' + pageId) || document.getElementById(pageId);
            if (targetSection) {
                document.querySelectorAll('.app-page').forEach(sec => { if (!sec.classList.contains('fixed')) sec.classList.remove('active'); });
                targetSection.classList.add('active');
                window.scrollTo(0, 0);
            }
        }

        function handleBookingIntent(serviceId = null) {
            if (!currentAuthenticatedRole) {
                alert("Please login or register an account first to book an appointment!");
                navigateTo('login');
            } else {
                if (serviceId) {
                    document.getElementById('booking-service-select').value = serviceId;
                }
                navigateTo('booking');
            }
        }

        // =========================================================================
        // AI Hairstyle Recommender Engine
        // =========================================================================

        async function analyzeFaceShape() {
            const photoInput = document.getElementById('ai-photo-input');
            if (!photoInput || photoInput.files.length === 0) {
                alert("Please select a valid face profile photograph first.");
                return;
            }

            const cardContainer = document.getElementById('ai-recommender-card');
            cardContainer.innerHTML = `
                <div class="w-full flex flex-col items-center justify-center py-20 space-y-4 text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-youPurple"></div>
                    <div class="space-y-1">
                        <p class="text-sm font-bold text-white tracking-wide animate-pulse">Running Rule-Based Geometric Vector Scan...</p>
                        <p class="text-xs text-slate-500">Calculating head contour coordinates dynamically.</p>
                    </div>
                </div>
            `;

            const formData = new FormData();
            formData.append('image', photoInput.files[0]);

            try {
                const response = await fetch('generate_ai.php', { method: 'POST', body: formData });
                const result = await response.json();
                if (!result.success) {
                    alert("Analysis Engine Interrupted: " + result.message);
                    resetAIRecommender();
                    return;
                }
                renderMirrorOutput(result);
            } catch (error) {
                console.error("AJAX Error:", error);
                alert("Critical backend communication breakdown with generate_ai.php.");
                resetAIRecommender();
            }
        }

        function renderMirrorOutput(data) {
            const cardContainer = document.getElementById('ai-recommender-card');

            const hairStyleMap = {
                'crop':      { front: 'crop_front.png',  side: 'crop_side.png',  back: 'crop_back.png' },
                'pompadour': { front: 'pf.png',          side: 'ps.png',         back: 'pb.png' },
                'quiff':     { front: 'quiff_front.png', side: 'quiff_side.png', back: 'quiff_back.png' },
                'side_part': { front: 'sf.png',          side: 'ss.png',         back: 'sb.png' },
                'fade':      { front: 'fade_front.png',  side: 'fade_side.png',  back: 'fade_back.png' },
                'buzz':      { front: 'bf.png',          side: 'bs.png',         back: 'bb.png' }
            };

            const base = 'uploads/images/hairstyles/';
            const style = hairStyleMap[data.filter_key] || hairStyleMap['crop'];
            const compatibilityScores = {
                'crop': 96, 'pompadour': 88, 'quiff': 91,
                'side_part': 94, 'fade': 89, 'buzz': 85
            };
            const score = compatibilityScores[data.filter_key] || 90;

            let viewportHTML = `
                <div class="w-full space-y-6 animate-fadeIn">

                    <!-- Face Shape Badge -->
                    <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-950 p-4 rounded-xl border border-slate-800">
                        <div class="px-3 py-1.5 rounded-lg bg-youPurple/10 text-youPurple font-mono text-xs font-bold border border-youPurple/20">
                            FACE SHAPE: ${data.face_shape.toUpperCase()}
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-500 font-medium">AI Best Match:</span>
                            <span class="text-xs font-bold text-youAmber bg-youAmber/10 px-2.5 py-1 rounded-md border border-youAmber/20">${data.recommendation}</span>
                            <span class="text-xs font-bold text-emerald-400 bg-emerald-400/10 px-2 py-1 rounded-md border border-emerald-400/20">${score}% Match</span>
                        </div>
                    </div>

                    <!-- MAIN LAYOUT: Left = User Photo | Right = 4 Views -->
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                        <!-- LEFT: User Original Photo -->
                        <div class="lg:col-span-2 bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden flex flex-col">
                            <div class="px-4 py-3 border-b border-slate-800 flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                                <span class="text-xs font-bold text-white uppercase tracking-wider">Your Uploaded Photo</span>
                            </div>
                            <div class="flex-1 p-4 flex flex-col gap-4">
                                <div class="w-full aspect-[3/4] rounded-xl overflow-hidden border border-slate-700 relative">
                                    <img src="${data.uploaded_image}" class="w-full h-full object-cover object-top" alt="Your Original Photo">
                                    <div class="absolute top-2 left-2 bg-black/60 backdrop-blur-sm text-[9px] text-emerald-400 font-bold px-2 py-1 rounded-md border border-emerald-500/20 uppercase tracking-wider">Original</div>
                                </div>
                                <div class="bg-slate-900 rounded-xl p-3 border border-slate-800 space-y-2">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Detected Details</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-[11px] text-slate-500">Face Shape</span>
                                        <span class="text-[11px] font-bold text-youAmber">${data.face_shape}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-[11px] text-slate-500">Best Match</span>
                                        <span class="text-[11px] font-bold text-youPurple">${data.recommendation}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-[11px] text-slate-500">Match Score</span>
                                        <span class="text-[11px] font-bold text-emerald-400">${score}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT: AI Hairstyle 4 Views -->
                        <div class="lg:col-span-3 bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden flex flex-col">
                            <div class="px-4 py-3 border-b border-slate-800 flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-youPurple"></div>
                                <span class="text-xs font-bold text-white uppercase tracking-wider">AI Recommended — ${data.recommendation}</span>
                            </div>
                            <div class="flex-1 p-4">
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden flex flex-col">
                                        <div class="px-3 py-1.5 bg-slate-800/60 text-[9px] font-bold text-slate-400 uppercase tracking-wider">1. Front View</div>
                                        <div class="aspect-square overflow-hidden">
                                            <img src="${base}${style.front}" class="w-full h-full object-cover object-top" alt="Front View"
                                                 onerror="this.parentElement.innerHTML='<div style=\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#475569;font-size:12px\'>No Image</div>'">
                                        </div>
                                        <div class="px-2 py-1.5 text-center"><span class="text-[9px] text-youPurple font-mono font-bold uppercase tracking-wider">Front Render</span></div>
                                    </div>
                                    <div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden flex flex-col">
                                        <div class="px-3 py-1.5 bg-slate-800/60 text-[9px] font-bold text-slate-400 uppercase tracking-wider">2. Left Profile</div>
                                        <div class="aspect-square overflow-hidden">
                                            <img src="${base}${style.side}" class="w-full h-full object-cover object-top" alt="Left Profile"
                                                 onerror="this.parentElement.innerHTML='<div style=\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#475569;font-size:12px\'>No Image</div>'">
                                        </div>
                                        <div class="px-2 py-1.5 text-center"><span class="text-[9px] text-youPurple font-mono font-bold uppercase tracking-wider">Left Profile</span></div>
                                    </div>
                                    <div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden flex flex-col">
                                        <div class="px-3 py-1.5 bg-slate-800/60 text-[9px] font-bold text-slate-400 uppercase tracking-wider">3. Right Profile</div>
                                        <div class="aspect-square overflow-hidden">
                                            <img src="${base}${style.side}" class="w-full h-full object-cover object-top" style="transform: scaleX(-1);" alt="Right Profile"
                                                 onerror="this.parentElement.innerHTML='<div style=\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#475569;font-size:12px\'>No Image</div>'">
                                        </div>
                                        <div class="px-2 py-1.5 text-center"><span class="text-[9px] text-youPurple font-mono font-bold uppercase tracking-wider">Right Profile</span></div>
                                    </div>
                                    <div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden flex flex-col">
                                        <div class="px-3 py-1.5 bg-slate-800/60 text-[9px] font-bold text-slate-400 uppercase tracking-wider">4. Rear View</div>
                                        <div class="aspect-square overflow-hidden">
                                            <img src="${base}${style.back}" class="w-full h-full object-cover object-top" alt="Rear View"
                                                 onerror="this.parentElement.innerHTML='<div style=\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#475569;font-size:12px\'>No Image</div>'">
                                        </div>
                                        <div class="px-2 py-1.5 text-center"><span class="text-[9px] text-youPurple font-mono font-bold uppercase tracking-wider">Rear Taper</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Alternative Styles -->
                    <div class="bg-slate-950/80 p-4 rounded-xl border border-slate-800 space-y-4">
                        <div class="border-b border-slate-800 pb-2">
                            <h5 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-th-large text-youAmber"></i> Explore Alternative Hairstyle Options
                            </h5>
                            <p class="text-[11px] text-slate-500 mt-0.5">Click any style below to update the 4-view preview.</p>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                            ${Object.entries(data.full_catalog).map(([shape, layout]) => {
                                const isActive = layout.key === data.filter_key;
                                const s = hairStyleMap[layout.key] || hairStyleMap['crop'];
                                return `
                                    <div onclick="swapActiveFilter('${layout.key}', '${layout.name}', '${shape}', '${data.uploaded_image}');"
                                         class="cursor-pointer rounded-xl border overflow-hidden transition-all duration-200 hover:scale-105
                                         ${isActive ? 'border-youAmber/60 shadow-md shadow-youAmber/10' : 'border-slate-800 hover:border-slate-700'}">
                                        <div class="h-20 bg-slate-900 overflow-hidden">
                                            <img src="${base}${s.front}" class="w-full h-full object-cover object-top" alt="${layout.name}"
                                                 onerror="this.parentElement.innerHTML='<div style=\'width:100%;height:100%;display:flex;align-items:center;justify-content:center\'><i class=\'fas fa-cut\' style=\'color:#475569\'></i></div>'">
                                        </div>
                                        <div class="p-2 ${isActive ? 'bg-gradient-to-br from-youAmber/10 to-youPurple/10' : 'bg-slate-900'}">
                                            <p class="text-[10px] font-bold text-white truncate">${layout.name}</p>
                                            <p class="text-[9px] text-slate-500 mt-0.5">${shape}</p>
                                            ${isActive ? '<p class="text-[9px] text-youAmber font-bold mt-1">✓ Active</p>' : ''}
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>

                    <!-- Reset Button -->
                    <div class="flex justify-end pt-2">
                        <button onclick="resetAIRecommender();" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold px-5 py-2.5 rounded-lg border border-slate-700 transition shadow-md">
                            <i class="fas fa-redo mr-1"></i> Clear & Re-Upload
                        </button>
                    </div>

                </div>
            `;

            cardContainer.innerHTML = viewportHTML;
        }

        function swapActiveFilter(filterKey, styleName, faceShape, imgPath) {
            const virtualDataStruct = {
                success: true,
                face_shape: faceShape,
                recommendation: styleName,
                filter_key: filterKey,
                uploaded_image: imgPath,
                full_catalog: {
                    'Oval':    { name: 'Modern Textured Crop', key: 'crop' },
                    'Square':  { name: 'Pompadour',            key: 'pompadour' },
                    'Round':   { name: 'Quiff',                key: 'quiff' },
                    'Oblong':  { name: 'Side Part',            key: 'side_part' },
                    'Diamond': { name: 'Low Fade',             key: 'fade' },
                    'Heart':   { name: 'Buzz Cut',             key: 'buzz' }
                }
            };
            renderMirrorOutput(virtualDataStruct);
        }

        function resetAIRecommender() {
            const cardContainer = document.getElementById('ai-recommender-card');
            document.getElementById('ai-photo-input').value = "";
            cardContainer.innerHTML = `
                <div class="text-center space-y-3 max-w-sm">
                    <div class="w-16 h-16 bg-slate-900 border border-slate-800 rounded-2xl flex items-center justify-center mx-auto shadow-inner text-slate-500">
                        <i class="fas fa-user-astronaut text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-200">Interactive Mirror System Idle</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Please choose a clear portrait selfie and execute processing to apply 360° transparent haircut filter configurations over your facial structure.</p>
                    </div>
                </div>
            `;
        }

        // Gallery Carousel
let currentSlide = 0;
const totalSlides = 4;
function moveCarousel(direction) {
    currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
    updateCarousel();
}
function goToSlide(index) {
    currentSlide = index;
    updateCarousel();
}
function updateCarousel() {
    document.getElementById('carousel-track').style.transform = `translateX(-${currentSlide * 100}%)`;
    document.querySelectorAll('.carousel-dot').forEach((dot, i) => {
        dot.classList.toggle('bg-youAmber', i === currentSlide);
        dot.classList.toggle('bg-slate-600', i !== currentSlide);
        dot.style.transform = i === currentSlide ? 'scale(1.3)' : 'scale(1)';
    });
}
setInterval(() => moveCarousel(1), 5000);
    </script>
</body>
</html>
