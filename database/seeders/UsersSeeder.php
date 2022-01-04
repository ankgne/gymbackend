<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $owner = Role::where("name", "owner")
            ->get()
            ->sole();
        $member = Role::where("name", "member")
            ->get()
            ->sole();
        $admin = Role::where("name", "admin")
            ->get()
            ->sole();

        User::create([
            "email" => "owner@gmail.com",
            "name" => "owner",
            "password" => Hash::make("secret@123"),
        ])->attachRole($owner);

        User::create([
            "email" => "member@gmail.com",
            "name" => "member",
            "password" => Hash::make("secret@123"),
        ])->attachRole($member);

        User::create([
            "email" => "ankgne@gmail.com",
            "name" => "admin",
            "password" => Hash::make("secret123"),
        ])->attachRole($admin);
    }
}
