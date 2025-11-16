<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }} - OtakAtik Academy</title>
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

    <!-- Course Detail Section -->
    <section class="pt-32 pb-20 px-6">
        <div class="max-w-4xl mx-auto">
            <!-- Course Header -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                <div class="h-48 bg-gradient-to-r from-blue-500 to-purple-600 relative">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                        <div class="text-center text-white">
                            <h1 class="text-4xl font-bold mb-4">{{ $course->title }}</h1>
                            <p class="text-xl opacity-90">{{ $course->type }} Course</p>
                            <p class="text-lg opacity-80 mt-2">by {{ $course->instructor->name ?? 'Tidak tersedia' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Content -->
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="md:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Course Description</h2>
                        <p class="text-gray-600 leading-relaxed">{{ $course->description }}</p>
                    </div>

                    <!-- Course Materials -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Course Materials</h2>
                        @if($course->materials->count() > 0)
                            <div class="space-y-4">
                                @foreach($course->materials as $material)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-blue-300 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-file-pdf text-white text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800">{{ $material->title }}</h4>
                                            @if($material->description)
                                            <p class="text-sm text-gray-600">{{ $material->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @if($isEnrolled)
                                    <a href="/storage/{{ $material->file_path }}" 
                                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-all flex items-center gap-2"
                                       target="_blank">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    @else
                                    <span class="text-gray-500 text-sm">Enroll to access</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">No materials available yet.</p>
                        @endif
                    </div>

                    <!-- Assignments -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Course Assignments</h2>
                        @if($course->assignments->count() > 0)
                            <div class="space-y-6">
                                @foreach($course->assignments as $assignment)
                                <div class="border border-gray-200 rounded-lg p-6 hover:border-blue-300 transition-colors">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h4 class="text-xl font-bold text-gray-800">{{ $assignment->title }}</h4>
                                            @if($assignment->description)
                                            <p class="text-gray-600 mt-2">{{ $assignment->description }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm text-gray-500">Due Date</div>
                                            <div class="font-semibold text-gray-800">{{ $assignment->due_date->format('d M Y, H:i') }}</div>
                                            <div class="text-sm text-blue-600 font-medium">{{ $assignment->max_points }} Points</div>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                        <h5 class="font-semibold text-gray-800 mb-2">Instructions:</h5>
                                        <p class="text-gray-700">{{ $assignment->instructions }}</p>
                                    </div>
                                    
                                    @if($isEnrolled)
                                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                                        <i class="fas fa-upload"></i> Submit Assignment
                                    </button>
                                    @else
                                    <p class="text-gray-500 text-sm">Enroll to submit assignments</p>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">No assignments available yet.</p>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="md:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-32">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Course Info</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600">Instructor</p>
                                <p class="font-semibold text-gray-800">{{ $course->instructor->name ?? 'Tidak tersedia' }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600">Course Type</p>
                                <p class="font-semibold text-gray-800">{{ $course->type }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600">Price</p>
                                @if($course->discount_percent > 0)
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl font-bold text-green-600">
                                        {{ 'Rp' . number_format($course->price * (1 - $course->discount_percent/100), 0, ',', '.') }}
                                    </span>
                                    <span class="text-lg text-gray-500 line-through">
                                        {{ 'Rp' . number_format($course->price, 0, ',', '.') }}
                                    </span>
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">
                                        -{{ $course->discount_percent }}%
                                    </span>
                                </div>
                                @else
                                <span class="text-2xl font-bold text-gray-800">
                                    {{ 'Rp' . number_format($course->price, 0, ',', '.') }}
                                </span>
                                @endif
                            </div>

                            <div>
                                <p class="text-sm text-gray-600">Available Slots</p>
                                <p class="font-semibold text-gray-800">{{ $course->max_quota - $course->current_enrollment }} / {{ $course->max_quota }}</p>
                            </div>
                        </div>

                        @if(!$isEnrolled)
                        <button onclick="showRegistrationForm({{ $course->id }})" 
                                class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-lg transition-all mt-6">
                            <i class="fas fa-shopping-cart mr-2"></i> Enroll Now
                        </button>
                        @else
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-6">
                            <p class="text-green-800 font-semibold text-center">
                                <i class="fas fa-check-circle mr-2"></i>You are enrolled in this course
                            </p>
                            <p class="text-green-600 text-sm text-center mt-2">Progress: {{ $userRegistration->progress }}%</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Registration Modal -->
    <div id="registrationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Enroll in Course</h3>
            <form action="{{ route('checkout.show', $course->id) }}" method="POST">
                @csrf
                <input type="hidden" name="course_id" id="course_id">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" name="nama_lengkap" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               value="{{ Auth::user()->name }}">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Place, Date of Birth</label>
                        <input type="text" name="ttl" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Example: Jakarta, 15 August 1990">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Residence</label>
                        <input type="text" name="tempat_tinggal" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Your current city">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                        <select name="gender" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Gender</option>
                            <option value="Laki-laki">Male</option>
                            <option value="Perempuan">Female</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Discount Code (Optional)</label>
                        <input type="text" name="discount_code" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Enter discount code if any">
                    </div>
                </div>
                
                <div class="flex gap-3 mt-6">
                    <button type="submit" 
                            class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition-all">
                        <i class="fas fa-paper-plane mr-2"></i> Enroll Now
                    </button>
                    <button type="button" onclick="hideRegistrationForm()" 
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all">
                        Cancel
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
            <p class="text-gray-400 mb-4">Building Smart and Achieving Generations</p>
            <p class="text-gray-500 text-sm">&copy; 2025 OtakAtik Academy. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function showRegistrationForm(courseId) {
            document.getElementById('course_id').value = courseId;
            document.getElementById('registrationModal').classList.remove('hidden');
        }
        
        function hideRegistrationForm() {
            document.getElementById('registrationModal').classList.add('hidden');
        }
        
        // Close modal when clicking outside
        document.getElementById('registrationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideRegistrationForm();
            }
        });
    </script>

</body>
</html>