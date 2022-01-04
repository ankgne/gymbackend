<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                "name" => "owner",
                "display_name" => "Administrator",
                "description" => "This is the super admin role for owner",
            ],
            [
                "name" => "member",
                "display_name" => "Gym Member",
                "description" => "This role is for gym member",
            ],
            [
                "name" => "admin",
                "display_name" => "Gym Admin",
                "description" => "This role is for gym admin",
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate($role);
        }
    }
}
