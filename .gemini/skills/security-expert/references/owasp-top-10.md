# OWASP Top 10 - Analysis & Remediation

Use this reference to identify and fix common security vulnerabilities in the codebase.

## 1. Broken Access Control
- **Detection**: Check if users can access resources they don't own. Look for missing authorization checks in controllers.
- **Fix**: Use Laravel Policies or Gates. Always validate that the authenticated user has permission to perform the action on the specific resource.
- **Example**: `auth()->user()->can('update', $resource)`

## 2. Cryptographic Failures
- **Detection**: Look for sensitive data stored in plain text (passwords, PII). Check for weak encryption or hardcoded keys.
- **Fix**: Use Laravel's `Hash` for passwords and `Crypt` for other sensitive data. Ensure `APP_KEY` is set and secure.

## 3. Injection (SQL, XSS)
- **Detection**: Look for raw SQL queries (`DB::raw`) or unescaped output in Blade (`{!! $var !!}`).
- **Fix**: Use Eloquent or query builder with parameter binding. Use `{{ $var }}` in Blade for automatic escaping. Validate and sanitize all user input.

## 4. Insecure Design
- **Detection**: Check for missing security logic at the architecture level (e.g., missing rate limiting on mutation routes).
- **Fix**: Implement rate limiting (`throttle` middleware), secure headers, and robust business rule validation.

## 5. Security Misconfiguration
- **Detection**: Check if `APP_DEBUG` is true in production. Look for overly permissive CORS or public directory listings.
- **Fix**: Ensure environment-specific configs. Use `SecurityHeaders` middleware.

## 6. Vulnerable and Outdated Components
- **Detection**: Check `composer.json` and `package.json` for old versions.
- **Fix**: Run `composer audit` and `npm audit`. Update dependencies regularly.

## 7. Identification and Authentication Failures
- **Detection**: Weak password policies, lack of MFA, or predictable session IDs.
- **Fix**: Use Laravel Fortify/Breeze for robust auth. Enforce strong passwords and session timeout.

## 8. Software and Data Integrity Failures
- **Detection**: Unverified downloads or insecure deserialization.
- **Fix**: Use checksums for file integrity. Avoid `unserialize()` on user-provided data.

## 9. Security Logging and Monitoring Failures
- **Detection**: Critical actions (login, delete, update) not logged.
- **Fix**: Use an `ActivityLogger` to track sensitive operations and auth failures.

## 10. Server-Side Request Forgery (SSRF)
- **Detection**: Unvalidated URLs used in server-side requests (e.g., fetching remote images).
- **Fix**: Sanitize and validate URLs. Use a whitelist of allowed domains.
