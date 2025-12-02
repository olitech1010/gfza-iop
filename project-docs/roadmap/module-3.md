# Module 3: Admin Panel & Dashboards

**Duration:** 8-10 days  
**Dependencies:** Module 0, Module 1, Module 2  
**Objective:** Build comprehensive admin panel with CRUD operations for all system entities and audit logging.

---




```
Controllers:
- AdminController.php
- UserManagementController.php
- RolePermissionController.php
- AuditLogController.php
- SystemSettingsController.php

Migrations:
- Create audit_logs table

Views/Pages:
- Admin Dashboard
- User Management
- Role & Permission Management
- System Logs
- Settings
```


---

### 3.1 Admin Dashboard Overview


**Tasks:**
- [ ] Create AdminController::dashboard()
- [ ] Build dashboard page with:
  - System health KPIs:
    - Total users
    - Total departments
    - System uptime
    - Active sessions
  - Recent activities (last 10)
  - System logs summary
  - Module status indicators
- [ ] Add real-time data refresh (every 30s)
- [ ] Create admin-only middleware

**Dashboard Widgets:**
```
Top Row (4 KPI Cards):
- Total Users | 250 | +5 this month
- Departments | 12 | No change
- Active Sessions | 42 | Real-time
- System Uptime | 99.8% | Last 30 days

Bottom Row:
- Recent Activities (table)
- System Health (chart)
- Module Status (list with indicators)
```


---

### 3.2 User Management CRUD


**Tasks:**
- [ ] Create UserManagementController with methods:
  ```php
  index() - List all users (paginated, filtered)
  store() - Create new user
  show() - View single user
  update() - Edit user
  destroy() - Delete user
  ```
- [ ] Build API endpoints:
  ```
  GET /api/v1/admin/users
  POST /api/v1/admin/users
  GET /api/v1/admin/users/{id}
  PUT /api/v1/admin/users/{id}
  DELETE /api/v1/admin/users/{id}
  ```
- [ ] Add validation rules:
  - Email unique, valid format
  - Name required, min 3 chars
  - Department required
  - Role required
  - Password min 8 chars (create only)

**Testing:**
```bash
POST /api/v1/admin/users
{
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@gfza.com",
  "department_id": 1,
  "floor": 2,
  "phone": "+233123456789",
  "role_id": 3
}

# Response: 201 Created
{
  "id": 251,
  "email": "john@gfza.com",
  "created_at": "2025-01-15T..."
}
```


---

### 3.3 User Management UI


**Tasks:**
- [ ] Create User Management page
  - Users table with columns:
    - Name | Email | Department | Floor | Phone | Role | Status | Actions
  - Search bar (by name, email, department)
  - Filters:
    - Department dropdown
    - Role dropdown
    - Status (Active/Inactive)
  - Create new user button
  - Pagination (10, 25, 50 users per page)
  - Sort by columns
- [ ] Create Add/Edit User modal
  - Form fields:
    - First name (required)
    - Last name (required)
    - Email (required, unique)
    - Department (required, dropdown)
    - Floor (optional, number)
    - Phone (optional)
    - Role (required, dropdown)
    - Password (required on create, optional on edit)
  - Submit button
  - Cancel button
  - Show loading state
  - Validate on submit
- [ ] Create Delete User confirmation dialog
  - Warning message
  - Delete button (red)
  - Cancel button

**Testing:**
- [ ] Create new user → appears in table
- [ ] Edit user → changes reflect immediately
- [ ] Delete user → confirmation dialog → user removed
- [ ] Search filters users correctly
- [ ] Pagination works
- [ ] All form validation works
- [ ] Mobile responsive


---

### 3.4 Role & Permission Management


**Tasks:**
- [ ] Create RolePermissionController
  - Manage roles (CRUD)
  - Manage permissions
  - Assign permissions to roles
- [ ] Build API endpoints:
  ```
  GET /api/v1/admin/roles
  POST /api/v1/admin/roles
  PUT /api/v1/admin/roles/{id}
  DELETE /api/v1/admin/roles/{id}
  GET /api/v1/admin/permissions
  POST /api/v1/admin/roles/{id}/permissions
  DELETE /api/v1/admin/roles/{id}/permissions/{permissionId}
  ```
