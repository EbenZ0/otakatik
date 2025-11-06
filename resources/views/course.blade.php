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
                <a href="/my-courses" class="text-gray-700 hover:text-orange-500 font-medium transition">My Courses</a>
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

    <!-- Pricing Section -->
    <section class="pt-32 pb-10 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">Our Courses</h1>
                <p class="text-gray-600 text-lg">Choose the perfect plan for your learning journey</p>
            </div>

            <!-- Pricing Cards Container -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-8 shadow-custom animate-fade-in">
                <div class="grid md:grid-cols-3 gap-6">
                    <!-- Starter Package -->
                    <div class="bg-white rounded-xl p-6 text-center hover:transform hover:scale-105 transition-all duration-300">
                        <h3 class="text-2xl font-bold text-gray-800 mb-4 pb-3 border-b-2 border-gray-200">Starter</h3>
                        <ul class="text-left text-gray-600 text-sm space-y-2 mb-6 min-h-[200px]">
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>Akses ke rumaah</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>10+ modul / bulan / day</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>Video tutorial</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>3 bulan</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>Sertifikat digital</span>
                            </li>
                        </ul>
                        <div class="text-3xl font-bold text-gray-800 mb-4">
                            <span class="text-lg">Rp</span>99.000
                        </div>
                        <button onclick="selectPackage('Starter', 99000)" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-lg transition-all w-full">
                            Pilih
                        </button>
                    </div>

                    <!-- Pro Learner Package -->
                    <div class="bg-white rounded-xl p-6 text-center hover:transform hover:scale-105 transition-all duration-300">
                        <h3 class="text-2xl font-bold text-gray-800 mb-4 pb-3 border-b-2 border-gray-200">Pro Learner</h3>
                        <ul class="text-left text-gray-600 text-sm space-y-2 mb-6 min-h-[200px]">
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>Akses semua puurzee</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>3 buah instructor</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>Project kolaborasi</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>Mentorship</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>Sertifikat digital + badge</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>kompetensi</span>
                            </li>
                        </ul>
                        <div class="text-3xl font-bold text-gray-800 mb-4">
                            <span class="text-lg">Rp</span>179.000
                        </div>
                        <button onclick="selectPackage('Pro Learner', 179000)" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-lg transition-all w-full">
                            Pilih
                        </button>
                    </div>

                    <!-- Expert Mode Package -->
                    <div class="bg-white rounded-xl p-6 text-center hover:transform hover:scale-105 transition-all duration-300">
                        <h3 class="text-2xl font-bold text-gray-800 mb-4 pb-3 border-b-2 border-gray-200">Expert Mode</h3>
                        <ul class="text-left text-gray-600 text-sm space-y-2 mb-6 min-h-[200px]">
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>Akses semua puurzee +</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>Mentoring personal 1:1</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>Project dalam dunia kerja</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>Job job</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>1 tahun</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>Sertifikat digital + badge</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>Kompetensi + Rekomendasi</span>
                            </li>
                        </ul>
                        <div class="text-3xl font-bold text-gray-800 mb-4">
                            <span class="text-lg">Rp</span>299.000
                        </div>
                        <button onclick="selectPackage('Expert Mode', 299000)" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-lg transition-all w-full">
                            Pilih
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Registration Form Section -->
    <section class="pb-20 px-6">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-2xl shadow-custom animate-fade-in">
                <!-- Form Header -->
                <div class="border-b border-gray-200 px-8 py-6 flex items-center justify-between">
                    <h2 id="formTitle" class="text-2xl font-bold text-gray-800">Paket Starter</h2>
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
                        <input type="hidden" name="course" id="courseInput" value="Starter">
                        <input type="hidden" name="price" id="priceInput" value="99000">
                        
                        <div class="grid md:grid-cols-2 gap-6 mb-8">
                            <!-- Form fields sama seperti sebelumnya -->
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
                            <div>
                                <label class="text-sm text-gray-600 font-medium mb-2 block">Sub Course 1</label>
                                <select name="sub_course1" required class="w-full px-4 py-3 bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-200 transition">
                                    <option value="">Pilih Sub Course</option>
                                    <option value="Web Development">Web Development</option>
                                    <option value="Mobile Development">Mobile Development</option>
                                    <option value="Data Science">Data Science</option>
                                    <option value="AI & Machine Learning">AI & Machine Learning</option>
                                    <option value="Digital Marketing">Digital Marketing</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600 font-medium mb-2 block">Sub Course 2</label>
                                <select name="sub_course2" required class="w-full px-4 py-3 bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-200 transition">
                                    <option value="">Pilih Sub Course</option>
                                    <option value="Frontend">Frontend</option>
                                    <option value="Backend">Backend</option>
                                    <option value="Fullstack">Fullstack</option>
                                    <option value="DevOps">DevOps</option>
                                    <option value="UI/UX Design">UI/UX Design</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600 font-medium mb-2 block">Kelas</label>
                                <select name="kelas" required class="w-full px-4 py-3 bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-200 transition">
                                    <option value="">Pilih Kelas</option>
                                    <option value="Pagi">Pagi (09:00 - 12:00)</option>
                                    <option value="Siang">Siang (13:00 - 16:00)</option>
                                    <option value="Sore">Sore (16:00 - 19:00)</option>
                                    <option value="Malam">Malam (19:00 - 22:00)</option>
                                    <option value="Weekend">Weekend (Sabtu-Minggu)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Footer with Price and Submit -->
                        <div class="border-t-2 border-gray-100 pt-6 flex items-center justify-between">
                            <div class="flex items-center gap-8">
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-gray-800" id="displayPrice">Rp99.000</div>
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
        // Function to select package
        function selectPackage(packageName, price) {
            // Update form title
            document.getElementById('formTitle').textContent = 'Paket ' + packageName;
            
            // Update hidden inputs
            document.getElementById('courseInput').value = packageName;
            document.getElementById('priceInput').value = price;
            
            // Update display price
            document.getElementById('displayPrice').textContent = 'Rp' + price.toLocaleString('id-ID');
            
            // Smooth scroll to form
            document.getElementById('registrationForm').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
        }

        // Form submission handler
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const course = document.getElementById('courseInput').value;
            if (!course) {
                e.preventDefault();
                alert('Silakan pilih paket course terlebih dahulu!');
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