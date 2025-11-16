<!-- Navbar with Profile Dropdown and Notifications -->
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
            <a href="/my-courses" class="text-gray-700 hover:text-orange-500 font-medium transition">My Courses</a>
            <a href="/purchase-history" class="text-gray-700 hover:text-orange-500 font-medium transition">History</a>
        </div>
        
        <!-- Right Section: Notifications and Profile -->
        <div class="flex items-center gap-6">
            <!-- Notification Bell -->
            <div class="relative">
                @php
                    $unreadNotifications = Auth::user()->notifications()
                        ->whereNull('read_at')
                        ->orderBy('created_at', 'desc')
                        ->get();
                    $unreadCount = $unreadNotifications->count();
                @endphp
                
                <button id="notificationBtn" class="relative text-gray-600 hover:text-orange-500 transition text-xl">
                    <i class="fas fa-bell"></i>
                    <!-- Notification Badge -->
                    @if($unreadCount > 0)
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                    </span>
                    @endif
                </button>
                
                <!-- Notification Dropdown -->
                <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50 max-h-96 overflow-y-auto">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-800">Notifications</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($unreadNotifications->take(5) as $notification)
                            <div class="p-4 hover:bg-gray-50 cursor-pointer transition" onclick="markAsRead({{ $notification->id }})">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        @if($notification->type === 'course_purchased')
                                            <i class="fas fa-book text-blue-600"></i>
                                        @elseif($notification->type === 'assignment_posted')
                                            <i class="fas fa-tasks text-purple-600"></i>
                                        @elseif($notification->type === 'quiz_posted')
                                            <i class="fas fa-file-alt text-green-600"></i>
                                        @elseif($notification->type === 'material_posted')
                                            <i class="fas fa-file-pdf text-red-600"></i>
                                        @else
                                            <i class="fas fa-bell text-blue-600"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $notification->message }}</p>
                                        <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-gray-500">
                                <p class="text-sm">No new notifications</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="p-3 border-t border-gray-200 text-center">
                        <a href="{{ route('notifications.index') }}" class="text-sm text-orange-500 hover:text-orange-600 font-medium">View All Notifications</a>
                    </div>
                </div>
            </div>
            
            <!-- Profile Dropdown -->
            <div class="relative">
                <button id="profileBtn" class="flex items-center gap-3 hover:bg-gray-100 rounded-full p-1 transition">
                    <!-- Circular Profile Avatar -->
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                </button>
                
                <!-- Profile Dropdown Menu -->
                <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl z-50">
                    <!-- Header with name -->
                    <div class="p-4 border-b border-gray-200">
                        <p class="font-semibold text-gray-900">{{ Auth::user()->name ?? 'User' }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ Auth::user()->email ?? 'user@email.com' }}</p>
                    </div>
                    
                    <!-- Menu Items -->
                    <div class="py-2">
                        <a href="/profile" class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                            <i class="fas fa-user w-4"></i>
                            <span>My Profile</span>
                        </a>
                        <a href="/purchase-history" class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                            <i class="fas fa-history w-4"></i>
                            <span>Purchase History</span>
                        </a>
                        <a href="/settings" class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                            <i class="fas fa-cog w-4"></i>
                            <span>Settings</span>
                        </a>
                        <a href="/achievements" class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                            <i class="fas fa-trophy w-4"></i>
                            <span>Achievements</span>
                        </a>
                        <a href="/help" class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                            <i class="fas fa-question-circle w-4"></i>
                            <span>Help Center</span>
                        </a>
                    </div>
                    
                    <!-- Divider -->
                    <div class="border-t border-gray-200 py-2">
                        <form action="/logout" method="POST" class="block">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-red-600 hover:bg-red-50 transition">
                                <i class="fas fa-sign-out-alt w-4"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Dropdown Toggle Scripts -->
<script>
    // Mark notification as read
    function markAsRead(notificationId) {
        fetch(`/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Content-Type': 'application/json',
            }
        }).then(() => {
            location.reload();
        });
    }
    
    // Profile Dropdown
    const profileBtn = document.getElementById('profileBtn');
    const profileDropdown = document.getElementById('profileDropdown');
    
    profileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        profileDropdown.classList.toggle('hidden');
        notificationDropdown.classList.add('hidden');
    });
    
    // Notification Dropdown
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    
    notificationBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        notificationDropdown.classList.toggle('hidden');
        profileDropdown.classList.add('hidden');
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', () => {
        profileDropdown.classList.add('hidden');
        notificationDropdown.classList.add('hidden');
    });
</script>
