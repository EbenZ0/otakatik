<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses - OtakAtik Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .sidebar {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-active { background: #d1fae5; color: #065f46; }
        .status-inactive { background: #fee2e2; color: #dc2626; }
        
        .type-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .type-online { background: #dbeafe; color: #1e40af; }
        .type-hybrid { background: #fef3c7; color: #d97706; }
        .type-offline { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar w-64 text-white flex flex-col">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-white">OtakAtik<span class="text-blue-400">Admin</span></h1>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="/admin/dashboard" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-chart-line w-5"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/users" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-users w-5"></i>
                            <span>Participants / Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/courses" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-book w-5"></i>
                            <span>Course Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/courses/manage" class="flex items-center gap-3 px-4 py-3 bg-blue-600 rounded-lg text-white">
                            <i class="fas fa-plus-circle w-5"></i>
                            <span>Tambah Course</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/financial" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-chart-bar w-5"></i>
                            <span>Financial Analytics</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/refund" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-exchange-alt w-5"></i>
                            <span>Refund Management</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- User Section -->
            <div class="p-4 border-t border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">Administrator</p>
                    </div>
                </div>
                <form action="/logout" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-sign-out-alt w-4"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Tambah & Kelola Course</h1>
                        <p class="text-gray-600">Buat dan kelola course yang tersedia di platform</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Total: {{ $courses->count() }} courses</p>
                        <p class="text-sm font-medium text-gray-800">{{ date('M j, Y') }}</p>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Add Course Form -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Tambah Course Baru</h3>
                        
                        <form action="{{ route('admin.courses.create') }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <!-- Course Title -->
                                <div>
                                    <label class="text-sm font-medium text-gray-700 mb-2 block">Judul Course</label>
                                    <input type="text" name="title" required 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Contoh: Frontend Development Full Online">
                                </div>
                                
                                <!-- Course Description -->
                                <div>
                                    <label class="text-sm font-medium text-gray-700 mb-2 block">Deskripsi Course (Opsional)</label>
                                    <textarea name="description" rows="3"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                              placeholder="Deskripsi lengkap tentang course..."></textarea>
                                </div>
                                
                                <!-- Course Type -->
                                <div>
                                    <label class="text-sm font-medium text-gray-700 mb-2 block">Tipe Course</label>
                                    <select name="type" required 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            onchange="toggleInstructorField(this.value)">
                                        <option value="">Pilih Tipe Course</option>
                                        <option value="online">Full Online</option>
                                        <option value="hybrid">Hybrid</option>
                                        <option value="offline">Tatap Muka</option>
                                    </select>
                                </div>
                                
                                <!-- Instructor Selection (Conditional) -->
                                <div id="instructorField" style="display: none;">
                                    <label class="text-sm font-medium text-gray-700 mb-2 block">Assign Instruktur</label>
                                    <select name="instructor_id" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Pilih Instruktur</option>
                                        @foreach($instructors as $instructor)
                                        <option value="{{ $instructor->id }}">{{ $instructor->name }} ({{ $instructor->email }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Pricing -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 mb-2 block">Harga (Rp)</label>
                                        <input type="number" name="price" required min="0"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               placeholder="99000">
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 mb-2 block">Persentase Diskon (%)</label>
                                        <input type="number" name="discount_percent" min="0" max="100"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               placeholder="10">
                                    </div>
                                </div>
                                
                                <!-- Discount Code -->
                                <div>
                                    <label class="text-sm font-medium text-gray-700 mb-2 block">Kode Diskon (Opsional)</label>
                                    <input type="text" name="discount_code"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="PROMOPNJ">
                                </div>
                                
                                <!-- Quota -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 mb-2 block">Kuota Minimal</label>
                                        <input type="number" name="min_quota" required min="1"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               placeholder="5">
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 mb-2 block">Kuota Maksimal</label>
                                        <input type="number" name="max_quota" required min="1"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               placeholder="30">
                                    </div>
                                </div>
                                
                                <!-- Submit Button -->
                                <button type="submit" 
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-all mt-6">
                                    <i class="fas fa-plus-circle mr-2"></i> Tambah Course
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Existing Courses -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Course yang Tersedia</h3>
                        
                        <div class="space-y-4 max-h-96 overflow-y-auto">
                            @foreach($courses as $course)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-gray-800">{{ $course->title }}</h4>
                                    <div class="flex gap-2">
                                        <span class="type-badge type-{{ $course->type }}">
                                            {{ ucfirst($course->type) }}
                                        </span>
                                        <span class="status-badge status-{{ $course->is_active ? 'active' : 'inactive' }}">
                                            {{ $course->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="text-sm text-gray-600 mb-2">
                                    @if($course->instructor)
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-user-tie"></i>
                                        {{ $course->instructor->name }}
                                    </span>
                                    @endif
                                    <span class="flex items-center gap-1 mt-1">
                                        <i class="fas fa-users"></i>
                                        {{ $course->current_enrollment }}/{{ $course->max_quota }} peserta
                                    </span>
                                </div>
                                
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-medium text-gray-800">{{ $course->formatted_final_price }}</span>
                                    @if($course->discount_percent > 0)
                                    <span class="text-green-600">-{{ $course->discount_percent }}%</span>
                                    @endif
                                </div>
                                
                                <div class="flex gap-2 mt-3">
                                    <button onclick="editCourse({{ $course->id }})" 
                                            class="text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.courses.delete', $course->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-800 text-sm"
                                                onclick="return confirm('Yakin ingin menghapus course ini?')">
                                            <i class="fas fa-trash mr-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                            
                            @if($courses->count() === 0)
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-book-open text-3xl mb-3 opacity-50"></i>
                                <p>Belum ada course yang dibuat</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Success Message Handler -->
    @if(session('success'))
    <div class="fixed top-6 right-6 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    <script>
        setTimeout(() => {
            const alert = document.querySelector('.fixed.top-6');
            if(alert) alert.remove();
        }, 5000);
    </script>
    @endif

    <script>
        function toggleInstructorField(type) {
            const instructorField = document.getElementById('instructorField');
            if (type === 'hybrid' || type === 'offline') {
                instructorField.style.display = 'block';
                instructorField.querySelector('select').required = true;
            } else {
                instructorField.style.display = 'none';
                instructorField.querySelector('select').required = false;
            }
        }

        function editCourse(courseId) {
            alert('Fitur edit course akan datang untuk course ID: ' + courseId);
            // In real implementation, you would show a modal or redirect to edit page
        }
    </script>

</body>
</html>