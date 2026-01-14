# Module 1: Users & Departments Implementation

This guide documents the implementation of the **User & Department Management** module. This is the foundation of the system, as all other modules (Meals, HR, MIS) rely on knowing *who* a user is and *where* they work.

## 1. Department Management
We started by creating the structure to manage organizational departments (e.g., HR, MIS, Administration).

### Step 1.1: Create the Model
We ran `php artisan make:model Department` to create the Eloquent model.
This model interacts with the `departments` table we created in the earlier migration.

**File:** `app/Models/Department.php`
```php
class Department extends Model
{
    protected $fillable = ['name', 'code'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
```

### Step 1.2: Create the Filament Resource
We used Filament's generator to create the CRUD (Create, Read, Update, Delete) interface automatically:
```bash
php artisan make:filament-resource Department
```

This created `app/Filament/Resources/DepartmentResource.php`. We then configured the **Form** and **Table** logic in that file:
- **Form**: Added text inputs for `name` and `code`.
- **Table**: Added columns to display `name`, `code`, and creation date.


## 3. User Management
With departments in place, we configured the `User` model to belong to a department.

### Step 3.1: Update User Model
We added the `department_id` and other profile fields to the `$fillable` array in `app/Models/User.php`.
We also defined the relationship:
```php
public function department()
{
    return $this->belongsTo(Department::class);
}
```

### Step 3.2: Create User Resource
We ran:
```bash
php artisan make:filament-resource User --generate
```
This inspected the `users` table and automatically guessed the fields.

### Step 3.3: Customize User Resource
The auto-generated form was a bit messy, so we organized it into Sections in `app/Filament/Resources/UserResource.php`:
- **Personal Info**: Names (First, Middle, Last).
- **Employment Details**: Staff ID, Job Title, and Department (Dropdown).
- **Security**: Email, Password (only required on create), and Active Status.

We also updated the table to show the **Department Name** instead of the ID using dot notation: `Tables\Columns\TextColumn::make('department.name')`.
