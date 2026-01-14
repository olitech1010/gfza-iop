# Module 2: Meal Selection (Part 1 - Meal Items)

This module handles the core functionality of the Meal Selection system. We are breaking this down into three parts:
1.  **Meal Items**: The master list of all available food options.
2.  **Daily Menu (Served Meals)**: Which items are available on a specific day.
3.  **Meal Orders**: The staff selections.

## 1. Meal Items
We created a model and resource to manage the "Master List" of food.

### Step 1.1: Create Model
`php artisan make:model MealItem`

We defined the schema in the migration earlier (`name`, `description`, `image_path`, `is_active`).

**File:** `app/Models/MealItem.php`
```php
protected $fillable = [
    'name',
    'description',
    'image_path',
    'is_active',
];

protected $casts = [
    'is_active' => 'boolean',
];
```

### Step 1.2: Create Resource
`php artisan make:filament-resource MealItem --generate`

We customized the `MealItemResource.php` to include:
- **Navigation Group**: "Meal Management" to keep the sidebar organized.
- **Image Upload**: Configured `FileUpload` to store images in the `meals` directory.
- **Toggle**: For the `is_active` status.


## 2. Served Meals (Daily Menu)
This resource allows the kitchen to schedule *what* is being served on *which* days.

### Step 2.1: Create Model
`php artisan make:model ServedMeal`

We updated `app/Models/ServedMeal.php` to link back to the `MealItem`:
```php
public function mealItem()
{
    return $this->belongsTo(MealItem::class);
}
```

### Step 2.2: Create Resource
`php artisan make:filament-resource ServedMeal --generate`

We customized `ServedMealResource.php`:
- **Navigation**: Grouped under "Meal Management" and labeled "Daily Menu".
- **Form**:
    *   **Date Picker**: Formatted nicely.
    *   **Meal Select**: A dropdown searching `mealItem` by name.
    *   **Max Orders**: Optional limit for stock availability.
- **Table**: Shows Date (formatted D, d M Y) and Meal Name.


## 3. Meal Orders
This resource allows admins to view and manage orders placed by staff.

### Step 3.1: Create Model
`php artisan make:model MealOrder`

We updated `app/Models/MealOrder.php` with relations to `User` and `ServedMeal`.

### Step 3.2: Create Resource
`php artisan make:filament-resource MealOrder --generate`

We customized `MealOrderResource.php` to provide a clear overview:
- **Navigation**: "Meal Management" -> "Staff Orders".
- **Form**:
    *   **User Select**: Pick a staff member.
    *   **Meal Select**: We customized the label to show "Date - Meal Name" so it's clear what is being ordered.
    *   **Status**: A dropdown (Ordered, Collected, Cancelled).
- **Table**: Shows Staff Name, Date, Meal Name, and Color-coded Status.

## 4. Conclusion
We now have a fully functional Meal Management system backend:
1.  Define the food (Meal Items).
2.  Schedule the food (Daily Menu).
3.  Track the orders (Staff Orders).