- [ ] Create Role Management page
  - Roles table (Name, Permissions count, Actions)
  - Create role button
  - Edit role modal
  - Permission assignment interface
- [ ] Create permission assignment UI
  - Checkboxes for each permission
  - Organize by module (HR, MIS, Admin, Staff)
  - Save button

**Example Permissions Structure:**
```
HR Module:
- create_memo
- edit_memo
- delete_memo
- manage_meal_plans
- manage_employees

MIS Module:
- manage_tickets
- assign_assets
- view_assets
- manage_emails

Admin:
- manage_users
- manage_roles
- view_logs
- manage_system_settings

Staff:
- view_memos
- select_meals
- submit_tickets
- book_rooms
```


---

### 3.5 Audit Logging System


**Tasks:**
- [ ] Create AuditLog model and migration
  - Columns: user_id, action, module, entity_type, entity_id, old_values, new_values, ip_address, timestamp
- [ ] Create AuditLogMiddleware to capture all admin actions
- [ ] Implement logging for:
  - User creation/edit/delete
  - Role/permission changes
  - User login/logout
  - Data modifications
- [ ] Create System Logs page
  - Logs table:
    - Timestamp | User | Action | Module | Entity | Details
  - Filter by date range
  - Filter by user
  - Filter by action
  - Export to CSV
- [ ] Add audit helper function to log actions
  ```php
  logAudit('delete_user', 'user_management', 'User', 251, ['name' => 'John'], null);
  ```

**Testing:**
```bash
- Admin creates user → logs recorded
- Admin edits user email → old and new values logged
- Admin deletes user → logged with user data
- View logs page → shows all actions
- Filter logs by date → works correctly
```


---

### 3.6 System Settings


**Tasks:**
- [ ] Create Settings page
  - System name
  - Maintenance mode toggle
  - Email configuration
  - Session timeout (minutes)
  - Password policy
  - Backup schedule
- [ ] Store settings in database (settings table)
- [ ] Create SettingsController
- [ ] Add environment-based defaults
- [ ] Test settings apply system-wide


---

### 3.7 Audit Trail Export


**Tasks:**
- [ ] Add export button on Audit Logs page
- [ ] Export to CSV functionality
  - Columns: Timestamp, User, Action, Module, Entity, Old Values, New Values
  - Include date range in filename
- [ ] Test CSV file download

**Testing:**
```bash
- Click export → CSV file downloads
- Open CSV in Excel → data formatted correctly
- All columns present
- Date range correct
```


---

### 3.8 Module 3 Integration Testing


**Test Cases:**
```php
// tests/Feature/AdminTest.php
test('only admin can access admin dashboard')
test('admin dashboard shows correct KPIs')
test('admin can create user')
test('admin can edit user')
test('admin can delete user')
test('admin can create role')
test('admin can assign permissions to role')
test('audit logs record admin actions')
test('audit logs can be filtered')
test('audit logs can be exported to CSV')
test('unauthorized user cannot access admin panel')
```

**Manual Testing:**
- [ ] Login as admin → admin dashboard loads
- [ ] Create new user → user appears in table
- [ ] Edit user → changes save
- [ ] Delete user → confirmation → user removed
- [ ] Create new role → role appears in list
- [ ] Assign permissions to role → permissions saved
- [ ] View audit logs → all actions logged
- [ ] Filter logs by date → works
- [ ] Export logs to CSV → file downloads and opens correctly
- [ ] All pages responsive


---

### Module 3 Testing Checklist

```
✓ Admin dashboard loads
✓ All KPIs display correctly
✓ User CRUD operations work
✓ User search/filter works
✓ Role management works
✓ Permission assignment works
✓ Audit logs record actions
✓ Audit logs filtering works
✓ Export to CSV works
✓ Settings page loads
✓ Settings changes persist
✓ Unauthorized users blocked
✓ Mobile responsive
✓ No console errors
```


---

### Estimated Timeline: 8-10 days


---



---

## Estimated Timeline

**Duration:** 8-10 days

---

## Navigation

← [Module 2](./module-2.md) | [Module 4](./module-4.md) →
