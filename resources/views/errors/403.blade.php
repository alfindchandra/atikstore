<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .lock-animation {
            animation: lockShake 1s ease-in-out infinite;
        }
        
        @keyframes lockShake {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-5deg); }
            75% { transform: rotate(5deg); }
        }
        
        .warning-pulse {
            animation: warningPulse 2s ease-in-out infinite;
        }
        
        @keyframes warningPulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(245, 101, 101, 0.7);
            }
            50% {
                transform: scale(1.1);
                box-shadow: 0 0 0 20px rgba(245, 101, 101, 0);
            }
        }
        
        .glass-effect {
            backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
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
    </style>
</head>
<body class="gradient-bg flex items-center justify-center p-4 relative">
    <!-- Background elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-red-500/10 rounded-full animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 w-48 h-48 bg-yellow-500/10 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
    </div>
    
    <!-- Main Content -->
    <div class="text-center bounce-in max-w-4xl mx-auto relative z-10">
        <!-- Lock Icon -->
        <div class="mb-8">
            <div class="w-32 h-32 mx-auto bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center warning-pulse">
                <i class="fas fa-lock text-5xl text-white lock-animation"></i>
            </div>
        </div>
        
        <!-- Error Card -->
        <div class="glass-effect rounded-3xl p-8 md:p-12 shadow-2xl">
            <!-- Error Code -->
            <h1 class="text-6xl md:text-8xl font-black text-red-400 mb-4">
                403
            </h1>
            
            <!-- Title -->
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                Akses Ditolak
            </h2>
            
            <!-- Description -->
            <p class="text-lg md:text-xl text-white/80 mb-8 max-w-2xl mx-auto leading-relaxed">
                Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.
            </p>
            
            <!-- Possible Reasons -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="glass-effect rounded-2xl p-6">
                    <i class="fas fa-user-slash text-2xl text-red-300 mb-3"></i>
                    <h3 class="text-white font-semibold mb-2">Tidak Login</h3>
                    <p class="text-white/70 text-sm">Anda belum masuk ke sistem</p>
                </div>
                
                <div class="glass-effect rounded-2xl p-6">
                    <i class="fas fa-shield-alt text-2xl text-yellow-300 mb-3"></i>
                    <h3 class="text-white font-semibold mb-2">Tidak Ada Izin</h3>
                    <p class="text-white/70 text-sm">Role Anda tidak memiliki akses</p>
                </div>
                
                <div class="glass-effect rounded-2xl p-6 md:col-span-2 lg:col-span-1">
                    <i class="fas fa-ban text-2xl text-orange-300 mb-3"></i>
                    <h3 class="text-white font-semibold mb-2">Akses Terbatas</h3>
                    <p class="text-white/70 text-sm">Halaman untuk pengguna tertentu</p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-8">
                <a href="{{ route('login') }}" 
                   class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 hover:scale-105 shadow-lg inline-flex items-center">
                    <i class="fas fa-sign-in-alt mr-3"></i>
                    Login
                </a>
                
                <a href="{{ url('/') }}" 
                   class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 hover:scale-105 shadow-lg inline-flex items-center">
                    <i class="fas fa-home mr-3"></i>
                    Kembali ke Beranda
                </a>
                
                <button onclick="history.back()" 
                        class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 hover:scale-105 shadow-lg inline-flex items-center">
                    <i class="fas fa-arrow-left mr-3"></i>
                    Kembali
                </button>
            </div>
            
            <!-- Contact Support -->
            <div class="glass-effect rounded-xl p-6">
                <h3 class="text-white font-semibold mb-3">
                    <i class="fas fa-question-circle mr-2"></i>
                    Butuh Bantuan?
                </h3>
                <p class="text-white/80 mb-4">
                    Jika Anda merasa memiliki akses ke halaman ini, silakan hubungi administrator sistem.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="mailto:admin@example.com" 
                       class="text-blue-300 hover:text-blue-200 transition-colors inline-flex items-center">
                        <i class="fas fa-envelope mr-2"></i>
                        admin@example.com
                    </a>
                    <a href="tel:+6281234567890" 
                       class="text-green-300 hover:text-green-200 transition-colors inline-flex items-center">
                        <i class="fas fa-phone mr-2"></i>
                        +62 812-3456-7890
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="text-white/60 text-sm mt-6">
            <p>Error Code: 403 Forbidden | Time: <span id="current-time"></span></p>
            <p class="mt-2">© 2025 Alfin Dchandra. All rights reserved.</p>
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
        
        // Add click effects to buttons
        const buttons = document.querySelectorAll('a, button');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Create ripple effect
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.style.position = 'absolute';
                ripple.style.background = 'rgba(255, 255, 255, 0.3)';
                ripple.style.borderRadius = '50%';
                ripple.style.transform = 'scale(0)';
                ripple.style.animation = 'ripple 0.6s linear';
                ripple.style.pointerEvents = 'none';
                
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
        
        // Add CSS for ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>