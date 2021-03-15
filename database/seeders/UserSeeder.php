<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create user
        $user = [
            'email' => 'rshme@acsa.com',
            'username' => 'RsHme',
            'password' => bcrypt('rshme!!')
        ];
        $user = User::create($user);

        // create profile
        $profile = [
            'user_id' => $user->id,
            'name' => 'Rafi Septian Hadi',
            'bio' => "My name is Rafi Septian Hadi and I am a Junior Web Developer for Oswald Technologies. I am an accomplished coder and programmer, and I enjoy using my skills to contribute to the exciting technological advances that happen every day at Oswald Tech.",
        ];
        Profile::create($profile);

    }
}
