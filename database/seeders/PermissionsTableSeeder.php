<?php
namespace Database\Seeders;


use Illuminate\Database\Seeder;
use \App\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the seeder.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'ANNOUNCEMENT.EDIT',
            'ANNOUNCEMENT.DELETE',
            'USER.EDIT',
            'USER.REMOVE',
            // Dodaj więcej uprawnień, jeśli chcesz
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}