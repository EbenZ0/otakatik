<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - OtakAtik Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .progress-ring {
            transform: rotate(-90deg);
        }
        .progress-ring__circle {
            transition: stroke-dashoffset 0.35s;
            transform: rotate(90deg);
            transform-origin: 50% 50%;
        }
    </style>
</head>
<body class="bg-gray-50">
    
    @include('components.navbar')

    <!-- My Courses Section -->
    <section class="pt-32 pb-20 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">My Courses</h1>
                <p class="text-gray-600 text-lg">Lihat progress dan kelola kursus yang Anda ikuti</p>
            </div>

            @if($enrolledCourses->count() > 0)
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($enrolledCourses as $registration)
                    @php
                        $course = $registration->course;
                        $progress = $registration->progress;
                        $strokeDasharray = 2 * 3.1416 * 45; // 2*pi*r
                        $strokeDashoffset = $strokeDasharray - ($progress / 100) * $strokeDasharray;
                    @endphp
                    
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300">
                        <!-- Course Header -->
                        <div class="h-32 bg-gradient-to-r from-blue-500 to-purple-600 relative">
                            <div class="absolute bottom-4 left-4 text-white">
                                <h3 class="text-xl font-bold">{{ $course->title }}</h3>
                                <p class="text-sm opacity-90">{{ $course->type }}</p>
                            </div>
                        </div>
                        
                        <!-- Course Content -->
                        <div class="p-6">
                            <!-- Progress Info -->
                            <div class="mb-4">
                                <div class="flex justify-between text-sm text-gray-600 mb-2">
                                    <span>Progress Belajar</span>
                                    <span class="font-bold text-green-600">{{ $progress }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>
                            
                            <!-- Course Details -->
                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user-tie w-4"></i>
                                    <span>Instruktur: {{ $course->instructor->name ?? 'Tidak tersedia' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar w-4"></i>
                                    <span>Bergabung: 
                                        @if($registration->enrolled_at)
                                            {{ $registration->enrolled_at->format('d M Y') }}
                                        @else
                                            {{ $registration->created_at->format('d M Y') }}
                                        @endif
                                    </span>
                                </div>
                                @if($registration->completed_at)
                                <div class="flex items-center gap-2 text-green-600">
                                    <i class="fas fa-check-circle w-4"></i>
                                    <span>Selesai: {{ $registration->completed_at->format('d M Y') }}</span>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <a href="{{ route('course.show.detail', $course->id) }}" 
                                   class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-center py-2 px-4 rounded-lg transition-all text-sm font-medium">
                                    <i class="fas fa-play mr-1"></i> Lanjutkan
                                </a>
                                <button class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg transition-all text-sm">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16 bg-white rounded-2xl shadow-lg">
                    <div class="max-w-md mx-auto">
                        <div class="w-32 h-32 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-book-open text-white text-4xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Anda Tidak Memiliki Course</h3>
                        <p class="text-gray-600 mb-8">Mulai perjalanan belajar Anda dengan mendaftar course pertama!</p>
                        <a href="/course" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold px-8 py-4 rounded-lg transition-all hover:scale-105 shadow-lg">
                            <i class="fas fa-shopping-cart mr-2"></i> Beli Course Sekarang
                        </a>
                    </div>
                </div>
            @endif

            <!-- Overall Progress Card -->
            @if($enrolledCourses->count() > 0)
            @php
                // Calculate overall progress directly in view
                $totalProgress = 0;
                $courseCount = $enrolledCourses->count();
                
                foreach($enrolledCourses as $registration) {
                    $totalProgress += $registration->progress;
                }
                $calculatedOverallProgress = $courseCount > 0 ? $totalProgress / $courseCount : 0;
            @endphp

            <div class="mt-12 bg-gradient-to-r from-blue-600 to-purple-700 rounded-2xl shadow-lg p-8 text-white">
                <h3 class="text-2xl font-bold mb-6 text-center">Progress Belajar Keseluruhan</h3>
                
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="text-4xl font-bold mb-2">{{ $enrolledCourses->count() }}</div>
                        <p class="text-blue-100">Total Course</p>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold mb-2">{{ $enrolledCourses->where('progress', 100)->count() }}</div>
                        <p class="text-blue-100">Course Selesai</p>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold mb-2">{{ number_format($calculatedOverallProgress, 1) }}%</div>
                        <p class="text-blue-100">Rata-rata Progress</p>
                    </div>
                </div>
                
                <!-- Progress Bars for Each Course -->
                <div class="mt-8 space-y-4">
                    <h4 class="text-lg font-semibold mb-4">Detail Progress per Course</h4>
                    @foreach($enrolledCourses as $registration)
                    <div class="flex items-center justify-between">
                        <span class="text-sm">{{ $registration->course->title }}</span>
                        <div class="flex items-center gap-3">
                            <div class="w-32 bg-blue-400 rounded-full h-2">
                                <div class="bg-white h-2 rounded-full" style="width: {{ $registration->progress }}%"></div>
                            </div>
                            <span class="text-sm font-bold w-12 text-right">{{ $registration->progress }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>
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

    <script>
        // Animate progress rings on page load
        document.addEventListener('DOMContentLoaded', function() {
            const rings = document.querySelectorAll('.progress-ring__circle');
            rings.forEach(ring => {
                const progress = parseInt(ring.parentElement.parentElement.querySelector('span').textContent);
                const strokeDasharray = 2 * 3.1416 * 36;
                const strokeDashoffset = strokeDasharray - (progress / 100) * strokeDasharray;
                ring.style.strokeDasharray = strokeDasharray;
                ring.style.strokeDashoffset = strokeDashoffset;
            });
        });
    </script>

</body>
</html>