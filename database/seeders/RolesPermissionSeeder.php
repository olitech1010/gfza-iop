<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Assigns permissions to roles based on the SRS documentation.
     * Permission names follow Filament Shield format: {action}_{resource::name}
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permission sets for each resource (using Filament Shield naming convention)
        $resources = [
            'user',
            'department',
            'memo',
            'memo::recipient',
            'leave::request',
            'weekly::menu',
            'meal::item',
            'meal::request',
            'meal::order',
            'served::meal',
            'caterer',
            'mis::ticket',
            'mis::asset',
            'conference::room',
            'room::booking',
        ];

        $actions = ['view', 'view_any', 'create', 'update', 'delete', 'delete_any', 'restore', 'restore_any', 'replicate', 'reorder', 'force_delete', 'force_delete_any'];

        // Create all permissions if they don't exist
        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$action}_{$resource}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // Create page permissions
        $pagePermissions = [
            'page_DailyMenu',
            'page_MealSummary',
            'page_StaffMealSelection',
        ];

        foreach ($pagePermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Assign permissions to roles
        $this->assignHrManagerPermissions();
        $this->assignMisSupportPermissions();
        $this->assignDeptHeadPermissions();
        $this->assignStaffPermissions();
    }

    /**
     * HR Manager: Memos, Meals, Leave Requests, Users (add/edit)
     */
    protected function assignHrManagerPermissions(): void
    {
        $role = Role::firstOrCreate(['name' => 'hr_manager', 'guard_name' => 'web']);

        $permissions = [
            // Memos - Full access
            'view_memo', 'view_any_memo', 'create_memo', 'update_memo', 'delete_memo', 'delete_any_memo',
            // MemoRecipients - Full access
            'view_memo::recipient', 'view_any_memo::recipient', 'create_memo::recipient', 'update_memo::recipient', 'delete_memo::recipient', 'delete_any_memo::recipient',
            // LeaveRequests - Full access
            'view_leave::request', 'view_any_leave::request', 'create_leave::request', 'update_leave::request', 'delete_leave::request', 'delete_any_leave::request',
            // WeeklyMenu - Full access
            'view_weekly::menu', 'view_any_weekly::menu', 'create_weekly::menu', 'update_weekly::menu', 'delete_weekly::menu', 'delete_any_weekly::menu',
            // MealItems - Full access
            'view_meal::item', 'view_any_meal::item', 'create_meal::item', 'update_meal::item', 'delete_meal::item', 'delete_any_meal::item',
            // MealRequests - View and update only
            'view_meal::request', 'view_any_meal::request', 'update_meal::request',
            // MealOrders - Full access
            'view_meal::order', 'view_any_meal::order', 'create_meal::order', 'update_meal::order', 'delete_meal::order', 'delete_any_meal::order',
            // ServedMeals - Full access
            'view_served::meal', 'view_any_served::meal', 'create_served::meal', 'update_served::meal', 'delete_served::meal', 'delete_any_served::meal',
            // Caterers - Full access
            'view_caterer', 'view_any_caterer', 'create_caterer', 'update_caterer', 'delete_caterer', 'delete_any_caterer',
            // Users - View, create, update (no delete)
            'view_user', 'view_any_user', 'create_user', 'update_user',
            // Departments - View only
            'view_department', 'view_any_department',
            // ConferenceRooms - View only
            'view_conference::room', 'view_any_conference::room',
            // RoomBookings - Full access (override/cancel)
            'view_room::booking', 'view_any_room::booking', 'create_room::booking', 'update_room::booking', 'delete_room::booking', 'delete_any_room::booking',
            // Pages
            'page_DailyMenu',
            'page_MealSummary',
            'page_StaffMealSelection',
        ];

        $role->syncPermissions($permissions);
    }

    /**
     * MIS Support: IT Tickets, Assets, Users, Conference Rooms
     */
    protected function assignMisSupportPermissions(): void
    {
        $role = Role::firstOrCreate(['name' => 'mis_support', 'guard_name' => 'web']);

        $permissions = [
            // MisTickets - View and update (assign, change status)
            'view_mis::ticket', 'view_any_mis::ticket', 'update_mis::ticket',
            // MisAssets - Full access
            'view_mis::asset', 'view_any_mis::asset', 'create_mis::asset', 'update_mis::asset', 'delete_mis::asset', 'delete_any_mis::asset',
            // Users - View, create, update (no delete)
            'view_user', 'view_any_user', 'create_user', 'update_user',
            // Departments - View, create, update (no delete)
            'view_department', 'view_any_department', 'create_department', 'update_department',
            // Memos - Read only
            'view_memo', 'view_any_memo',
            // ConferenceRooms - Full access
            'view_conference::room', 'view_any_conference::room', 'create_conference::room', 'update_conference::room', 'delete_conference::room', 'delete_any_conference::room',
            // RoomBookings - View, create, update (no delete others')
            'view_room::booking', 'view_any_room::booking', 'create_room::booking', 'update_room::booking',
            // Pages
            'page_StaffMealSelection',
        ];

        $role->syncPermissions($permissions);
    }

    /**
     * Department Head: Department oversight, Leaves, Tickets (submit)
     */
    protected function assignDeptHeadPermissions(): void
    {
        $role = Role::firstOrCreate(['name' => 'dept_head', 'guard_name' => 'web']);

        $permissions = [
            // LeaveRequests - View, create, update (approve/reject for dept)
            'view_leave::request', 'view_any_leave::request', 'create_leave::request', 'update_leave::request',
            // Memos - Read only
            'view_memo', 'view_any_memo',
            // MisTickets - View and create (submit for dept)
            'view_mis::ticket', 'view_any_mis::ticket', 'create_mis::ticket',
            // RoomBookings - View, create, update
            'view_room::booking', 'view_any_room::booking', 'create_room::booking', 'update_room::booking',
            // ConferenceRooms - View only
            'view_conference::room', 'view_any_conference::room',
            // Users - View directory
            'view_user', 'view_any_user',
            // Pages
            'page_StaffMealSelection',
        ];

        $role->syncPermissions($permissions);
    }

    /**
     * Staff: Own records only (Meals, Tickets, Bookings, Leaves)
     */
    protected function assignStaffPermissions(): void
    {
        $role = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);

        $permissions = [
            // Memos - Read only (filtered by policy to their inbox)
            'view_memo', 'view_any_memo',
            // MisTickets - View and create own
            'view_mis::ticket', 'view_any_mis::ticket', 'create_mis::ticket', 'update_mis::ticket',
            // RoomBookings - View, create, update, delete own
            'view_room::booking', 'view_any_room::booking', 'create_room::booking', 'update_room::booking', 'delete_room::booking',
            // ConferenceRooms - View availability
            'view_conference::room', 'view_any_conference::room',
            // Users - View directory
            'view_user', 'view_any_user',
            // LeaveRequests - View, create, update own
            'view_leave::request', 'view_any_leave::request', 'create_leave::request', 'update_leave::request',
            // Pages
            'page_StaffMealSelection',
        ];

        $role->syncPermissions($permissions);
    }
}
