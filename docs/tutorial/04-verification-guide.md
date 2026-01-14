# Verification Guide: Modules 1 & 2

This guide outlines how to verify the functionality of the **User & Department Management** and **Meal Selection** modules.

## 1. Prerequisites
Ensure you have seeded the database with the admin user:
```bash
php artisan db:seed --class=AdminUserSeeder
```

## 2. Admin Panel Access
1.  Navigate to `/admin`.
2.  Login with:
    *   **Email**: `admin@gfza.gov.gh`
    *   **Password**: `password`
3.  Verify you are redirected to the Dashboard.

## 3. Verify Module 1: Users & Departments
1.  **Departments**:
    *   Go to "Departments".
    *   Create a new Department (e.g., "Human Resources", Code: "HR").
    *   Verify it appears in the table.
2.  **Users**:
    *   Go to "Users".
    *   Create a new User.
    *   **Test Relations**: Ensure you can select the "Human Resources" department in the dropdown.
    *   **Test Auto-fill**: Enter First/Last name and check if "Display Name" auto-fills.

## 4. Verify Module 2: Meal Management
1.  **Meal Items**:
    *   Go to "Meal Items" (under Meal Management).
    *   Create a new item (e.g., "Jollof Rice & Chicken").
    *   Upload an image (optional).
    *   Ensure "Is Active" is toggled ON.
2.  **Daily Menu (Served Meals)**:
    *   Go to "Daily Menu".
    *   Create a record for **Today's Date**.
    *   Select "Jollof Rice" from the dropdown.
    *   Save.
3.  **Staff Orders**:
    *   Go to "Staff Orders".
    *   Create an order.
    *   **Select User**: Pick the user you created in step 3.
    *   **Select Meal**: Pick the meal you scheduled in step 4.
    *   Save.
    *   Verify the status badge (e.g., "Ordered" in yellow).

## 5. Troubleshooting
*   **Login Failed**: Ensure `is_active` is true in the database for your user.
*   **Images not showing**: Ensure `php artisan storage:link` has been run.
