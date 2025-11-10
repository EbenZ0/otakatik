<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseRegistration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@otakatik.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'is_instructor' => false,
        ]);

        // Create instructor users
        User::factory()->create([
            'name' => 'John Instructor',
            'email' => 'instructor@otakatik.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'is_instructor' => true,
            'bio' => 'Experienced web development instructor with 5+ years of teaching experience.',
            'expertise' => 'Web Development, JavaScript, PHP',
        ]);

        User::factory()->create([
            'name' => 'Sarah Teacher',
            'email' => 'sarah@otakatik.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'is_instructor' => true,
            'bio' => 'UI/UX designer and frontend development expert.',
            'expertise' => 'UI/UX Design, React, Figma',
        ]);

        // Create regular users
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'is_instructor' => false,
        ]);

        // Create 7 more regular users
        User::factory(7)->create();

        // Create sample courses
        $courses = [
            [
                'title' => 'Frontend Development Full Online',
                'description' => 'Learn modern frontend development with HTML, CSS, JavaScript and React. Build responsive web applications from scratch.',
                'type' => 'online',
                'instructor_id' => 2, // John Instructor
                'price' => 299000,
                'discount_code' => 'PROMOPNJ',
                'discount_percent' => 10,
                'min_quota' => 5,
                'max_quota' => 30,
                'is_active' => true,
            ],
            [
                'title' => 'Backend Development Hybrid',
                'description' => 'Master backend development with Node.js, Express, and MongoDB. Learn to build RESTful APIs and database design.',
                'type' => 'hybrid',
                'instructor_id' => 2, // John Instructor
                'price' => 399000,
                'discount_percent' => 0,
                'min_quota' => 3,
                'max_quota' => 20,
                'is_active' => true,
            ],
            [
                'title' => 'Fullstack Development Tatap Muka',
                'description' => 'Complete fullstack development course covering both frontend and backend technologies with hands-on projects.',
                'type' => 'offline',
                'instructor_id' => 3, // Sarah Teacher
                'price' => 499000,
                'discount_code' => 'PROMOPNJ',
                'discount_percent' => 15,
                'min_quota' => 5,
                'max_quota' => 15,
                'is_active' => true,
            ],
            [
                'title' => 'UI/UX Design Full Online',
                'description' => 'Learn user interface and user experience design principles. Create beautiful and functional designs with Figma.',
                'type' => 'online',
                'instructor_id' => 3, // Sarah Teacher
                'price' => 249000,
                'discount_percent' => 0,
                'min_quota' => 5,
                'max_quota' => 25,
                'is_active' => true,
            ],
        ];

        foreach ($courses as $courseData) {
            Course::create($courseData);
        }

        // Create sample course registrations for Frontend course
        for ($i = 4; $i <= 10; $i++) {
            CourseRegistration::create([
                'user_id' => $i,
                'course_id' => 1, // Frontend course
                'nama_lengkap' => 'Student ' . $i,
                'ttl' => 'Jakarta, 01 Januari 2000',
                'tempat_tinggal' => 'Jakarta',
                'gender' => $i % 2 == 0 ? 'Laki-laki' : 'Perempuan',
                'price' => 299000,
                'final_price' => 269100, // After 10% discount
                'discount_code' => 'PROMOPNJ',
                'status' => 'paid',
                'progress' => rand(0, 100),
                'enrolled_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        // Create some pending registrations
        CourseRegistration::create([
            'user_id' => 4,
            'course_id' => 2, // Backend course
            'nama_lengkap' => 'Student 4',
            'ttl' => 'Bandung, 15 Maret 1999',
            'tempat_tinggal' => 'Bandung',
            'gender' => 'Laki-laki',
            'price' => 399000,
            'final_price' => 399000,
            'discount_code' => null,
            'status' => 'pending',
            'progress' => 0,
            'enrolled_at' => null,
        ]);

        // Update course enrollment counts
        $this->updateCourseEnrollments();
    }

    /**
     * Update course enrollment counts based on paid registrations
     */
    private function updateCourseEnrollments()
    {
        $courses = Course::all();
        
        foreach ($courses as $course) {
            $enrollmentCount = CourseRegistration::where('course_id', $course->id)
                ->where('status', 'paid')
                ->count();
                
            $course->update(['current_enrollment' => $enrollmentCount]);
        }
    }
}