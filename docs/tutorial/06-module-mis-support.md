# Module 4: MIS Support

This module helps the IT department track assets and manage support tickets.

## 1. MIS Assets (Inventory)
We created a system to track hardware/software assets.

### Step 1.1: Create Model
`php artisan make:model MisAsset`

We defined fields like `asset_tag` (barcode), `serial_number`, and `assigned_to_user_id`.

### Step 1.2: Create Resource
`php artisan make:filament-resource MisAsset --generate`

We customized `MisAssetResource.php`:
- **Navigation**: Grouped under "MIS Support".
- **Form**:
    *   **Asset Identity**: Name, Tag, Serial, Type (Laptop, Desktop, etc.).
    *   **Status**: Active, In Repair, Retired, Lost.
    *   **Assignment**: Link asset to a Staff Member.
- **Table**: Shows Tag, Model, Status (colored badges), and Assigned User.

## 2. MIS Tickets (Helpdesk)
We created a simple ticketing system for staff to report issues.

### Step 2.1: Create Model
`php artisan make:model MisTicket`

We defined relations for `requester` (User) and `assignedStaff` (User).

### Step 2.2: Create Resource
`php artisan make:filament-resource MisTicket --generate`

We customized `MisTicketResource.php`:
- **Navigation**: Grouped under "MIS Support".
- **Form**:
    *   **Details**: Subject, Description, Priority (Critical, High, Medium, Low).
    *   **Workflow**: Status (Open, In Progress, Resolved), Assigned Agent.
- **Table**:
    *   Shows Subject with a short description preview.
    *   Color-coded **Status** and **Priority** badges.
    *   Shows Requester and Agent names.

## 3. Conclusion
We now have a core Operations Portal covering:
*   **HR**: User Directory, Department structure, Memos.
*   **Operations**: Meal Ordering.
*   **IT**: Asset Tracking & Helpdesk.

The next steps would typically be:
1.  **Role Based Access Control (RBAC)**: To ensure normal staff can't delete users or see IT assets.
2.  **Frontend Dashboard**: For non-admin users to place orders and submit tickets easily.
