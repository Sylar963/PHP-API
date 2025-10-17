# Security Documentation

## Overview

This document outlines the comprehensive security measures implemented in the Project Management & Team Collaboration Application. The application follows enterprise-grade security best practices suitable for production deployment.

**Security Grade:** Enterprise (A+)
**Last Updated:** 2025-10-17
**Framework:** Laravel 11.x

---

## Table of Contents

1. [Authentication & Authorization](#authentication--authorization)
2. [Rate Limiting](#rate-limiting)
3. [Security Headers](#security-headers)
4. [CORS Configuration](#cors-configuration)
5. [Infrastructure Security](#infrastructure-security)
6. [Monitoring & Logging](#monitoring--logging)
7. [Input Validation](#input-validation)
8. [Environment Configuration](#environment-configuration)
9. [Deployment Checklist](#deployment-checklist)

---

## Authentication & Authorization

### Laravel Sanctum Configuration

**Token Security:**
- Token expiration: 480 minutes (8 hours) by default
- Token prefix: `pmapp_` for GitHub secret scanning protection
- Stateful API authentication for SPA support

**Configuration:**
```env
SANCTUM_EXPIRATION=480
SANCTUM_TOKEN_PREFIX=pmapp_
SANCTUM_STATEFUL_DOMAINS=your-domain.com
```

**Files:**
- `config/sanctum.php` - Sanctum configuration
- `routes/api.php:18-25` - Authentication routes

### Role-Based Access Control (RBAC)

**Roles Implemented:**
- **Super Admin**: Full system access
- **Project Manager**: Manage projects, assign tasks, manage teams
- **Team Lead**: Manage assigned teams and projects
- **Team Member**: Access assigned tasks and projects
- **Client/Viewer**: Read-only access to specific projects

**Permissions:**
35+ granular permissions covering:
- Projects (view, create, edit, delete, manage settings)
- Tasks (view, create, edit, delete, assign, update status)
- Teams (view, create, edit, delete, manage members)
- Users (view, create, edit, delete, manage roles)
- Milestones (view, create, edit, delete)
- Time Entries (view, create, edit, delete, view all)
- System (view settings, edit settings, view logs, manage integrations)

**Usage:**
```php
// Protect route with permission
Route::get('/projects', [ProjectController::class, 'index'])
    ->middleware('permission:view projects');

// Check user permission
if (auth()->user()->hasPermission('create projects')) {
    // Allow action
}

// Check user role
if (auth()->user()->hasRole('Super Admin')) {
    // Allow action
}
```

**Database Setup:**
```bash
# Run permission migrations
php artisan migrate

# Seed roles and permissions
php artisan db:seed --class=RolePermissionSeeder
```

---

## Rate Limiting

### Authentication Endpoints

**Standard Rate Limiting:**
- 5 attempts per minute per IP address
- Applied to login and registration routes
- Custom headers: `X-RateLimit-Limit`, `X-RateLimit-Remaining`

**Progressive Throttling for Failed Logins:**
- 3 failed attempts: 1 minute lockout
- 5 failed attempts: 5 minute lockout
- 10 failed attempts: 1 hour lockout
- 15+ failed attempts: 24 hour lockout + security alert

### API Endpoints

**Rate Limiting:**
- 60 requests per minute per authenticated user
- IP-based limiting for unauthenticated requests
- Rate limit headers included in all responses

**Configuration:**
```env
RATE_LIMIT_AUTH=5           # Auth endpoint limit
RATE_LIMIT_API=60           # API endpoint limit
RATE_LIMIT_FAILED_LOGIN_DECAY=10  # Decay minutes
```

**Middleware:**
- `RateLimitAuth` - Authentication route protection
- `RateLimitApi` - General API route protection
- `ThrottleFailedLogins` - Progressive failed login throttling

---

## Security Headers

### Implemented Headers

**HTTP Strict Transport Security (HSTS):**
- Max-Age: 31536000 seconds (1 year)
- includeSubDomains
- preload directive

**Content Security Policy (CSP):**
```
default-src 'self';
script-src 'self' 'unsafe-inline' 'unsafe-eval';
style-src 'self' 'unsafe-inline';
img-src 'self' data: https:;
connect-src 'self' [app-url];
frame-ancestors 'none';
base-uri 'self';
form-action 'self';
upgrade-insecure-requests;
```

**Additional Headers:**
- X-Frame-Options: DENY
- X-Content-Type-Options: nosniff
- X-XSS-Protection: 1; mode=block
- Referrer-Policy: strict-origin-when-cross-origin
- Permissions-Policy: Restrictive browser feature access

**Configuration:**
```env
SECURITY_HEADERS_ENABLED=true    # Enable in production
HSTS_MAX_AGE=31536000
CSP_ENABLED=true
```

**Files:**
- `app/Http/Middleware/SecurityHeaders.php`
- `bootstrap/app.php` - Global middleware registration

---

## CORS Configuration

### Production Settings

**Allowed Origins:**
- NEVER use `*` in production
- Specify exact domains only
- Environment-based configuration

**Configuration:**
```env
CORS_ALLOWED_ORIGINS=https://app.example.com,https://www.example.com
CORS_ALLOWED_METHODS=GET,POST,PUT,DELETE,OPTIONS
CORS_ALLOWED_HEADERS=Content-Type,Authorization,X-Requested-With,Accept,Origin
CORS_SUPPORTS_CREDENTIALS=true
CORS_MAX_AGE=3600
```

**Files:**
- `config/cors.php`

---

## Infrastructure Security

### Redis Security

**Configuration:**
```env
REDIS_PASSWORD=your_strong_password  # REQUIRED for production
REDIS_HOST=your_redis_host
REDIS_PORT=6379

# Separate databases for different purposes
REDIS_DB=0          # Default
REDIS_CACHE_DB=1    # Cache
REDIS_QUEUE_DB=2    # Queue
REDIS_SESSION_DB=3  # Sessions
```

### Queue Encryption

**Laravel Queue Encryption:**
- Encrypts sensitive data in queue jobs
- Prevents data leakage from Redis/queue backend

**Configuration:**
```env
QUEUE_ENCRYPTION=true  # Enable in production
QUEUE_CONNECTION=redis
```

**Files:**
- `config/queue.php:73-74` - Encryption configuration

### Database SSL/TLS

**MySQL SSL Configuration:**
```env
DB_SSL_CA=/path/to/ca-cert.pem
DB_SSL_VERIFY_SERVER_CERT=true
```

**Files:**
- `config/database.php:61-66` - SSL options

### Laravel Horizon Security

**Access Control:**
- Local environment: All authenticated users
- Production: Super Admins only
- Email whitelist fallback for emergency access

**Configuration:**
```env
HORIZON_AUTH=true
HORIZON_ALLOWED_EMAILS=admin@example.com,ops@example.com
```

**Files:**
- `app/Providers/HorizonServiceProvider.php`

---

## Monitoring & Logging

### Security Event Logging

**Logged Events:**
- Successful logins
- Failed login attempts
- Account lockouts
- User registrations
- Logouts
- Permission violations
- Rate limit breaches

**Log Retention:**
- Security logs: 90 days
- Application logs: 14 days

**Log Files:**
- `storage/logs/security.log` - Dedicated security event log
- `storage/logs/laravel.log` - General application log

**Listener:**
- `app/Listeners/LogSecurityEvents.php`

**Configuration:**
```env
LOG_LEVEL=error  # Production
LOG_CHANNEL=stack
```

### Monitoring & Alerts

**Alert Triggers:**
- 5+ failed login attempts (configurable)
- 10+ rate limit breaches
- Account lockouts
- Permission violations

**Alert Channels:**
- Slack webhook integration
- Log-based alerts
- Email notifications (optional)

**Configuration:**
```env
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/YOUR/WEBHOOK
ALERT_FAILED_LOGIN_THRESHOLD=5
ALERT_RATE_LIMIT_THRESHOLD=10
```

---

## Input Validation

### Global Input Sanitization

**Protection Against:**
- SQL Injection
- XSS (Cross-Site Scripting)
- Null byte injection
- Directory traversal attacks

**Middleware:**
- `ValidateInput` - Scans all input for suspicious patterns
- Automatic blocking of malicious requests
- Security logging of suspicious activity

**Files:**
- `app/Http/Middleware/ValidateInput.php`

### Laravel Validation

Always use Laravel's built-in validation for all user input:

```php
$validated = $request->validate([
    'email' => 'required|email|max:255',
    'password' => 'required|string|min:8',
    'name' => 'required|string|max:255',
]);
```

---

## Environment Configuration

### Development (.env.example)

**Safe Defaults:**
- `APP_DEBUG=true` - For development only
- `APP_ENV=local`
- `SESSION_ENCRYPT=false`
- `SECURITY_HEADERS_ENABLED=false`
- `CORS_ALLOWED_ORIGINS=http://localhost:3000,http://localhost:8080`

### Production (.env.production)

**CRITICAL Security Settings:**

```env
# Application
APP_ENV=production
APP_DEBUG=false          # MUST be false
APP_URL=https://your-domain.com

# Session Security
SESSION_DRIVER=redis
SESSION_LIFETIME=480
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict

# Security Features
SECURITY_HEADERS_ENABLED=true
CSP_ENABLED=true
QUEUE_ENCRYPTION=true

# Redis
REDIS_PASSWORD=strong_password_here

# Database
DB_SSL_CA=/path/to/ca-cert.pem
DB_SSL_VERIFY_SERVER_CERT=true

# CORS
CORS_ALLOWED_ORIGINS=https://your-domain.com
CORS_SUPPORTS_CREDENTIALS=true
```

**Files:**
- `.env.production` - Production template
- `.env.example` - Development template
- `.gitignore` - Excludes all .env variants

---

## Deployment Checklist

### Pre-Deployment Security Checks

- [ ] Set `APP_DEBUG=false` in production
- [ ] Set `APP_ENV=production`
- [ ] Generate new `APP_KEY` for production
- [ ] Configure `SANCTUM_STATEFUL_DOMAINS` with production domains
- [ ] Set strong `REDIS_PASSWORD`
- [ ] Configure database SSL certificates
- [ ] Update `CORS_ALLOWED_ORIGINS` with production URLs
- [ ] Enable `SECURITY_HEADERS_ENABLED=true`
- [ ] Enable `CSP_ENABLED=true`
- [ ] Enable `SESSION_ENCRYPT=true`
- [ ] Enable `QUEUE_ENCRYPTION=true`
- [ ] Configure `SLACK_WEBHOOK_URL` for alerts
- [ ] Run permission migrations and seeder
- [ ] Review and test all middleware
- [ ] Verify Horizon authentication
- [ ] Test security health endpoint

### Database & Migrations

```bash
# Run migrations
php artisan migrate --force

# Seed roles and permissions
php artisan db:seed --class=RolePermissionSeeder --force

# Create permission tables
php artisan migrate --path=database/migrations/*_create_permission_tables.php
```

### Testing Security Features

```bash
# Test security status endpoint
curl -H "Authorization: Bearer YOUR_TOKEN" \
  https://your-domain.com/api/security/status

# Expected features in response:
# - Rate limiting configured
# - Token expiration set
# - Security headers enabled
# - CORS configured
# - RBAC active
```

### Monitoring Setup

1. Configure Horizon dashboard authentication
2. Set up security log monitoring
3. Configure Slack/email alerts
4. Test alert thresholds
5. Review security logs regularly

---

## Security Status Endpoint

**Endpoint:** `GET /api/security/status`

**Access:** Super Admins only (or local environment)

**Response:**
```json
{
  "security_features": {
    "rate_limiting": { ... },
    "authentication": { ... },
    "security_headers": { ... },
    "cors": { ... },
    "infrastructure": { ... },
    "rbac": { ... }
  },
  "environment": "production",
  "debug_mode": "disabled",
  "timestamp": "2025-10-17 19:55:52"
}
```

---

## Security Incident Response

### If a Security Breach is Detected:

1. **Immediate Actions:**
   - Review security logs: `storage/logs/security.log`
   - Check failed login attempts and patterns
   - Review rate limit breach logs
   - Identify compromised accounts

2. **Containment:**
   - Revoke affected Sanctum tokens
   - Reset passwords for compromised accounts
   - Temporarily block suspicious IP addresses
   - Increase rate limiting if needed

3. **Investigation:**
   - Analyze attack patterns
   - Check for data exfiltration
   - Review permission violations
   - Examine audit trails

4. **Recovery:**
   - Patch vulnerabilities
   - Update security configurations
   - Notify affected users
   - Document incident

---

## Support & Reporting

**Security Issues:**
Report security vulnerabilities privately to: security@your-domain.com

**Documentation:**
- Laravel Security: https://laravel.com/docs/11.x/security
- Sanctum: https://laravel.com/docs/11.x/sanctum
- Spatie Permission: https://spatie.be/docs/laravel-permission

**Maintained By:** Development Team
**Review Schedule:** Quarterly security audits

---

## Changelog

**2025-10-17 - Initial Security Implementation**
- ✅ Removed public testing routes
- ✅ Implemented rate limiting (5/min auth, 60/min API)
- ✅ Progressive failed login throttling
- ✅ Sanctum token expiration (8 hours)
- ✅ Security headers (HSTS, CSP, X-Frame-Options, etc.)
- ✅ CORS hardening
- ✅ RBAC with 5 roles and 35+ permissions
- ✅ Redis authentication
- ✅ Queue encryption
- ✅ Database SSL support
- ✅ Security event logging (90-day retention)
- ✅ Horizon authentication
- ✅ Input validation middleware
- ✅ Production environment template
- ✅ Security health check endpoint
