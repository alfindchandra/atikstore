<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login {{ config('app.toko')}}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #0f766e, #14b8a6, #5eead4);
            
            background-size: 400% 400%;
            animation: gradientShift 8s ease infinite;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .glass-effect {
            backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .floating-animation {
            animation: floating 6s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(3deg); }
        }
        
        .input-focus {
            transition: all 0.3s ease;
        }
        
        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(20, 184, 166, 0.1), 0 10px 10px -5px rgba(20, 184, 166, 0.04);
        }
        
        .btn-hover {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 25px 50px -12px rgba(15, 118, 110, 0.5);
        }
        
        .fade-in {
            animation: fadeInUp 1s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .circle-float-1 {
            animation: floatCircle1 10s ease-in-out infinite;
        }
        
        .circle-float-2 {
            animation: floatCircle2 12s ease-in-out infinite;
        }
        
        @keyframes floatCircle1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }
        
        @keyframes floatCircle2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-40px, -40px) scale(1.2); }
        }
    </style>
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center p-4 overflow-hidden relative">
    <!-- Background floating elements -->
    <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full circle-float-1"></div>
    <div class="absolute top-1/3 right-16 w-16 h-16 bg-teal-300/20 rounded-full circle-float-2"></div>
    <div class="absolute bottom-20 left-1/4 w-12 h-12 bg-teal-100/15 rounded-full floating-animation"></div>
    <div class="absolute top-2/3 right-1/3 w-8 h-8 bg-white/20 rounded-full circle-float-1" style="animation-delay: -2s;"></div>
    
    <!-- Main container -->
    <div class="w-full max-w-md mx-auto fade-in">
        <!-- Login Form -->
        <div class="glass-effect rounded-3xl p-8 shadow-2xl">
            <!-- Success Message -->
            @if (session('success'))
            <div class="mb-4 p-4 bg-green-600/80 text-white rounded-lg backdrop-blur">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
            <div class="mb-4 p-4 bg-red-600/70 text-white rounded-lg backdrop-blur glass-effect">
                <ul class="list-none">
                    @foreach ($errors->all() as $error)
                        <li><i class="fas fa-exclamation-circle mr-2"></i>{{ $error }}</li>
                    @endforeach
                </ul>
            </div> 
            @endif

            <form id="loginForm" class="space-y-6" action="{{ route('login.validate') }}" method="POST">
                @csrf    

                <!-- Email Input -->
                <div class="relative">
                    <label class="block text-sm font-medium text-white/90 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </label>
                    <input 
                        type="email" 
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="w-full px-4 py-4 bg-white/10 border border-white/20 rounded-2xl text-white placeholder-white/60 input-focus outline-none @error('email') border-red-400 @enderror"
                        placeholder="nama@email.com"
                        required
                    >
                    @error('email')
                    <span class="text-red-300 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Password Input -->
                <div class="relative">
                    <label class="block text-sm font-medium text-white/90 mb-2">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password"
                            name="password"
                            class="w-full px-4 py-4 bg-white/10 border border-white/20 rounded-2xl text-white placeholder-white/60 input-focus outline-none pr-12 @error('password') border-red-400 @enderror"
                            placeholder="••••••••"
                            required
                        >
                        
                        <button 
                            type="button" 
                            id="togglePassword"
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white/70 hover:text-white transition-colors"
                        >
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    @error('password')
                    <span class="text-red-300 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Remember Me -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center text-white/80">
                        <input type="checkbox" name="remember" class="mr-2 rounded border-white/20 bg-white/10 text-teal-500">
                        Ingat saya
                    </label>
                </div>
                
                <!-- Login Button -->
                <button 
                    type="submit"
                    id="loginBtn"
                    class="w-full bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-semibold py-4 px-6 rounded-2xl btn-hover shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span id="btnText">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Masuk
                    </span>
                </button>
            </form>
        </div>
        
        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-teal-100/60 text-xs">
                © 2025 Alfin Dchandra. All rights reserved.
            </p>
        </div>
    </div>
    
    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
        
        // Form submission with loading state
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const btnText = document.getElementById('btnText');
        
        loginForm.addEventListener('submit', function(e) {
            // Show loading state
            loginBtn.disabled = true;
            btnText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            
            // Form will submit normally
        });
        
        // Add focus/blur effects to inputs
        const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('scale-[1.02]');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('scale-[1.02]');
            });
        });
    </script>
</body>
</html>