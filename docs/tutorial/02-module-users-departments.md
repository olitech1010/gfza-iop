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

## 2. Next Steps
The next commit will update the **User** model to link it to these Departments, and create the **User Resource** so admins can manage staff.
