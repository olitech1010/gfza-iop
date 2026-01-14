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

## 2. Next Steps
We will creating the **ServedMeal** model to allow the Admin (or Kitchen Manager) to schedule these meal items for specific dates.
