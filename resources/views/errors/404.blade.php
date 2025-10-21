<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .floating-animation {
            animation: floating 6s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { 
                transform: translateY(0px) rotate(0deg); 
            }
            33% { 
                transform: translateY(-20px) rotate(2deg); 
            }
            66% { 
                transform: translateY(10px) rotate(-2deg); 
            }
        }
        
        .bounce-in {
            animation: bounceIn 1s ease-out;
        }
        
        @keyframes bounceIn {
            0% {
                transform: scale(0.3);
                opacity: 0;
            }
            50% {
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .pulse-glow {
            animation: pulseGlow 2s ease-in-out infinite alternate;
        }
        
        @keyframes pulseGlow {
            from {
                text-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
            }
            to {
                text-shadow: 0 0 30px rgba(255, 255, 255, 0.8);
            }
        }
        
        .btn-hover {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn-hover:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        
        .glass-effect {
            backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .number-404 {
            background: linear-gradient(45deg, #ff6b6b, #feca57, #48dbfb, #ff9ff3);
            background-size: 400% 400%;
            animation: gradientShift 4s ease infinite;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .circle-float {
            animation: circleFloat 8s ease-in-out infinite;
        }
        
        @keyframes circleFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }
    </style>
</head>
<body class="gradient-bg flex items-center justify-center p-4 overflow-hidden relative">
    <!-- Background floating elements -->
    <div class="absolute top-20 left-20 w-32 h-32 bg-white/10 rounded-full circle-float"></div>
    <div class="absolute top-1/4 right-16 w-24 h-24 bg-purple-300/20 rounded-full floating-animation" style="animation-delay: -2s;"></div>
    <div class="absolute bottom-32 left-1/3 w-16 h-16 bg-blue-200/15 rounded-full circle-float" style="animation-delay: -4s;"></div>
    <div class="absolute top-3/4 right-1/4 w-20 h-20 bg-pink-200/20 rounded-full floating-animation" style="animation-delay: -6s;"></div>
    
    <!-- Main Content -->
    <div class="text-center bounce-in max-w-4xl mx-auto">
        <!-- 404 Number -->
        <div class="mb-8">
            <h1 class="text-9xl md:text-[12rem] font-black number-404 pulse-glow leading-none">
                404
            </h1>
        </div>
        
        <!-- Error Card -->
        <div class="glass-effect rounded-3xl p-8 md:p-12 shadow-2xl mb-8">
            <!-- Icon -->
            <div class="mb-6">
                <div class="w-24 h-24 mx-auto bg-gradient-to-br from-red-400 to-pink-500 rounded-full flex items-center justify-center floating-animation">
                    <i class="fas fa-search text-4xl text-white"></i>
                </div>
            </div>
            
            <!-- Title -->
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Oops! Halaman Tidak Ditemukan
            </h2>
            
            <!-- Description -->
            <p class="text-lg md:text-xl text-white/80 mb-8 max-w-2xl mx-auto leading-relaxed">
                Maaf, halaman yang Anda cari tidak dapat ditemukan. Mungkin halaman tersebut telah dipindahkan, dihapus, atau URL yang Anda masukkan salah.
            </p>
            
            <!-- Suggestions -->
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <div class="glass-effect rounded-2xl p-6 card-hover">
                    <i class="fas fa-home text-2xl text-yellow-300 mb-3"></i>
                    <h3 class="text-white font-semibold mb-2">Kembali ke Beranda</h3>
                    <p class="text-white/70 text-sm">Mulai dari halaman utama</p>
                </div>
                
                <div class="glass-effect rounded-2xl p-6 card-hover">
                    <i class="fas fa-search text-2xl text-blue-300 mb-3"></i>
                    <h3 class="text-white font-semibold mb-2">Cari Halaman</h3>
                    <p class="text-white/70 text-sm">Gunakan fitur pencarian</p>
                </div>
                
                <div class="glass-effect rounded-2xl p-6 card-hover">
                    <i class="fas fa-envelope text-2xl text-green-300 mb-3"></i>
                    <h3 class="text-white font-semibold mb-2">Hubungi Kami</h3>
                    <p class="text-white/70 text-sm">Laporkan masalah ini</p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ url('/') }}" 
                   class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-4 px-8 rounded-2xl btn-hover shadow-lg inline-flex items-center">
                    <i class="fas fa-home mr-3"></i>
                    Kembali ke Beranda
                </a>
                
                <button onclick="history.back()" 
                        class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-4 px-8 rounded-2xl btn-hover shadow-lg inline-flex items-center">
                    <i class="fas fa-arrow-left mr-3"></i>
                    Kembali ke Halaman Sebelumnya
                </button>
            </div>
            
            <!-- Search Box -->
            <div class="mt-8">
                <div class="max-w-md mx-auto">
                    <div class="relative">
                        <input type="text" 
                               placeholder="Cari halaman..." 
                               class="w-full px-6 py-4 bg-white/20 border border-white/30 rounded-2xl text-white placeholder-white/60 focus:outline-none focus:ring-4 focus:ring-white/20 focus:border-white/40 transition-all duration-300">
                        <button class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-purple-500 to-pink-500 text-white px-4 py-2 rounded-xl hover:from-purple-600 hover:to-pink-600 transition-all duration-300">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="text-white/60 text-sm">
            <p>Error Code: 404 | © 2025 Alfin Dchandra. All rights reserved.</p>
            <p class="mt-2">
                <i class="fas fa-clock mr-1"></i>
                <span id="current-time"></span>
            </p>
        </div>
    </div>
    
    <script>
        // Update current time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('current-time').textContent = timeString;
        }
        
        updateTime();
        setInterval(updateTime, 1000);
        
        // Add hover effects to suggestion cards
        const cards = document.querySelectorAll('.card-hover');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.05)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
        
        // Search functionality
        const searchInput = document.querySelector('input[placeholder="Cari halaman..."]');
        const searchButton = searchInput.nextElementSibling;
        
        function performSearch() {
            const query = searchInput.value.trim();
            if (query) {
                // Redirect to search or main page with query parameter
                window.location.href = `/?search=${encodeURIComponent(query)}`;
            }
        }
        
        searchButton.addEventListener('click', performSearch);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
        
        // Auto-redirect countdown (optional)
        let countdown = 30;
        const autoRedirect = setInterval(() => {
            countdown--;
            if (countdown <= 0) {
                clearInterval(autoRedirect);
                window.location.href = '/';
            }
        }, 1000);
    </script>
</body>
</html>