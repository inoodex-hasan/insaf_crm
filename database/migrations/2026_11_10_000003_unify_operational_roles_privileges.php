<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Operational Triad strictly: Marketing, Consultant, Application (Digital Marketing is separate!)
        $triadRoleIds = DB::table('roles')
            ->whereIn('slug', ['marketing', 'consultant', 'application'])
            ->pluck('id')
            ->toArray();

        // 2. Operational Privileges: *marketing, *consultant, *application, *invoice
        $triadPrivilegeIds = DB::table('privileges')
            ->whereIn('slug', ['*marketing', '*consultant', '*application', '*invoice'])
            ->pluck('id')
            ->toArray();

        // Attach operational privileges to the triad
        foreach ($triadRoleIds as $roleId) {
            foreach ($triadPrivilegeIds as $privilegeId) {
                DB::table('privilege_role')->updateOrInsert(
                    [
                        'role_id' => $roleId,
                        'privilege_id' => $privilegeId,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        // 3. Ensure Digital Marketing role (slug: digital-marketing) remains isolated with only Digital Marketing privilege
        $digitalMarketingRoleId = DB::table('roles')->where('slug', 'digital-marketing')->value('id');
        $digitalMarketingPrivilegeId = DB::table('privileges')->where('slug', '*digital_marketing')->value('id');

        if ($digitalMarketingRoleId && $digitalMarketingPrivilegeId) {
            // Remove any consultant/application/marketing/invoice privileges from digital-marketing
            DB::table('privilege_role')
                ->where('role_id', $digitalMarketingRoleId)
                ->whereIn('privilege_id', $triadPrivilegeIds)
                ->delete();

            // Ensure digital marketing has its own privilege
            DB::table('privilege_role')->updateOrInsert(
                [
                    'role_id' => $digitalMarketingRoleId,
                    'privilege_id' => $digitalMarketingPrivilegeId,
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Remove *digital_marketing privilege from the triad roles (keep digital marketing distinct)
            DB::table('privilege_role')
                ->whereIn('role_id', $triadRoleIds)
                ->where('privilege_id', $digitalMarketingPrivilegeId)
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructive reverse needed
    }
};
