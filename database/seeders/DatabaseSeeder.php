<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database for standalone installation.
     */
    public function run(): void
    {
        // 1. Create or get default primary company
        $company = Company::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Apex Real Estate & Developments',
                'email' => 'contact@apexrealestate.test',
                'phone' => '+1 (555) 019-2834',
                'address' => '742 Evergreen Terrace, Suite 100, Beverly Hills, CA 90210',
                'is_active' => true,
                'lead_allocation_method' => 'manual',
            ]
        );

        // 2. Create standard system roles
        $adminRole = Role::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Admin'],
            ['permissions' => ['*']]
        );

        $managerRole = Role::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Manager'],
            [
                'permissions' => [
                    'projects.view',
                    'projects.create',
                    'projects.edit',
                    'inquiries.view',
                    'inquiries.edit',
                ]
            ]
        );

        $salesRole = Role::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Sales Executive'],
            [
                'permissions' => [
                    'inquiries.view',
                    'inquiries.edit',
                ]
            ]
        );

        // 3. Create default Super Admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'company_id' => $company->id,
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
            ]
        );

        // Ensure existing admin user has correct company and role
        if ($adminUser->company_id !== $company->id || $adminUser->role_id !== $adminRole->id) {
            $adminUser->update([
                'company_id' => $company->id,
                'role_id' => $adminRole->id,
                'email_verified_at' => $adminUser->email_verified_at ?? now(),
            ]);
        }

        // Call dedicated Admin User Seeder
        $this->call(AdminUserSeeder::class);

        // 4. Create sample Sales Executive user
        User::firstOrCreate(
            ['email' => 'agent@example.com'],
            [
                'name' => 'Sarah Jenkins',
                'password' => Hash::make('password'),
                'company_id' => $company->id,
                'role_id' => $salesRole->id,
                'email_verified_at' => now(),
            ]
        );
    }
}
