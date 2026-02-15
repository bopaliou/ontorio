# Laravel Security Hardening Guide

Specific procedures for securing Laravel applications.

## 1. Request Validation
- **Practice**: Never use `$request->all()` for creating or updating models.
- **Fix**: Use Form Requests or `$request->validate([...])`. Use `$request->only([...])` or `$request->validated()`.

## 2. Authentication & Authorization
- **Practice**: Always use Spatie Permissions or Laravel's native authorization.
- **Fix**: Apply `middleware(['permission:...'])` or `middleware(['role:...'])` to routes. Check `$this->authorize()` in controllers.

## 3. Mass Assignment
- **Practice**: Protect models from mass assignment vulnerabilities.
- **Fix**: Use `$fillable` instead of `$guarded = []`.

## 4. Cross-Site Scripting (XSS)
- **Practice**: Avoid raw output.
- **Fix**: Use `{{ $data }}`. If raw output is necessary `{!! $data !!}`, ensure the data is sanitized via a library like HTMLPurifier.

## 5. Secure File Uploads
- **Practice**: Prevent execution of uploaded scripts.
- **Fix**: 
  - Validate file extension and MIME type.
  - Store files outside the web root if possible.
  - Generate a unique, random filename.
  - Serve sensitive files via signed URLs.

## 6. CSRF Protection
- **Practice**: Ensure all mutation routes (POST, PUT, PATCH, DELETE) are protected.
- **Fix**: Ensure `VerifyCsrfToken` middleware is active for `web` routes. Use `@csrf` in forms.

## 7. Security Headers
- **Practice**: Use OWASP recommended headers.
- **Fix**: Implement a middleware to add:
  - `Content-Security-Policy`
  - `X-Frame-Options: DENY`
  - `X-Content-Type-Options: nosniff`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `Strict-Transport-Security`

## 8. Database Security
- **Practice**: Prevent SQL injection.
- **Fix**: Use Eloquent or Query Builder with bindings. Avoid `whereRaw` with user-provided strings.
