---
name: security-expert
description: Expert security auditor and fixer for web applications. Use when you need to analyze code for vulnerabilities (OWASP), implement security headers, secure file uploads, prevent SQL injection, or harden authorization logic.
---

# Security Expert

This skill enables Gemini CLI to perform comprehensive security audits and implement robust remediation strategies for web applications, with a focus on Laravel.

## Security Audit Workflow

Follow these steps when tasked with a security audit or hardening a specific module:

### 1. Ingestion & Discovery
- Identify the target files (Controllers, Models, Routes, Middlewares).
- Review `references/owasp-top-10.md` for common vulnerability patterns.
- Review `references/laravel-hardening.md` for framework-specific best practices.

### 2. Vulnerability Analysis
Perform a deep scan of the code looking for:
- **Missing Authorization**: Routes or controller methods without `authorize()` or permission middleware.
- **Insecure Input Handling**: Lack of validation or use of `$request->all()`.
- **Potential Injections**: Raw SQL, unescaped Blade output, or unvalidated URLs (SSRF).
- **Sensitive Data Exposure**: Plaintext storage, logging of PII, or insecure file access.

### 3. Remediation Planning
- Propose a clear fix for each identified issue.
- Prioritize fixes based on risk (e.g., Broken Access Control > Code Style).
- Ensure the fix follows modern best practices (e.g., using signed URLs for sensitive files).

### 4. Implementation
- Apply the fixes using the `replace` tool.
- Implement security-enhancing features (e.g., `ActivityLogger`, `SecurityHeaders`).
- Clean up any legacy or insecure patterns identified during the audit.

### 5. Verification
- Run existing tests to ensure no regressions.
- Propose new security-focused tests if applicable.

## Advanced Security Integration

### Secure File Access
When serving sensitive files (CNI, documents), always use the **Signed URL pattern**:
1. Create a signed route in `web.php`.
2. Implement a controller method that validates the signature and returns the file from protected storage.
3. Use a helper to generate temporary signed URLs.

### Activity Logging
Log all sensitive operations (logins, deletions, permission changes) using an `ActivityLogger`.

## Reference Material

- [OWASP Top 10](references/owasp-top-10.md): Common vulnerabilities and remediation.
- [Laravel Hardening](references/laravel-hardening.md): Framework-specific security configurations.
