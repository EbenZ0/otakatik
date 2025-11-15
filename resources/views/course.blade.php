<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Courses - OtakAtik Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .course-card {
            transition: all 0.3s ease;
        }
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        .type-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .type-full-online { background: #dbeafe; color: #1e40af; }
        .type-hybrid { background: #fef3c7; color: #d97706; }
        .type-tatap-muka { background: #d1fae5; color: #065f46; }
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
                <a href="/purchase-history" class="text-gray-700 hover:text-orange-500 font-medium transition">History</a>
            </div>
            
            <!-- User Info -->
            <div class="flex items-center gap-4">
                <span class="text-gray-700 font-medium">Hi, {{ Auth::user()->name }}!</span>
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold px-4 py-2 rounded-lg transition-all">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Courses Section -->
    <section class="pt-32 pb-20 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">Our Courses</h1>
                <p class="text-gray-600 text-lg">Pilih course yang sesuai dengan kebutuhan belajar Anda</p>
            </div>

            @if($courses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($courses as $course)
                    <div class="course-card bg-white rounded-2xl shadow-lg overflow-hidden">
                        <!-- Course Header -->
                        <div class="h-32 bg-gradient-to-r from-blue-500 to-purple-600 relative">
                            <div class="absolute inset-0 bg-black bg-opacity-20 flex items-center justify-center">
                                <h3 class="text-xl font-bold text-white text-center px-4">{{ $course->title }}</h3>
                            </div>
                        </div>
                        
                        <!-- Course Content -->
                        <div class="p-6">
                            <!-- Course Type & Status -->
                            <div class="flex items-center justify-between mb-4">
                                <span class="type-badge type-{{ strtolower(str_replace(' ', '-', $course->type)) }}">
                                    {{ $course->type }}
                                </span>
                                @if($course->is_active)
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">
                                    Available
                                </span>
                                @else
                                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-medium">
                                    Coming Soon
                                </span>
                                @endif
                            </div>
                            
                            <!-- Course Description -->
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                {{ Str::limit($course->description, 120) }}
                            </p>
                            
                            <!-- Course Details -->
                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                @if($course->instructor)
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user-tie text-blue-500"></i>
                                    <span>Instruktur: {{ $course->instructor->name }}</span>
                                </div>
                                @endif
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-users text-green-500"></i>
                                    <span>{{ $course->current_enrollment }}/{{ $course->max_quota }} peserta</span>
                                </div>
                                @if($course->has_available_slots)
                                <div class="flex items-center gap-2 text-green-600">
                                    <i class="fas fa-check-circle"></i>
                                    <span class="font-medium">Masih tersedia</span>
                                </div>
                                @else
                                <div class="flex items-center gap-2 text-red-600">
                                    <i class="fas fa-times-circle"></i>
                                    <span class="font-medium">Kuota penuh</span>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Pricing -->
                            <div class="mb-4">
                                @if($course->discount_percent > 0)
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl font-bold text-green-600">
                                        {{ $course->formatted_final_price }}
                                    </span>
                                    <span class="text-lg text-gray-500 line-through">
                                        {{ $course->formatted_price }}
                                    </span>
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">
                                        -{{ $course->discount_percent }}%
                                    </span>
                                </div>
                                @else
                                <span class="text-2xl font-bold text-gray-800">
                                    {{ $course->formatted_price }}
                                </span>
                                @endif
                            </div>
                            
                            <!-- Action Button -->
<div class="flex gap-2">
    <a href="{{ route('course.show.detail', $course->id) }}" 
       class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-center py-3 px-4 rounded-lg transition-all font-medium">
        <i class="fas fa-eye mr-2"></i> Lihat Detail
    </a>
    @if($course->is_active && $course->has_available_slots)
    <a href="{{ route('checkout.show', $course->id) }}" 
            class="bg-orange-500 hover:bg-orange-600 text-white py-3 px-4 rounded-lg transition-all font-medium">
        <i class="fas fa-shopping-cart mr-2"></i> Daftar
    </a>
    @else
    <button disabled 
            class="bg-gray-400 text-white py-3 px-4 rounded-lg font-medium cursor-not-allowed">
        <i class="fas fa-clock mr-2"></i> Tidak Tersedia
    </button>
    @endif
</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16 bg-white rounded-2xl shadow-lg">
                    <div class="max-w-md mx-auto">
                        <div class="w-32 h-32 bg-gradient-to-br from-gray-400 to-gray-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-book-open text-white text-4xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Belum Ada Course Tersedia</h3>
                        <p class="text-gray-600 mb-8">Saat ini belum ada course yang aktif. Silakan cek kembali nanti.</p>
                        <div class="w-12 h-1 bg-gray-200 rounded-full mx-auto"></div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Registration Modal -->
    <div id="registrationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Form Pendaftaran Course</h3>
            
            <!-- GUNAKAN URL LANGSUNG - SOLUSI NOMOR 4 -->
            <form id="registrationForm" action="/course/register" method="POST">
                @csrf
                <input type="hidden" name="course_id" id="course_id">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               value="{{ Auth::user()->name }}">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tempat, Tanggal Lahir</label>
                        <input type="text" name="ttl" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Contoh: Jakarta, 15 Agustus 1990">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Tinggal</label>
                        <input type="text" name="tempat_tinggal" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Kota tempat tinggal saat ini">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                        <select name="gender" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kode Diskon (Opsional)</label>
                        <input type="text" name="discount_code" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Masukkan kode diskon jika ada">
                    </div>
                </div>
                
                <div class="flex gap-3 mt-6">
                    <button type="submit" 
                            class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition-all">
                        <i class="fas fa-paper-plane mr-2"></i> Daftar Sekarang
                    </button>
                    <button type="button" onclick="hideRegistrationForm()" 
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

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
        function showRegistrationForm(courseId) {
            console.log('Showing registration form for course:', courseId);
            document.getElementById('course_id').value = courseId;
            document.getElementById('registrationModal').classList.remove('hidden');
        }
        
        function hideRegistrationForm() {
            console.log('Hiding registration form');
            document.getElementById('registrationModal').classList.add('hidden');
            document.getElementById('course_id').value = '';
        }
        
        // Close modal when clicking outside
        document.getElementById('registrationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideRegistrationForm();
            }
        });

        // HAPUS SEMUA EVENT LISTENER FORM YANG LAIN
        // Biarkan form submit secara normal tanpa JavaScript interference
        // TIDAK ADA EVENT LISTENER UNTUK FORM SUBMISSION
    </script>

</body>
</html>