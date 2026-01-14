# Part 1: Project Setup and Architecture

## 1. Environment & Versions
We are using the absolute latest version of **Laravel (v12.x)** as requested.
However, a key detail to understand in modern PHP development is **Dependency Compatibility**.
- **Your Machine**: Running PHP `8.2.12`.
- **Latest Packages**: Some "bleeding edge" libraries now require PHP `8.3+`.

To ensure our project is stable and installable without "hacking" (ignoring requirements), we strictly tell Composer to **only** install packages that work with your specific PHP version.

## 2. Dependency Management
We made a change to `composer.json` to "pin" the platform config. This ensures that every time we install a package, it picks the best version *compatible with your machine*, rather than failing because it tried to grab a version meant for a newer server.

## 3. The Tech Stack
- **Framework**: Laravel 12 (The core engine).
- **Admin Panel**: Filament v3 (The interface for Admin/HR/MIS).
- **Database**: MySQL (The data storage).


## 4. Why Filament?
Filament allows us to build the "Meal Selection", "HR Memos", and "Asset Tracking" modules in days instead of weeks. It provides built-in tables, forms, and validation, allowing us to focus on the *logic* (Business Rules) rather than the pixel-pushing (CSS/HTML).

## 5. Laravel Boost (AI Context)
We have installed **Laravel Boost** (`composer require laravel/boost --dev`) to give AI agents better context of your codebase.
> **Note**: To fully activate Boost, ensure your MySQL server is running and execute:
> ```bash
> php artisan boost:install
> ```

# Part 2: Troubleshooting
If you encounter "Connection refused" errors, please ensure:
1.  **XAMPP/MySQL** is running.
2.  Database `gfza_iop` exists.

