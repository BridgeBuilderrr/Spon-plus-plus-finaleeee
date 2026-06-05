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
        User::updateOrCreate(
            ['username' => 'arielkinosaki'],
            [
                'name' => 'Ariel Baru',
                'email' => 'ariel@spon.test',
                'password' => Hash::make('password'),
                'role' => 'Teacher',
                'bio' => 'Lead Educator at Spon++. Specialist in Modern Web Tech.',
            ]
        );

        User::updateOrCreate(
            ['username' => 'sponmaster'],
            [
                'name' => 'Spon++ Master',
                'email' => 'admin@spon.test',
                'password' => Hash::make('password'),
                'role' => 'Teacher',
            ]
        );

        // More Teachers
        $teachers = [
            ['name' => 'Hideo Kojima', 'username' => 'hideo'],
            ['name' => 'Masahiro Sakurai', 'username' => 'sakurai'],
            ['name' => 'Linus Torvalds', 'username' => 'linus'],
        ];

        foreach ($teachers as $t) {
            User::updateOrCreate(
                ['username' => $t['username']],
                [
                    'name' => $t['name'],
                    'email' => $t['username'] . '@spon.test',
                    'password' => Hash::make('password'),
                    'role' => 'Teacher',
                ]
            );
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
            User::updateOrCreate(
                ['username' => $m['username']],
                [
                    'name' => $m['name'],
                    'email' => $m['username'] . '@spon.test',
                    'password' => Hash::make('password'),
                    'role' => 'Member',
                ]
            );
        }
    }
}
