<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Module;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $modules = [
            'Generic',
            'Wildlife',
            'Population Estimate',
            'Quota Setting',
            'Hunting Concession',
            'Hunting Client',
            'Hunting Activity',
            'Human Wildlife Conflict',
            'Problematic Animal Control',
            'Poaching',
            'Child Organisation',
            'Organisation User',
            'Revenue Sharing',
            'Finance',
        ];

        foreach ($modules as $module) {
            Module::create(['name' => ucfirst($module)]);

            //create permissions for the module
            $module = Module::where('name', $module)->first();
            $permissions = ['view', 'create', 'read', 'update', 'delete'];
            foreach ($permissions as $permission) {
                Permission::create(['name' => $permission . '-' . $module->slug]);
            }
        }
    }
}
