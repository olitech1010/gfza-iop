# Module 6: RBAC & Security (Filament Shield)

## Overview
This module implements Role-Based Access Control (RBAC) using the `bezhansalleh/filament-shield` package. This allows us to manage user roles and permissions directly from the Filament admin panel, ensuring secure access to various modules like Leave Management, MIS Support, and HR Memos.

## 1. Installation

We installed Filament Shield and its dependency, `spatie/laravel-permission`:

```bash
composer require bezhansalleh/filament-shield
```

*Note: We encountered an issue with the PHP `zip` extension, which was resolved by ensuring the environment was correctly configured or using `--prefer-source` where applicable.*

## 2. Configuration

### User Model
We updated the `App\Models\User` model to include the `HasRoles` trait from Spatie, as required by Shield.

```php
// app/Models/User.php
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;

class User extends Authenticatable implements FilamentUser
{
    use HasRoles; 
    // ...
}
```

### Admin Panel Provider
We registered the `FilamentShieldPlugin` in `app/Providers/Filament/AdminPanelProvider.php`:

```php
public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugins([
            \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
        ]);
}
```

## 3. Policy Generation

We generated policies for all existing Filament resources (Departments, LeaveRequests, Memos, etc.) to enforce permissions.

```bash
php artisan shield:generate --all --panel=admin
```

This created Policy classes in `app/Policies` that correspond to the permissions defined by Shield.

## 4. Roles & Seeding

We created a `RolesSeeder` to define the standard roles for the GFZA IOP:

-   **super_admin**: Full access (assigned to User ID 1).
-   **hr_manager**: Manages Departments, Staff, Leave Requests, and Memos.
-   **dept_head**: Approves Leave Requests for their department.
-   **mis_support**: Manages Assets and Tickets.
-   **staff**: Basic access to request leave, view memos, and submit tickets.

Run the seeder:
```bash
php artisan db:seed --class=RolesSeeder
```

## 5. Usage

1.  Log in as the **Super Admin**.
2.  Navigate to **Shield > Roles**.
3.  Select a role (e.g., `hr_manager`).
4.  Toggle the permissions they should have (e.g., `view_any_leave::request`, `update_leave::request`).
5.  Users assigned to these roles will now be restricted based on these policies.
