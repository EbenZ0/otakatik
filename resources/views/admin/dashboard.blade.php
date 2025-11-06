<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - OtakAtik Academy</title>
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
        
        .card-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .stats-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .finance-card {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .users-card {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        
        .refund-card {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }
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
                        <a href="/admin/dashboard" class="flex items-center gap-3 px-4 py-3 bg-blue-600 rounded-lg text-white">
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
                        <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-chart-bar w-5"></i>
                            <span>Financial Analytics</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
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
                        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                        <p class="text-gray-600">Welcome back, {{ Auth::user()->name }}!</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Admin Panel</p>
                            <p class="text-sm font-medium text-gray-800">{{ date('l, F j, Y') }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Participants / Users -->
                    <div class="users-card rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90 mb-1">Participants / Users</p>
                                <p class="text-3xl font-bold">{{ $stats['total_users'] }}</p>
                                <p class="text-xs opacity-80 mt-2">Total registered users</p>
                            </div>
                            <i class="fas fa-users text-3xl opacity-80"></i>
                        </div>
                    </div>

                    <!-- Financial Analytics -->
                    <div class="finance-card rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90 mb-1">Financial Analytics</p>
                                <p class="text-3xl font-bold">Rp{{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                                <p class="text-xs opacity-80 mt-2">Total revenue</p>
                            </div>
                            <i class="fas fa-chart-line text-3xl opacity-80"></i>
                        </div>
                    </div>

                    <!-- Total Courses -->
                    <div class="card-gradient rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90 mb-1">Total Courses</p>
                                <p class="text-3xl font-bold">{{ $stats['total_courses'] }}</p>
                                <p class="text-xs opacity-80 mt-2">All course registrations</p>
                            </div>
                            <i class="fas fa-book-open text-3xl opacity-80"></i>
                        </div>
                    </div>

                    <!-- Refund Management -->
                    <div class="refund-card rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90 mb-1">Refund Management</p>
                                <p class="text-3xl font-bold">{{ $stats['cancelled_courses'] }}</p>
                                <p class="text-xs opacity-80 mt-2">Cancelled courses</p>
                            </div>
                            <i class="fas fa-exchange-alt text-3xl opacity-80"></i>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Recent Registrations -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-800">Recent Registrations</h3>
                            <a href="/admin/courses" class="text-blue-500 hover:text-blue-600 text-sm font-medium">
                                View All →
                            </a>
                        </div>
                        
                        <div class="space-y-4">
                            @forelse($recent_registrations as $registration)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border-l-4 
                                @if($registration->status == 'paid') border-green-500 
                                @elseif($registration->status == 'pending') border-yellow-500 
                                @else border-red-500 @endif">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">{{ substr($registration->user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $registration->user->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $registration->course }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        @if($registration->status == 'paid') bg-green-100 text-green-800
                                        @elseif($registration->status == 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($registration->status) }}
                                    </span>
                                    <p class="text-sm text-gray-500 mt-1">{{ $registration->created_at->format('M d, H:i') }}</p>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-book-open text-3xl mb-3 opacity-50"></i>
                                <p>No recent registrations</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Popular Courses & Quick Actions -->
                    <div class="space-y-8">
                        <!-- Popular Courses -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-6">Popular Courses</h3>
                            
                            <div class="space-y-4">
                                @foreach($popular_courses as $course)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-book text-white text-sm"></i>
                                        </div>
                                        <span class="font-medium text-gray-800">{{ $course->course }}</span>
                                    </div>
                                    <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                                        {{ $course->count }} students
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-6">Quick Actions</h3>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <a href="/admin/courses" class="p-4 bg-blue-50 rounded-lg text-center hover:bg-blue-100 transition-colors group">
                                    <i class="fas fa-book text-blue-500 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                                    <p class="font-medium text-blue-800">Manage Courses</p>
                                </a>
                                
                                <a href="/admin/users" class="p-4 bg-green-50 rounded-lg text-center hover:bg-green-100 transition-colors group">
                                    <i class="fas fa-users text-green-500 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                                    <p class="font-medium text-green-800">Manage Users</p>
                                </a>
                                
                                <a href="/admin/courses?status=pending" class="p-4 bg-yellow-50 rounded-lg text-center hover:bg-yellow-100 transition-colors group">
                                    <i class="fas fa-clock text-yellow-500 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                                    <p class="font-medium text-yellow-800">Pending Payments</p>
                                </a>
                                
                                <a href="/admin/courses/export" class="p-4 bg-purple-50 rounded-lg text-center hover:bg-purple-100 transition-colors group">
                                    <i class="fas fa-download text-purple-500 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                                    <p class="font-medium text-purple-800">Export Data</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Summary -->
                <div class="mt-8 bg-gradient-to-r from-blue-600 to-purple-700 rounded-2xl shadow-lg p-6 text-white">
                    <h3 class="text-xl font-bold mb-6">Financial Summary</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <p class="text-sm opacity-90">Total Revenue</p>
                            <p class="text-2xl font-bold mt-2">Rp{{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm opacity-90">Paid Courses</p>
                            <p class="text-2xl font-bold mt-2">{{ $stats['paid_courses'] }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm opacity-90">Pending Payments</p>
                            <p class="text-2xl font-bold mt-2">{{ $stats['pending_courses'] }}</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Success Message Handler -->
    @if(session('success'))
    <div class="fixed top-6 right-6 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 animate-fade-in flex items-center gap-3">
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

</body>
</html>