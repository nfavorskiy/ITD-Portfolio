# ITD Portfolio

A secure Laravel + Bootstrap CRUD application for managing blog posts and user profiles hosted at

[https://www.itdportfolio-laravel.blog/](https://www.itdportfolio-laravel.blog/)

---

## Security & Access Control Overview

This application is designed to prevent common web vulnerabilities, including **Insecure Direct Object Reference (IDOR)** and session hijacking, by following the **Principle of Least Privilege**.

### 1. Session Security

- **Session Storage:** Sessions are stored in the database (`config/session.php`), not in cookies or files.
- **Session Expiry:** Sessions expire after inactivity and are invalidated on logout.
- **Secure Cookies:** Cookies are set with `http_only` and `secure` flags.
- **CSRF Protection:** All forms use CSRF tokens. If a token mismatch occurs, the user is redirected with a warning.
- **HTTPS Enforcement:** In production, HTTPS is enforced (`AppServiceProvider.php`, `TrustProxies.php`).

### 2. Access Control & IDOR Prevention

- **Authentication Middleware:** Sensitive routes (profile, posts, settings, account deletion) are protected by `auth` and `verified` middleware (`routes/web.php`, `routes/auth.php`).
- **Authorization Checks:** Controllers ensure users can only access or modify their own data:
  - **Profile & Settings:** Only the authenticated user can view or update their profile ([ProfileController.php](app/Http/Controllers/ProfileController.php)).
  - **Posts:** Only the post owner or an admin can edit or delete posts ([PostController.php](app/Http/Controllers/PostController.php)).
  - **Account Deletion:** Only the logged-in user can delete their own account, and password confirmation is required.
- **No Direct ID Access:** All user actions are scoped to the authenticated user; IDs are not accepted from the client for sensitive actions.

#### Example: Preventing IDOR on Posts

```php
// Only post owner or admin can edit/delete
public function edit(Post $post)
{
    if (auth()->user()->id !== $post->user_id && !auth()->user()->isAdmin()) {
        abort(403, 'Unauthorized action.');
    }
    return view('posts.edit', compact('post'));
}
```

---

## How to Verify Access Control Features

You can verify access control and IDOR prevention by visiting these pages in the hosted app:

| Feature                    | URL/Route                    | Access Control                                                          |
| -------------------------- | ---------------------------- | ----------------------------------------------------------------------- |
| **Profile Page**     | `/profile`                 | Authenticated users only; shows own profile                             |
| **Profile Settings** | `/profile/settings`        | Authenticated users only; can only update own info                      |
| **Posts**            | `/posts`                   | Authenticated users only; can only edit/delete own posts (unless admin) |
| **Delete Account**   | `/profile` (Delete button) | Authenticated users only; password confirmation required                |
| **Password Update**  | `/profile/settings`        | Authenticated users only; password confirmation required                |

**Instructions:**

- Log in as a user and try to access another user's profile or edit/delete another user's post. You will receive a **403 Forbidden** error.
- Attempting to access protected routes without authentication will redirect you to the login page.
- All sensitive actions require authentication and, where appropriate, password confirmation.

---

## Additional Notes

- **Email Verification:** Users must verify their email before accessing certain features.
- **Notifications:** Security-related events (login, password change, account deletion) trigger notifications.
- **Error Pages:** Custom error pages for 403, 404, and 500 errors are provided for clarity and security.

---

## Summary

This application enforces strict access control and session security, ensuring users can only access or modify their own data, effectively preventing IDOR and session hijacking. All features are documented above for easy verification.
