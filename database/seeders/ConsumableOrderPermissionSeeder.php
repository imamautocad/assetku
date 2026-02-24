<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsumableOrderPermissionSeeder extends Seeder
{
    public function run()
    {
        $permissionsToAdd = [
            'consumable-order.view',
            'consumable-order.create',
            'consumable-order.edit',
            'consumable-order.delete',
            'consumable-order.approve'
        ];

        // Ambil semua group permission
        $groups = DB::table('permission_groups')->get();

        foreach ($groups as $group) {
            // Decode existing permissions
            $existingPermissions = json_decode($group->permissions, true) ?? [];

            // Tambahkan permission baru (default = 1 semua diizinkan)
            foreach ($permissionsToAdd as $perm) {
                $existingPermissions[$perm] = 1;
            }

            // Update ke database
            DB::table('permission_groups')
                ->where('id', $group->id)
                ->update([
                    'permissions' => json_encode($existingPermissions),
                    'updated_at' => now()
                ]);
        }

        echo "✅ Permission Consumable Order berhasil ditambahkan ke semua permission_groups.\n";
    }
}
