
## MODULE 2: AUTHENTICATION & AUTHORIZATION

**Objective:** Implement complete auth system with role-based dashboard redirects and session management.

**Dependencies:** Module 0, Module 1

**Key Files:**
```
Controllers:
- AuthController.php
- DashboardController.php

Middleware:
- CheckRole.php
- VerifyEmail.php

Views/Pages:
- Login page
- Register page (admin only)
- Forgot Password page
- Reset Password page
```

### 2.1 Laravel Authentication Setup

**Tasks:**
- [ ] Use Laravel Sanctum for API auth
  - `composer require laravel/sanctum`
  - `php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"`
  - Configure `.env` for session
- [ ] Create AuthController with methods:
  - `login(Request $request)` - email + password
  - `logout(Request $request)` - destroy session
  - `refreshToken()` - refresh auth token
- [ ] Add password hashing (Laravel's Hash facade)
- [ ] Create Auth routes (web + API)

**Testing:**
```bash
POST /api/v1/auth/login
{
  "email": "user@example.com",
  "password": "password123"
}

Response:
{
  "token": "xxxxx",
  "user": { ... },
  "message": "Login successful"
}
```

### 2.2 Role-Based Access Control (RBAC)

**Tasks:**
- [ ] Create middleware: `CheckRole`
  ```php
  Route::get('/admin', [AdminController::class, 'index'])
    ->middleware('auth:sanctum', 'role:admin');
  ```
- [ ] Create middleware: `HasPermission`
  ```php
  ->middleware('permission:manage_assets')
  ```
- [ ] Define all permissions in seeder:
  - HR: create_memo, manage_meals, manage_employees
  - MIS: manage_tickets, manage_assets, manage_emails
  - Admin: all permissions
  - Staff: view_memo, select_meal, submit_ticket
- [ ] Test middleware rejection for unauthorized users

**Testing:**
```bash
# With valid token but wrong role
GET /api/v1/admin/users
# Response: 403 Forbidden

# With valid token and correct role
GET /api/v1/admin/users
# Response: 200 OK
```

### 2.3 Dashboard Redirect Logic

**Tasks:**
- [ ] Create DashboardController::show()
  - Check user role
  - Redirect to appropriate dashboard:
    - Admin → `/dashboard/admin`
    - HR → `/dashboard/hr`
    - MIS → `/dashboard/mis`
    - Staff → `/dashboard/staff`
    - Director → `/dashboard/director`
- [ ] Create routes with redirects
- [ ] Add redirect after login in middleware
- [ ] Store redirect_to URL in session if coming from protected route

**Logic Flow:**
```
Login Form Submit
    ↓
AuthController@login (validate credentials)
    ↓
Generate token + create session
    ↓
GET /api/v1/user (with token)
    ↓
Check role from User model
    ↓
Frontend redirects to role-specific dashboard
    ↓
Dashboard page mounts + checks permission
    ↓
If no permission: redirect to staff dashboard
```

**Testing:**
```bash
# Login as admin → should redirect to /dashboard/admin
# Login as staff → should redirect to /dashboard/staff
# Login with invalid credentials → show error message
```

### 2.4 Login & Authentication Pages

**Tasks:**
- [ ] Create Login page component (React/Vue)
  - Email input
  - Password input
  - Remember me checkbox
  - Forgot password link
  - Sign up link (disabled for regular users)
  - Loading state on submit
  - Error message display
- [ ] Create Forgot Password page
  - Email input
  - Submit button
  - Success message
- [ ] Create Reset Password page (token-based)
  - New password input
  - Confirm password input
  - Submit button

**Styling:** (Reference UI Guide)
- Two-column layout
- Form on left (50%), hero image on right (50%)
- Primary green (#00c73f) for buttons
- Poppins font
- Mobile: single column, hero image background

**Testing:**
- [ ] Manual login with correct credentials
- [ ] Login with wrong password → error message
- [ ] Login with non-existent email → error message
- [ ] Forgot password email sent
- [ ] Reset password token works
- [ ] Mobile responsive

### 2.5 Session & Token Management

**Tasks:**
- [ ] Implement token expiration (24 hours)
- [ ] Create refresh token endpoint
- [ ] Add "remember me" functionality (7 days)
- [ ] Implement logout (revoke token)
- [ ] Add session timeout warning (5 min before expire)
- [ ] Store token in HttpOnly cookie (secure)

**Code Example:**
```php
// AuthController
public function login(Request $request)
{
    $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:6',
    ]);

    $user = User::where('email', $validated['email'])->first();
    
    if (!$user || !Hash::check($validated['password'], $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('api-token')->plainTextToken;
    
    return response()->json([
        'message' => 'Login successful',
        'token' => $token,
        'user' => $user->load('roles', 'department'),
    ]);
}
```

### 2.6 Email Verification (Optional for Phase 1)

**Tasks:**
- [ ] Create email verification migration
- [ ] Send verification email on registration
- [ ] Create email verification link
- [ ] Mark user as verified after clicking link
- [ ] Block login until email verified (configurable)

### 2.7 Module 2 Integration Testing

**Test Cases:**
```php
// tests/Feature/AuthTest.php
test('user can login with valid credentials')
test('user cannot login with invalid password')
test('user cannot login with non-existent email')
test('logged-in user is redirected to dashboard')
test('admin is redirected to admin dashboard')
test('staff is redirected to staff dashboard')
test('user can logout')
test('token expires after 24 hours')
test('forgot password sends email')
test('reset password token works')
test('unauthorized user cannot access admin routes')
```

**Manual Testing:**
- [ ] Login flow with all user types
- [ ] Verify correct dashboard loads
- [ ] Test session persistence (refresh page)
- [ ] Test logout functionality
- [ ] Test unauthorized access (403 error)
- [ ] Test token expiration
- [ ] Mobile login experience

### Module 2 Testing Checklist
```
✓ Login page loads correctly
✓ Login with correct credentials works
✓ Login with wrong password shows error
✓ Admin redirects to admin dashboard
✓ HR redirects to hr dashboard
✓ MIS redirects to mis dashboard
✓ Staff redirects to staff dashboard
✓ Logout works and clears token
✓ Unauthorized users get 403 error
✓ Token expires correctly
✓ Session persists on page refresh
✓ Forgot password sends email
✓ Reset password works
✓ All pages responsive on mobile
✓ No console errors
```

### Estimated Timeline: 6-8 days

---
