<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ($this->adminLists() as $admin) {
            $c = \App\Models\Admin::where('username', '=', $admin['username'])
                ->where('type', '=', $admin['type'])->first();

            if (! $c) {
                $c = new \App\Models\Admin;
                foreach ($admin as $f => $v) {
                    $c->$f = $v;
                }
                $c->save();
            }
        }
    }

    protected function adminLists(): array
    {
        return [
            ['username' => 'superadmin', 'name' => 'Super Admin', 'password' => 'aabb1122', 'type' => 1],
            ['username' => 'developer', 'name' => 'Developer', 'password' => '123qweasd123', 'type' => 2],
        ];
    }
}
