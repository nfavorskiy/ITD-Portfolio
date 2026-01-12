# ITD Portfolio

A secure Laravel blog application demonstrating modern web security practices, built with Laravel 11 and Bootstrap 5.

**Live Demo:** [https://www.itdportfolio-laravel.blog/](https://www.itdportfolio-laravel.blog/)

---

## What is This Project?

ITD Portfolio is a full-featured blog platform where users can:

- Create an account and verify their email
- Write, edit, and delete blog posts
- Manage their profile and account settings
- Browse posts from other users

The application was built as a portfolio project. It demonstrates **secure coding practices** and **IDOR (Insecure Direct Object Reference) prevention** using Laravel Policies and middleware.

---

## Features

| Feature                       | Description                                                 |
| ----------------------------- | ----------------------------------------------------------- |
| **User Authentication** | Registration, login, logout, password reset                 |
| **Email Verification**  | Users must verify email before accessing protected features |
| **Blog Posts (CRUD)**   | Create, read, update, delete posts                          |
| **User Profiles**       | View and update profile information                         |
| **Role-Based Access**   | Admin/Moderator role with elevated permissions              |
| **Soft Delete**         | Deleted accounts preserve post authorship                   |

---

## Access Control Features (IDOR Prevention)

The application implements strict access control to prevent unauthorized access:

### Posts

| Action                     | Who Can Do It       | How to Test                                          |
| -------------------------- | ------------------- | ---------------------------------------------------- |
| **View all posts**   | Authenticated users | Visit `/posts`                                     |
| **View single post** | Authenticated users | Click any post                                       |
| **Create post**      | Authenticated users | Click "Add Post" button                              |
| **Edit post**        | Post owner only     | Edit button appears only on your posts               |
| **Delete post**      | Post owner OR Admin | Delete button appears on your posts (admins see all) |

**Try it:** Log in and try to access `/posts/{id}/edit` for a post you don't own → **403 Forbidden**

### Profile

| Action                    | Who Can Do It                       | How to Test                                     |
| ------------------------- | ----------------------------------- | ----------------------------------------------- |
| **View profile**    | Own profile only                    | Visit `/profile`                              |
| **Update profile**  | Own profile only                    | Visit `/profile/settings`                     |
| **Change password** | Own account only                    | Visit `/profile/settings`                     |
| **Delete account**  | Own account + password confirmation | Click "Delete Account" on `/profile/settings` |

**Try it:** You cannot access another user's profile or settings - all profile routes are scoped to the authenticated user.

---

## Where to Find These Features

### In the Hosted App

| Page                  | URL                   | Features Demonstrated                           |
| --------------------- | --------------------- | ----------------------------------------------- |
| **Home**        | `/`                 | Public landing page                             |
| **Login**       | `/login`            | Rate limiting (3 attempts, 5 min lockout)       |
| **Register**    | `/register`         | Strong password policy, unique username/email   |
| **Posts List**  | `/posts`            | View all posts, filter "Show only your Posts"   |
| **Single Post** | `/posts/{id}`       | View post, conditional Edit/Delete buttons      |
| **Create Post** | `/posts/create`     | Authenticated users only                        |
| **Edit Post**   | `/posts/{id}/edit`  | Owner only (Policy enforced)                    |
| **Profile**     | `/profile`          | View your profile                               |
| **Settings**    | `/profile/settings` | Update profile, change password, delete account |

### In the Source Code

| Security Feature              | File Location                                  |
| ----------------------------- | ---------------------------------------------- |
| **Post Policy**         | `app/Policies/PostPolicy.php`                |
| **User Policy**         | `app/Policies/UserPolicy.php`                |
| **Policy Registration** | `app/Providers/AppServiceProvider.php`       |
| **Post Controller**     | `app/Http/Controllers/PostController.php`    |
| **Profile Controller**  | `app/Http/Controllers/ProfileController.php` |
| **Security Headers**    | `app/Http/Middleware/SecurityHeaders.php`    |
| **Login Rate Limiting** | `app/Http/Requests/Auth/LoginRequest.php`    |
| **Routes**              | `routes/web.php`, `routes/auth.php`        |

---

## Security Enhancements

### 1. Content Security Policy (CSP)

Prevents XSS attacks by controlling which scripts can execute.

```
Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-{random}'; style-src 'self' 'unsafe-inline' ...
```

- **Nonce-based script execution** - Only scripts with valid nonces run
- **No inline event handlers** - All JavaScript uses `addEventListener()`
- **Strict source restrictions** - External resources explicitly whitelisted

### 2. HTTP Security Headers

All responses include:

| Header                        | Value                                            | Purpose                |
| ----------------------------- | ------------------------------------------------ | ---------------------- |
| `Strict-Transport-Security` | `max-age=31536000; includeSubDomains; preload` | Forces HTTPS           |
| `X-Content-Type-Options`    | `nosniff`                                      | Prevents MIME sniffing |
| `X-Frame-Options`           | `SAMEORIGIN`                                   | Prevents clickjacking  |
| `X-XSS-Protection`          | `1; mode=block`                                | Legacy XSS filter      |
| `Referrer-Policy`           | `strict-origin-when-cross-origin`              | Controls referrer info |

### 3. Authentication Security

| Feature                          | Implementation                                  |
| -------------------------------- | ----------------------------------------------- |
| **Login Rate Limiting**    | 5 failed attempts → 5 minute lockout           |
| **Strong Password Policy** | Min 8 chars, mixed case, numbers, symbols       |
| **Session Security**       | Database-stored sessions, secure cookies        |
| **CSRF Protection**        | All forms protected with CSRF tokens            |
| **Password Confirmation**  | Required for sensitive actions (delete account) |

### 4. Authorization with Laravel Policies

```php
// PostPolicy.php
public function update(User $user, Post $post): bool
{
    return $user->id === $post->user_id;
}

public function delete(User $user, Post $post): bool
{
    return $user->id === $post->user_id || $user->isAdmin();
}
```

- **Centralized authorization logic** - Single source of truth
- **Controller integration** - `$this->authorize('update', $post)`
- **Blade directives** - `@can('update', $post)` for UI

### 5. IDOR Prevention

- **No client-supplied IDs for ownership** - Profile actions use `auth()->user()`
- **Policy checks on every action** - Edit, update, delete all verified
- **Route model binding** - Laravel validates resource exists before policy check

### 6. Soft Delete for Users

When users delete their account:

- Posts are **preserved** with "Deleted User" attribution
- Email/username are **anonymized** to allow re-registration
- No orphaned data or broken foreign keys

### 7. HTTPS Enforcement

```php
// Production only
URL::forceScheme('https');

// Redirect non-www to www
if (Request::getHost() === 'itdportfolio-laravel.blog') {
    redirect('https://www.itdportfolio-laravel.blog' . Request::getRequestUri());
}
```

---

## Tech Stack

- **Backend:** Laravel 11 (PHP 8.2+)
- **Frontend:** Bootstrap 5, Alpine.js
- **Database:** MySQL (production), SQLite (testing)
- **Build:** Vite
- **Testing:** Pest PHP
- **Hosting:** Heroku

---

## Running Tests

```bash
# Run all tests
php artisan test

# Run policy tests only
php artisan test --filter=PostPolicyTest
```

---

## Local Development

```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Build assets
npm run dev

# Start server
php artisan serve
```

---

## License

This project is open-sourced for educational purposes.
