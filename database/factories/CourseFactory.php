<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['online', 'hybrid', 'offline'];
        $type = $this->faker->randomElement($types);
        
        $titles = [
            'online' => [
                'Frontend Development Full Online',
                'Backend Programming Online Course',
                'Data Science Complete Online',
                'UI/UX Design Digital Class',
                'Mobile App Development Online'
            ],
            'hybrid' => [
                'Fullstack Development Hybrid',
                'Digital Marketing Hybrid Program',
                'Cloud Computing Hybrid Course',
                'Cyber Security Hybrid Training'
            ],
            'offline' => [
                'Advanced Programming Tatap Muka',
                'Web Development Intensive Class',
                'Software Engineering Bootcamp',
                'Database Design Workshop'
            ]
        ];

        $prices = [199000, 299000, 399000, 499000, 599000];
        
        return [
            'title' => $this->faker->randomElement($titles[$type]),
            'description' => $this->faker->paragraph(4),
            'type' => $type,
            'instructor_id' => \App\Models\User::where('is_instructor', true)->inRandomOrder()->first()->id ?? null,
            'price' => $this->faker->randomElement($prices),
            'discount_code' => $this->faker->randomElement([null, 'PROMOPNJ', 'DISKON10', 'NEWSTUDENT']),
            'discount_percent' => $this->faker->randomElement([0, 10, 15, 20]),
            'min_quota' => $this->faker->numberBetween(3, 5),
            'max_quota' => $this->faker->numberBetween(15, 30),
            'current_enrollment' => $this->faker->numberBetween(0, 20),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
            'image_url' => null,
        ];
    }

    /**
     * Indicate that the course is online.
     */
    public function online(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'online',
            'instructor_id' => null, // Online courses might not have instructor
        ]);
    }

    /**
     * Indicate that the course is hybrid.
     */
    public function hybrid(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'hybrid',
        ]);
    }

    /**
     * Indicate that the course is offline.
     */
    public function offline(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'offline',
        ]);
    }

    /**
     * Indicate that the course is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the course is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}