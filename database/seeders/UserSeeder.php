<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Primary Teachers
        User::create([
            'name' => 'Ariel Baru',
            'username' => 'arielkinosaki',
            'email' => 'ariel@spon.test',
            'password' => Hash::make('password'),
            'role' => 'Teacher',
            'bio' => 'Lead Educator at Spon++. Specialist in Modern Web Tech.',
        ]);

        User::create([
            'name' => 'Spon++ Master',
            'username' => 'sponmaster',
            'email' => 'admin@spon.test',
            'password' => Hash::make('password'),
            'role' => 'Teacher',
        ]);

        // More Teachers
        $teachers = [
            ['name' => 'Hideo Kojima', 'username' => 'hideo'],
            ['name' => 'Masahiro Sakurai', 'username' => 'sakurai'],
            ['name' => 'Linus Torvalds', 'username' => 'linus'],
        ];

        foreach ($teachers as $t) {
            User::create([
                'name' => $t['name'],
                'username' => $t['username'],
                'email' => $t['username'] . '@spon.test',
                'password' => Hash::make('password'),
                'role' => 'Teacher',
            ]);
        }

        // Many Members (approx 20+)
        $members = [
            ['name' => 'Naruto Uzumaki', 'username' => 'naruto'],
            ['name' => 'Sasuke Uchiha', 'username' => 'sasuke'],
            ['name' => 'Sakura Haruno', 'username' => 'sakura'],
            ['name' => 'Kakashi Hatake', 'username' => 'kakashi'],
            ['name' => 'Monkey D. Luffy', 'username' => 'luffy'],
            ['name' => 'Roronoa Zoro', 'username' => 'zoro'],
            ['name' => 'Vinsmoke Sanji', 'username' => 'sanji'],
            ['name' => 'Nami Swan', 'username' => 'nami'],
            ['name' => 'Tanjiro Kamado', 'username' => 'tanjiro'],
            ['name' => 'Nezuko Kamado', 'username' => 'nezuko'],
            ['name' => 'Zenitsu Agatsuma', 'username' => 'zenitsu'],
            ['name' => 'Inosuke Hashibira', 'username' => 'inosuke'],
            ['name' => 'Eren Yeager', 'username' => 'eren'],
            ['name' => 'Mikasa Ackerman', 'username' => 'mikasa'],
            ['name' => 'Armin Arlert', 'username' => 'armin'],
            ['name' => 'Levi Ackerman', 'username' => 'levi'],
            ['name' => 'Satoru Gojo', 'username' => 'gojo'],
            ['name' => 'Yuji Itadori', 'username' => 'yuji'],
            ['name' => 'Megumi Fushiguro', 'username' => 'megumi'],
            ['name' => 'Nobara Kugisaki', 'username' => 'nobara'],
        ];

        foreach ($members as $m) {
            User::create([
                'name' => $m['name'],
                'username' => $m['username'],
                'email' => $m['username'] . '@spon.test',
                'password' => Hash::make('password'),
                'role' => 'Member',
            ]);
        }
    }
}
