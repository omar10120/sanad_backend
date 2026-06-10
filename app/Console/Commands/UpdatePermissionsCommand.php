<?php

namespace App\Console\Commands;

use App\Enums\PermissionEnum;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\Console\Command\Command as CommandAlias;

class UpdatePermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:update
                           {--force : Force update without confirmation}
                           {--remove-orphaned : Remove permissions not in enum}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update permissions in database based on PermissionEnum';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting permissions update...');

        // Get all permissions from enum
        $enumPermissions = PermissionEnum::getAllPermissions();

        // Get existing permissions from database
        $existingPermissions = Permission::pluck('name')->toArray();

        // Find new permissions to add
        $newPermissions = array_diff($enumPermissions, $existingPermissions);

        // Find orphaned permissions to potentially remove
        $orphanedPermissions = array_diff($existingPermissions, $enumPermissions);

        $this->displayStatus($enumPermissions, $existingPermissions, $newPermissions, $orphanedPermissions);

        // Ask for confirmation unless --force is used
        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to proceed with the update?')) {
                $this->info('Operation cancelled.');
                return CommandAlias::SUCCESS;
            }
        }

        // Add new permissions
        $this->addNewPermissions($newPermissions);

        // Handle orphaned permissions if requested
        if ($this->option('remove-orphaned') && !empty($orphanedPermissions)) {
            $this->removeOrphanedPermissions($orphanedPermissions);
        } elseif (!empty($orphanedPermissions)) {
            $this->warn('Found orphaned permissions but --remove-orphaned flag not set. Use --remove-orphaned to remove them.');
        }

        // Always assign all permissions to role with ID = 1
        $this->assignAllPermissionsToSuperRole();

        $this->info('Permissions update completed successfully!');
        return CommandAlias::SUCCESS;
    }

    /**
     * Display current status of permissions
     */
    private function displayStatus(array $enumPermissions, array $existingPermissions, array $newPermissions, array $orphanedPermissions): void
    {
        $this->info('=== Permissions Status ===');
        $this->line("Total permissions in enum: " . count($enumPermissions));
        $this->line("Total permissions in database: " . count($existingPermissions));

        if (!empty($newPermissions)) {
            $this->warn("New permissions to add (" . count($newPermissions) . "):");
            foreach ($newPermissions as $permission) {
                $this->line("  + $permission");
            }
        } else {
            $this->info("No new permissions to add.");
        }

        if (!empty($orphanedPermissions)) {
            $this->error("Orphaned permissions in database (" . count($orphanedPermissions) . "):");
            foreach ($orphanedPermissions as $permission) {
                $this->line("  - $permission");
            }
        } else {
            $this->info("No orphaned permissions found.");
        }
    }

    /**
     * Add new permissions to database
     */
    private function addNewPermissions(array $newPermissions): void
    {
        if (empty($newPermissions)) {
            return;
        }

        $this->info('Adding new permissions...');

        $progressBar = $this->output->createProgressBar(count($newPermissions));
        $progressBar->start();

        foreach ($newPermissions as $permissionName) {
            try {
                Permission::create([
                    'name' => $permissionName,
                    'guard_name' => 'web'
                ]);
                $progressBar->advance();
            } catch (\Exception $e) {
                $progressBar->advance();
                $this->error("\nFailed to create permission '$permissionName': " . $e->getMessage());
            }
        }

        $progressBar->finish();
        $this->newLine();
        $this->info('Added ' . count($newPermissions) . ' new permissions.');
    }

    /**
     * Remove orphaned permissions from database
     */
    private function removeOrphanedPermissions(array $orphanedPermissions): void
    {
        if (empty($orphanedPermissions)) {
            return;
        }

        $this->warn('Removing orphaned permissions...');

        // Double check with user since this is destructive
        if (!$this->option('force')) {
            if (!$this->confirm('This will permanently delete ' . count($orphanedPermissions) . ' permissions. Are you sure?')) {
                $this->info('Skipped removing orphaned permissions.');
                return;
            }
        }

        $progressBar = $this->output->createProgressBar(count($orphanedPermissions));
        $progressBar->start();

        $removedCount = 0;
        foreach ($orphanedPermissions as $permissionName) {
            try {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission) {
                    // Remove permission from all roles first
                    $permission->roles()->detach();
                    // Remove permission from all users
                    $permission->users()->detach();
                    // Delete the permission
                    $permission->delete();
                    $removedCount++;
                }
                $progressBar->advance();
            } catch (\Exception $e) {
                $progressBar->advance();
                $this->error("\nFailed to remove permission '$permissionName': " . $e->getMessage());
            }
        }

        $progressBar->finish();
        $this->newLine();
        $this->info("Removed $removedCount orphaned permissions.");
    }

    /**
     * Assign all permissions to the super admin role (role_id = 1)
     */
    private function assignAllPermissionsToSuperRole(): void
    {
        $this->info('Assigning all permissions to super admin role (ID: 1)...');

        try {
            // Find the role with ID = 1
            $superRole = Role::find(1);

            if (!$superRole) {
                $this->warn('Role with ID 1 not found. Skipping permission assignment.');
                return;
            }

            // Get all current permissions
            $allPermissions = Permission::all();

            if ($allPermissions->isEmpty()) {
                $this->warn('No permissions found in database.');
                return;
            }

            // Sync all permissions to the super role
            $superRole->syncPermissions($allPermissions);

            $this->info("Successfully assigned {$allPermissions->count()} permissions to role '{$superRole->name}' (ID: 1).");

        } catch (\Exception $e) {
            $this->error('Failed to assign permissions to super admin role: ' . $e->getMessage());
        }
    }
}
