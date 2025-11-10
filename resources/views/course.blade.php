<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Course - OtakAtik Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        .shadow-custom {
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .course-type-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .type-online { background: #3b82f6; color: white; }
        .type-hybrid { background: #f59e0b; color: white; }
        .type-offline { background: #10b981; color: white; }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Navbar -->
    <nav class="bg-white shadow-md fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-lg">O</span>
                </div>
                <span class="text-xl font-bold text-gray-800">OtakAtik</span>
            </div>
            
            <!-- Menu -->
            <div class="hidden md:flex items-center gap-8">
                <a href="/dashboard" class="text-gray-700 hover:text-orange-500 font-medium transition">About Us</a>
                <a href="/course" class="text-orange-500 font-medium transition">Our Course</a>
                @auth
                <a href="/my-courses" class="text-gray-700 hover:text-orange-500 font-medium transition">My Courses</a>
                <a href="/purchase-history" class="text-gray-700 hover:text-orange-500 font-medium transition">History</a>
                @endauth
            </div>
            
            <!-- Sign In / Logout Button -->
            @auth
                <div class="flex items-center gap-4">
                    <span class="text-gray-700 font-medium">Hi, {{ Auth::user()->name }}!</span>
                    <a href="/my-courses" class="bg-blue-500 hover:bg-blue-600 text-white font-bold px-4 py-2 rounded-lg transition-all">
                        <i class="fas fa-book mr-2"></i>My Courses
                    </a>
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold px-4 py-2 rounded-lg transition-all">
                            Logout
                        </button>
                    </form>
                </div>
            @else
                <div class="flex items-center gap-4">
                    <a href="/login" class="text-gray-700 hover:text-orange-500 font-medium transition">Login</a>
                    <a href="/register" class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-6 py-2.5 rounded-lg transition-all">
                        Sign Up
                    </a>
                </div>
            @endauth
        </div>
    </nav>

    <!-- Courses Section -->
    <section class="pt-32 pb-10 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">Our Courses</h1>
                <p class="text-gray-600 text-lg">Choose the perfect course for your learning journey</p>
            </div>

            <!-- Courses Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                @foreach($courses as $course)
                <div class="bg-white rounded-2xl shadow-custom overflow-hidden hover:transform hover:scale-105 transition-all duration-300 animate-fade-in relative">
                    <span class="course-type-badge type-{{ $course->type }}">
                        {{ ucfirst($course->type) }}
                    </span>
                    
                    <!-- Course Image -->
                    <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-book-open text-white text-6xl"></i>
                    </div>
                    
                    <!-- Course Content -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $course->title }}</h3>
                        
                        @if($course->instructor)
                        <p class="text-gray-600 text-sm mb-3">
                            <i class="fas fa-user-tie mr-2"></i>By {{ $course->instructor->name }}
                        </p>
                        @endif
                        
                        <p class="text-gray-700 mb-4 text-sm line-clamp-2">
                            {{ $course->description ?? 'No description available.' }}
                        </p>
                        
                        <!-- Course Info -->
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Kuota:</span>
                                <span class="font-medium">{{ $course->current_enrollment }}/{{ $course->max_quota }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tipe:</span>
                                <span class="font-medium capitalize">{{ $course->type }}</span>
                            </div>
                        </div>
                        
                        <!-- Pricing -->
                        <div class="flex items-center justify-between mb-4">
                            @if($course->discount_percent > 0)
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-bold text-gray-800">{{ $course->formatted_final_price }}</span>
                                <span class="text-sm text-gray-500 line-through">{{ $course->formatted_price }}</span>
                                <span class="text-xs bg-green-500 text-white px-2 py-1 rounded-full">
                                    -{{ $course->discount_percent }}%
                                </span>
                            </div>
                            @else
                            <span class="text-lg font-bold text-gray-800">{{ $course->formatted_price }}</span>
                            @endif
                        </div>
                        
                        <!-- Action Button -->
                        @auth
                        <button onclick="selectCourse({{ $course->id }}, '{{ $course->title }}', {{ $course->final_price }})" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition-all">
                            Daftar Sekarang
                        </button>
                        @else
                        <a href="/login" 
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition-all block text-center">
                            Login untuk Daftar
                        </a>
                        @endauth
                    </div>
                </div>
                @endforeach
            </div>

            @if(count($courses) === 0)
            <div class="text-center py-12">
                <i class="fas fa-book-open text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum ada course tersedia</h3>
                <p class="text-gray-500">Silakan check kembali nanti.</p>
            </div>
            @endif
        </div>
    </section>

    <!-- Registration Form Section -->
    <section class="pb-20 px-6" id="registrationSection" style="display: none;">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-2xl shadow-custom animate-fade-in">
                <!-- Form Header -->
                <div class="border-b border-gray-200 px-8 py-6 flex items-center justify-between">
                    <h2 id="formTitle" class="text-2xl font-bold text-gray-800">Pendaftaran Course</h2>
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">O</span>
                        </div>
                        <span class="text-lg font-bold text-gray-800">OtakAtik</span>
                    </div>
                </div>

                <!-- Form Body -->
                <div class="p-8">
                    <form id="registrationForm" action="/course/register" method="POST">
                        @csrf
                        <input type="hidden" name="course_id" id="courseInput">
                        
                        <div class="grid md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="text-sm text-gray-600 font-medium mb-2 block">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required 
                                       class="w-full px-4 py-3 bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-200 transition">
                            </div>
                            <div>
                                <label class="text-sm text-gray-600 font-medium mb-2 block">Tempat Tanggal Lahir</label>
                                <input type="text" name="ttl" placeholder="Contoh: Jakarta, 01 Januari 2000" required 
                                       class="w-full px-4 py-3 bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-200 transition">
                            </div>
                            <div>
                                <label class="text-sm text-gray-600 font-medium mb-2 block">Tempat Tinggal</label>
                                <input type="text" name="tempat_tinggal" placeholder="Nama Kota" required 
                                       class="w-full px-4 py-3 bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-200 transition">
                            </div>
                            <div>
                                <label class="text-sm text-gray-600 font-medium mb-2 block">Gender</label>
                                <select name="gender" required class="w-full px-4 py-3 bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-200 transition">
                                    <option value="">Pilih Gender</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-sm text-gray-600 font-medium mb-2 block">Kode Diskon (Opsional)</label>
                                <input type="text" name="discount_code" placeholder="Contoh: PROMOPNJ" 
                                       class="w-full px-4 py-3 bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-200 transition">
                                <p class="text-xs text-gray-500 mt-1">Gunakan kode <strong>PROMOPNJ</strong> untuk mendapatkan diskon 10%</p>
                            </div>
                        </div>

                        <!-- Footer with Price and Submit -->
                        <div class="border-t-2 border-gray-100 pt-6 flex items-center justify-between">
                            <div class="flex items-center gap-8">
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-gray-800" id="displayPrice">Rp0</div>
                                    <div class="text-sm text-gray-600">Total Pembayaran</div>
                                </div>
                            </div>
                            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-8 py-3 rounded-lg transition-all hover:scale-105 shadow-lg">
                                Daftar Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12 px-6">
        <div class="max-w-7xl mx-auto text-center">
            <div class="flex items-center justify-center gap-2 mb-4">
                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-xl">O</span>
                </div>
                <span class="text-2xl font-bold">OtakAtik Academy</span>
            </div>
            <p class="text-gray-400 mb-4">Membentuk Generasi Cerdas dan Berprestasi</p>
            <p class="text-gray-500 text-sm">&copy; 2025 OtakAtik Academy. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Function to select course
        function selectCourse(courseId, courseTitle, finalPrice) {
            // Update form title
            document.getElementById('formTitle').textContent = 'Pendaftaran: ' + courseTitle;
            
            // Update hidden inputs
            document.getElementById('courseInput').value = courseId;
            
            // Update display price
            document.getElementById('displayPrice').textContent = 'Rp' + finalPrice.toLocaleString('id-ID');
            
            // Show form section
            document.getElementById('registrationSection').style.display = 'block';
            
            // Smooth scroll to form
            document.getElementById('registrationSection').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
        }

        // Form submission handler
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const courseId = document.getElementById('courseInput').value;
            if (!courseId) {
                e.preventDefault();
                alert('Silakan pilih course terlebih dahulu!');
                return;
            }
            
            // Show loading
            const button = this.querySelector('button[type="submit"]');
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            button.disabled = true;
        });

        // Success message handler
        @if(session('success'))
            alert('{{ session('success') }}');
        @endif

        @if($errors->any())
            alert('Terjadi kesalahan: {{ $errors->first() }}');
        @endif
    </script>

</body>
</html>