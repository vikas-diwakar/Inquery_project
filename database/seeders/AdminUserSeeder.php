<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds or resets the Super Admin credentials for instant login.
     */
    public function run(): void
    {
        // 1. Ensure the default primary company exists
        $company = Company::default();

        // 2. Ensure Admin role exists with unrestricted permissions
        $adminRole = Role::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Admin'],
            ['permissions' => ['*']]
        );

        // 3. Define Admin Credentials (configurable via env if specified)
        $adminEmail = env('ADMIN_EMAIL', 'admin@admin.com');
        $adminPassword = env('ADMIN_PASSWORD', 'password123');
        $adminName = env('ADMIN_NAME', 'System Administrator');

        // 4. Create or update primary admin user
        $adminUser = User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'password' => Hash::make($adminPassword),
                'company_id' => $company->id,
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
            ]
        );

        // Also ensure fallback admin@example.com is active and has known credentials
        if ($adminEmail !== 'admin@example.com') {
            User::updateOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name' => 'Super Admin',
                    'password' => Hash::make('password123'),
                    'company_id' => $company->id,
                    'role_id' => $adminRole->id,
                    'email_verified_at' => now(),
                ]
            );
        }

        // 5. Output clear banner for the administrator
        if ($this->command) {
            $this->command->newLine();
            $this->command->info('===========================================================');
            $this->command->info('           ADMIN LOGIN CREDENTIALS CREATED                 ');
            $this->command->info('===========================================================');
            $this->command->line("  Portal Login URL: " . route('login'));
            $this->command->line("  Admin Email:      <comment>{$adminEmail}</comment>");
            $this->command->line("  Admin Password:   <comment>{$adminPassword}</comment>");
            $this->command->line("  Role:             Admin (Full Unrestricted Access)");
            $this->command->line("  Company:          {$company->name} (ID: {$company->id})");
            $this->command->info('===========================================================');
            $this->command->line('  From the Admin dashboard you can register users, add');
            $this->command->line('  projects, configure WhatsApp API and manage inquiries.');
            $this->command->info('===========================================================');
            $this->command->newLine();
        }
    }
}