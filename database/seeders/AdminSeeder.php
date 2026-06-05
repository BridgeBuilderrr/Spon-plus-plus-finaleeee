<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure Admin User exists
        $admin = User::updateOrCreate(
            ['username' => 'admin_super'],
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@spon.test',
                'password' => Hash::make('password'),
                'role' => 'Teacher',
                'bio' => 'System administrator for SPON++ platform.',
            ]
        );

        // 2. Create more teachers if needed
        $teacherData = [
            'math_pro' => 'Mathematics Professor',
            'science_wiz' => 'Science Wizard',
            'history_buff' => 'History Buff',
            'coding_guru' => 'Coding Guru',
            'art_legend' => 'Art Legend',
        ];

        $teachers = [$admin];
        foreach ($teacherData as $username => $name) {
            $teachers[] = User::updateOrCreate(
                ['username' => $username],
                [
                    'name' => $name,
                    'email' => $username . '@spon.test',
                    'password' => Hash::make('password'),
                    'role' => 'Teacher',
                ]
            );
        }

        // 3. Ensure we have members
        if (User::where('role', 'Member')->count() < 20) {
            for ($i = 1; $i <= 20; $i++) {
                User::updateOrCreate(
                    ['username' => 'member_' . $i],
                    [
                        'name' => 'Test Member ' . $i,
                        'email' => 'member' . $i . '@spon.test',
                        'password' => Hash::make('password'),
                        'role' => 'Member',
                    ]
                );
            }
        }
        $members = User::where('role', 'Member')->get();

        // 4. Create 34 Classes
        $classNames = [
            'Quantum Computing Fundamentals', 'Modern Web Architectures', 'UI/UX Design Masterclass',
            'Advanced Laravel Patterns', 'Database Optimization Strategies', 'Cloud Native Deployments',
            'AI and Machine Learning 101', 'Cybersecurity Best Practices', 'Blockchain Decoded',
            'Mobile App Development with Flutter', 'React vs Vue: The Ultimate Guide', 'DevOps with Kubernetes',
            'Introduction to Rust', 'Go Programming for Backend', 'Data Science with Python',
            'Product Management Essentials', 'Digital Marketing Strategies', 'Entrepreneurship & Startups',
            'Discrete Mathematics', 'Organic Chemistry for Engineers', 'World History: Medieval Era',
            'English Literature: Shakespearean Plays', 'Classical Music Theory', 'Introduction to Psychology',
            'Political Science & Global Relations', 'Environmental Science & Sustainability', 'Anatomy for Artists',
            'Financial Literacy for Professionals', 'Project Management (PMP Prep)', 'Public Speaking & Leadership',
            'Video Editing with Premiere Pro', '3D Modeling with Blender', 'Game Development with Unity',
            'Photography & Visual Storytelling'
        ];

        // Ensure we have exactly 34 class names
        while (count($classNames) < 34) {
            $classNames[] = "Generic Elective Class " . (count($classNames) + 1);
        }
        
        // Trim to exactly 34 if somehow over (though hardcoded above is exactly 34)
        $classNames = array_slice($classNames, 0, 34);

        foreach ($classNames as $index => $title) {
            $code = 'SPON-' . str_pad($index + 101, 3, '0', STR_PAD_LEFT);
            $teacher = $teachers[array_rand($teachers)];

            $classroom = Classroom::updateOrCreate(
                ['code' => $code],
                [
                    'title' => $title,
                    'description' => 'Comprehensive course material for ' . $title . '. Join us to master these skills.',
                    'tags' => [Str::slug($title), 'edu', '2026'],
                    'teacher_id' => $teacher->id,
                ]
            );

            // Attach Teacher to the classroom pivot
            $classroom->users()->syncWithoutDetaching([
                $teacher->id => ['role' => 'Teacher', 'last_accessed_at' => now()]
            ]);

            // Randomly attach 5-10 members to each class
            $randomMembers = $members->random(min(rand(5, 12), $members->count()));
            foreach ($randomMembers as $member) {
                $classroom->users()->syncWithoutDetaching([
                    $member->id => [
                        'role' => 'Member',
                        'last_accessed_at' => now(),
                        'is_starred' => (rand(0, 10) > 8) // ~20% star rate
                    ]
                ]);
            }
        }

        $this->command->info('AdminSeeder: Successfully seeded ' . count($classNames) . ' classes and associated users.');
    }
}
