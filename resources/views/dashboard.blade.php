<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - OtakAtik Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    
    @auth
        @include('components.navbar')
    @else
        <!-- Navbar untuk guest -->
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
                    <a href="#about" class="text-gray-700 hover:text-orange-500 font-medium transition">About Us</a>
                    <a href="/course" class="text-gray-700 hover:text-orange-500 font-medium transition">Our Course</a>
                </div>
                
                <!-- Sign In Button -->
                <a href="/login" class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-6 py-2.5 rounded-lg transition-all hover:scale-105 shadow-lg">
                    Sign In
                </a>
            </div>
        </nav>
    @endauth

    <!-- Hero Section -->
    <section class="pt-32 pb-20 px-6 bg-gradient-to-b from-white to-gray-50">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-12">
            <div class="md:w-1/2 animate-fade-in">
                <h1 class="text-5xl font-bold text-gray-800 mb-4 leading-tight">
                    Welcome To<br>
                    <span class="text-orange-500">OtakAtik Acandemy</span> by PNJ
                </h1>
                <p class="text-gray-600 text-lg mb-8 leading-relaxed">
                    Kini program otak-atik ini hadir lho, untuk putra-putrimu dibuat yang merancang masa depan!
                </p>
                <a href="/course" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-4 rounded-lg transition-all hover:scale-105 shadow-lg inline-block">
                    Start Learning Now
                </a>
            </div>
            <div class="md:w-1/2 animate-fade-in">
                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=600&h=400&fit=crop" 
                     alt="Students Learning" 
                     class="rounded-2xl shadow-custom w-full">
            </div>
        </div>
    </section>

    <!-- The Story Section -->
    <section id="about" class="py-20 px-6 bg-gradient-to-br from-orange-500 to-orange-600 text-white">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-12">
            <div class="md:w-1/2">
                <h2 class="text-4xl font-bold mb-6">THE STORY</h2>
                <p class="text-white/90 text-lg leading-relaxed mb-4">
                    OtakAtik lahir dari kegelisahan kami ini, bahwa dari pengalaman kami yang cukup panjang dalam mengajar bimbel, banyak anak yang memiliki potensi namun terkendala oleh tiga hal yang menghambat proses belajarnya.
                </p>
                <p class="text-white/90 text-lg leading-relaxed mb-4">
                    Pertama, tidak paham konsep dasar pelajaran. Kedua, tidak terbiasa dengan soal-soal yang menantang yang melatih logika berpikir. Ketiga, mudah menyerah saat menghadapi soal yang menantang, atau bisa dikatakan tidak terbiasa dengan soal level olimpiade.
                </p>
                <p class="text-white/90 text-lg leading-relaxed">
                    Tiga masalah inilah yang menurut saya membuat anak-anak ini tidak optimal dalam mengeksplorasi potensinya. Hal ini tentu juga berdampak pada masa depannya kelak.
                </p>
            </div>
            <div class="md:w-1/2">
                <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=500&h=600&fit=crop" 
                     alt="Student" 
                     class="rounded-2xl shadow-2xl w-full">
            </div>
        </div>
    </section>

    <!-- Become The Best Section -->
    <section class="py-20 px-6 bg-white">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-12">
            <div class="md:w-1/2">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=600&h=400&fit=crop" 
                     alt="Students in Class" 
                     class="rounded-2xl shadow-custom w-full">
            </div>
            <div class="md:w-1/2">
                <h2 class="text-4xl font-bold text-gray-800 mb-6">
                    Become The Best<br>Student In Class
                </h2>
                <p class="text-gray-600 text-lg leading-relaxed mb-6">
                    YUK BELAJAR dengan OtakAtik Academy juga mulai menangani keternak-malas, kelik suka membuang-buang waktu, bahkan anak yang tidak percaya diri.
                </p>
                <a href="/course" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-4 rounded-lg transition-all hover:scale-105 shadow-lg inline-block">
                    Start Now!
                </a>
            </div>
        </div>
    </section>

    <!-- Kenapa Harus Belajar Section -->
    <section class="py-20 px-6 bg-gradient-to-br from-blue-700 to-blue-900 text-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center md:text-left mb-12">
                <h2 class="text-4xl font-bold mb-6">
                    Kenapa Harus Belajar Di<br>OtakAtik Academy?
                </h2>
                <div class="flex flex-col md:flex-row gap-12 items-center">
                    <div class="md:w-1/2">
                        <p class="text-white/90 text-lg leading-relaxed mb-6">
                            OtakAtik hadir dengan pelajaran Matematika dan IPA yang didesign sedemikian rupa untuk anak-anak yang dirasa memiliki potensi nilai tingkat nilai yang bisa digali dan diasah. Tentu dengan bimbingan dari pengajar yang expert dan berpengalaman, Karena di OtakAtik kamu terbiasa dengan konsep dan soal level olimpiade sejak dini, saat SMA nanti OtakAtik pun yakin anak-anak ini juga tidak akan kesulitan dalam mengerjakan soal olimpiade bahkan soal level PTS/OSN/INSO. Ini peningkatan keren yang bisa kami berikan ke anak-anak kalian.
                        </p>
                    </div>
                    <div class="md:w-1/2 flex gap-4">
                        <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=300&h=200&fit=crop" 
                             alt="Teacher" 
                             class="rounded-xl shadow-xl w-1/2">
                        <div class="flex flex-col gap-4 w-1/2">
                            <img src="https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?w=300&h=150&fit=crop" 
                                 alt="Students" 
                                 class="rounded-xl shadow-xl">
                            <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?w=300&h=150&fit=crop" 
                                 alt="Classroom" 
                                 class="rounded-xl shadow-xl">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-6 bg-white">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Tunggu apalagi?</h2>
            <p class="text-2xl text-gray-700 mb-8">
                Saatnya upgrade skill bareng<br><span class="font-bold">OtakAtik.</span>
            </p>
            <a href="/course" class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-12 py-4 rounded-lg transition-all hover:scale-105 shadow-lg text-lg inline-block">
                Join Now!
            </a>
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