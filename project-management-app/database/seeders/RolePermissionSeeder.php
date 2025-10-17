<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Role and Permission Seeder
 *
 * Creates the RBAC structure for the Project Management application:
 * - Super Admin: Full system access
 * - Project Manager: Manage projects, assign tasks, manage teams
 * - Team Lead: Manage assigned teams and projects
 * - Team Member: Access assigned tasks and projects
 * - Client/Viewer: Read-only access to specific projects
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Project permissions
            'view projects',
            'create projects',
            'edit projects',
            'delete projects',
            'manage project settings',

            // Task permissions
            'view tasks',
            'create tasks',
            'edit tasks',
            'delete tasks',
            'assign tasks',
            'update task status',

            // Team permissions
            'view teams',
            'create teams',
            'edit teams',
            'delete teams',
            'manage team members',

            // User permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage user roles',

            // Milestone permissions
            'view milestones',
            'create milestones',
            'edit milestones',
            'delete milestones',

            // Time entry permissions
            'view time entries',
            'create time entries',
            'edit time entries',
            'delete time entries',
            'view all time entries',

            // System permissions
            'view system settings',
            'edit system settings',
            'view logs',
            'manage integrations',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Super Admin - Full access
        $superAdmin = Role::create(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Project Manager - Manage projects, tasks, teams
        $projectManager = Role::create(['name' => 'Project Manager']);
        $projectManager->givePermissionTo([
            'view projects', 'create projects', 'edit projects', 'delete projects', 'manage project settings',
            'view tasks', 'create tasks', 'edit tasks', 'delete tasks', 'assign tasks', 'update task status',
            'view teams', 'create teams', 'edit teams', 'manage team members',
            'view users',
            'view milestones', 'create milestones', 'edit milestones', 'delete milestones',
            'view time entries', 'view all time entries',
        ]);

        // Team Lead - Manage assigned teams and projects
        $teamLead = Role::create(['name' => 'Team Lead']);
        $teamLead->givePermissionTo([
            'view projects', 'edit projects',
            'view tasks', 'create tasks', 'edit tasks', 'assign tasks', 'update task status',
            'view teams', 'edit teams', 'manage team members',
            'view users',
            'view milestones', 'create milestones', 'edit milestones',
            'view time entries', 'create time entries', 'edit time entries',
        ]);

        // Team Member - Access assigned tasks and projects
        $teamMember = Role::create(['name' => 'Team Member']);
        $teamMember->givePermissionTo([
            'view projects',
            'view tasks', 'edit tasks', 'update task status',
            'view teams',
            'view milestones',
            'view time entries', 'create time entries', 'edit time entries',
        ]);

        // Client/Viewer - Read-only access
        $client = Role::create(['name' => 'Client']);
        $client->givePermissionTo([
            'view projects',
            'view tasks',
            'view teams',
            'view milestones',
        ]);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->table(
            ['Role', 'Permissions Count'],
            [
                ['Super Admin', $superAdmin->permissions->count()],
                ['Project Manager', $projectManager->permissions->count()],
                ['Team Lead', $teamLead->permissions->count()],
                ['Team Member', $teamMember->permissions->count()],
                ['Client', $client->permissions->count()],
            ]
        );
    }
}
