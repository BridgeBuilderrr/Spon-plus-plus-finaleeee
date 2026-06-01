<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ariel = User::where('username', 'arielkinosaki')->first();
        $admin = User::where('username', 'sponmaster')->first();
        $students = User::where('role', 'Member')->get();

        // 1. Ariel's Tech Class (Mega Class)
        $techClass = Classroom::create([
            'title' => 'Advanced AI & UI/UX Patterns',
            'code' => 'SPON202',
            'description' => 'Deep dive into modern agentic AI andpremium glassmorphism design patterns.',
            'tags' => ['AI', 'UI/UX', 'Design'],
            'teacher_id' => $ariel->id
        ]);
        $techClass->users()->attach($ariel->id, ['role' => 'Teacher', 'last_accessed_at' => now()]);
        
        // Attach all students to Ariel's class
        foreach ($students as $student) {
            $techClass->users()->attach($student->id, [
                'role' => 'Member', 
                'last_accessed_at' => now(), 
                'is_starred' => ($student->id % 2 == 0)
            ]);
        }

        // Add some materials & assignments
        $techClass->materials()->create([
            'title' => 'Introduction to Glassmorphism',
            'description' => '<p>Check out these CSS variables for premium blur effects.</p>',
            'files' => [json_encode(['name' => 'design_guide.pdf', 'path' => 'materials/sample.pdf', 'size' => 1024 * 1024 * 2])]
        ]);

        $techClass->assignments()->create([
            'title' => 'Create a Premium Button Component',
            'description' => '<p>Submit your CSS code for a button with hover animations.</p>',
            'due_date' => now()->addDays(5),
        ]);

        // 2. Admin Class
        $adminClass = Classroom::create([
            'title' => 'System Architecture',
            'code' => 'SYSADMIN',
            'description' => 'Managing large scale deployments.',
            'tags' => ['DevOps', 'Scaling'],
            'teacher_id' => $admin->id
        ]);
        $adminClass->users()->attach($admin->id, ['role' => 'Teacher']);
        $adminClass->users()->attach($students[0]->id, ['role' => 'Member']);
    }
}
