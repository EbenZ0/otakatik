<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - OtakAtik Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                <a href="/course" class="text-gray-700 hover:text-orange-500 font-medium transition">Our Course</a>
                <a href="/my-courses" class="text-orange-500 font-medium transition">My Courses</a>
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

    <!-- My Courses Section -->
    <section class="pt-32 pb-20 px-6">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">My Courses</h1>
                <p class="text-gray-600 text-lg">Lihat semua course yang telah Anda daftar</p>
            </div>

            @if($courses->count() > 0)
                <div class="grid gap-6">
                    @foreach($courses as $course)
                    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 
                        @if($course->status == 'paid') border-green-500 
                        @elseif($course->status == 'pending') border-yellow-500 
                        @else border-red-500 @endif">
                        <div class="flex flex-col md:flex-row md:items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-4 mb-3">
                                    <h3 class="text-xl font-bold text-gray-800">{{ $course->course }}</h3>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        @if($course->status == 'paid') bg-green-100 text-green-800
                                        @elseif($course->status == 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($course->status) }}
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                    <div>
                                        <p><strong>Nama:</strong> {{ $course->nama_lengkap }}</p>
                                        <p><strong>TTL:</strong> {{ $course->ttl }}</p>
                                        <p><strong>Lokasi:</strong> {{ $course->tempat_tinggal }}</p>
                                        <p><strong>Gender:</strong> {{ $course->gender }}</p>
                                    </div>
                                    <div>
                                        <p><strong>Sub Course 1:</strong> {{ $course->sub_course1 }}</p>
                                        <p><strong>Sub Course 2:</strong> {{ $course->sub_course2 }}</p>
                                        <p><strong>Kelas:</strong> {{ $course->kelas }}</p>
                                        <p><strong>Harga:</strong> Rp{{ number_format($course->price, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 md:mt-0 md:text-right">
                                <p class="text-sm text-gray-500 mb-2">Didaftarkan pada</p>
                                <p class="text-sm font-medium text-gray-700">
                                    {{ $course->created_at->format('d M Y H:i') }}
                                </p>
                                @if($course->status == 'pending')
                                <form action="{{ route('course.destroy', $course->id) }}" method="POST" class="mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm" 
                                            onclick="return confirm('Yakin ingin membatalkan pendaftaran ini?')">
                                        <i class="fas fa-trash mr-1"></i>Batalkan
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-book-open text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum ada course yang didaftarkan</h3>
                    <p class="text-gray-500 mb-6">Yuk daftar course pertama Anda sekarang!</p>
                    <a href="/course" class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-6 py-3 rounded-lg transition-all">
                        Daftar Course Sekarang
                    </a>
                </div>
            @endif
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

</body>
</html>