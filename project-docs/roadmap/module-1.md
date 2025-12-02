## MODULE 1: USER MANAGEMENT & EMPLOYEE DIRECTORY

**Objective:** Create core user entity with employee data, departmental structure, and basic profile management.

**Dependencies:** Module 0

**Key Files to Create:**
```
Models:
- User.php (extend default)
- Department.php
- Role.php

Controllers:
- EmployeeController.php

Migrations:
- Create the departments table
- Modify users table (add employee fields)

Views/Components:
- Employee Directory page
- Employee profile card
- Department list
```

### 1.1 Database Schema & Seeding

**Tasks:**
- [ ] Create `departments` migration
  - Columns: id, name, description, head_id, created_at, updated_at
- [ ] Create `roles` migration & seed data (Admin, HR, MIS, Staff, Director)
- [ ] Create `permissions` migration & seed data
- [ ] Modify `users` migration
  - Add: department_id, floor, phone, profile_picture_url
- [ ] Create `role_permissions` pivot table
- [ ] Create role and permission seeders
- [ ] Test: Run migrations on fresh database

**Testing:**
```bash
php artisan migrate:fresh --seed
php artisan tinker
> App\Models\Role::all()
> App\Models\Department::all()
```

### 1.2 User Model & Relationships

**Tasks:**
- [ ] Define User model relationships
  ```php
  - belongsTo(Department)
  - belongsToMany(Role)
  - belongsToMany(Permission)
  - hasMany(AuditLog)
  ```
- [ ] Add user trait for role checking
  ```php
  hasRole($role)
  hasPermission($permission)
  canAccess($module)
  ```
- [ ] Create Department and Role models with relationships
- [ ] Add model factories for testing users (10 staff, 2 HR, 2 MIS, 1 Admin)

**Testing:**
```bash
php artisan tinker
> $user = User::with('roles', 'department')->first()
> $user->hasRole('staff')
> $user->hasPermission('view_memo')
```

### 1.3 Employee Directory API Endpoints

**Tasks:**
- [ ] Create EmployeeController
- [ ] Build API endpoints:
  - `GET /api/v1/employees` - List all with pagination
  - `GET /api/v1/employees/{id}` - Single employee
  - `GET /api/v1/departments` - List departments
  - `GET /api/v1/employees/search?q=name` - Search
- [ ] Add RBAC middleware to restrict data access
  - HR can see full details
  - Staff see basic info only
  - Admin sees all
- [ ] Add filtering (department, floor, role)

**Testing:**
```bash
# Use Postman or Laravel's built-in testing
GET /api/v1/employees
GET /api/v1/employees/1
GET /api/v1/departments
GET /api/v1/employees?department=1&floor=2
```

### 1.4 Employee Directory UI Components

**Tasks:**
- [ ] Create Employee Directory page (React/Vue component)
  - Table with: name, email, department, floor, phone
  - Search bar
  - Department filter dropdown
  - Employee count summary
- [ ] Create Employee Profile Modal
  - Name, email, department, floor, phone
  - Profile picture
  - View full details link
- [ ] Implement sorting (name, department, floor)
- [ ] Pagination (10, 25, 50 per page)

**Testing:**
- [ ] Manual test filtering by department
- [ ] Search for employee by name
- [ ] Click profile → modal appears
- [ ] Mobile responsive check

### 1.5 Module 1 Integration Testing

**Tasks:**
- [ ] Test full user flow:
  - Create new user via seeder
  - Retrieve via API
  - Display in directory
  - Search/filter works
  - Profile modal loads
- [ ] Test role-based data access
- [ ] Test API pagination
- [ ] Test error handling (404, unauthorized)

**Test Cases:**
```php
// tests/Feature/EmployeeDirectoryTest.php
test('staff can view employee directory')
test('staff cannot see sensitive employee data')
test('hr can see all employee data')
test('search filters employees correctly')
test('department filter works')
test('pagination works correctly')
```

### Module 1 Testing Checklist
```
✓ Users table seeded with roles/departments
✓ Employee Directory page loads
✓ Search functionality works
✓ Filter by department works
✓ Employee count displays correctly
✓ Pagination works (10, 25, 50)
✓ Profile modal shows correct data
✓ API endpoints tested with Postman
✓ Role-based access control works
✓ Mobile responsive on all screens
✓ No console errors
✓ Load time < 2 seconds
```

### Estimated Timeline: 5-7 days

---
