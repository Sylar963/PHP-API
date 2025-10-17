# Backend Engineer Quick Checklist

## 🎯 What Needs to Be Done

Your backend engineer needs to implement/verify these endpoints to support the new frontend features.

---

## ✅ **High Priority (Critical for Core Features)**

### 1. **Authentication** (Should already exist)
- [x] `POST /api/auth/register` - Register new user
- [x] `POST /api/auth/login` - Login (return user + token)
- [x] `POST /api/auth/logout` - Logout
- [x] `GET /api/auth/user` - Get current user
- [x] Laravel Sanctum configured
- [x] CORS allows `http://localhost:3000`

### 2. **Projects** (Should already exist)
- [x] `GET /api/projects` - List all
- [x] `GET /api/projects/{id}` - Get one
- [x] `POST /api/projects` - Create
- [x] `PUT /api/projects/{id}` - Update
- [x] `DELETE /api/projects/{id}` - Delete

### 3. **Tasks** (Should already exist)
- [x] `GET /api/tasks` - List all (with `?project_id=X` filter)
- [x] `GET /api/tasks/{id}` - Get one
- [x] `POST /api/tasks` - Create
- [x] `PUT /api/tasks/{id}` - Update
- [x] `DELETE /api/tasks/{id}` - Delete
- [x] ⭐ **`PUT /api/tasks/{id}/status`** - Update status (for drag-drop) **CRITICAL**

### 4. **Teams** (Should already exist)
- [x] `GET /api/teams` - List all
- [x] `GET /api/teams/{id}` - Get one
- [x] `POST /api/teams` - Create
- [x] `PUT /api/teams/{id}` - Update
- [x] `DELETE /api/teams/{id}` - Delete
- [x] `GET /api/teams/{id}/members` - Get members (optional)
- [x] `POST /api/teams/{id}/members` - Add member (optional)

### 5. **Users** (For Task Assignment)
- [x] `GET /api/users` - List all users **REQUIRED FOR DROPDOWN**

---

## ⭐ **NEW Features to Implement**

### 6. **Milestones** ⭐ NEW - MUST IMPLEMENT
- [x] Create `milestones` table (migration)
- [x] Create `MilestoneController`
- [x] `GET /api/milestones` - List all
- [x] `GET /api/milestones/{id}` - Get one
- [x] `POST /api/milestones` - Create
- [x] `PUT /api/milestones/{id}` - Update
- [x] `DELETE /api/milestones/{id}` - Delete
- [x] Include `project` relationship in response

**Migration:**
```php
Schema::create('milestones', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->foreignId('project_id')->constrained()->onDelete('cascade');
    $table->date('due_date');
    $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
    $table->timestamps();
});
```

---

### 7. **Time Entries** ⭐ NEW - MUST IMPLEMENT
- [x] Create `time_entries` table (migration)
- [x] Create `TimeEntryController`
- [x] `GET /api/time-entries` - List all
- [x] `GET /api/time-entries/{id}` - Get one
- [x] `POST /api/time-entries` - Create
- [x] `PUT /api/time-entries/{id}` - Update
- [x] `DELETE /api/time-entries/{id}` - Delete
- [x] Include `task` relationship in response
- [x] Auto-set `user_id` to authenticated user

**Migration:**
```php
Schema::create('time_entries', function (Blueprint $table) {
    $table->id();
    $table->foreignId('task_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->decimal('hours', 8, 2);
    $table->text('description')->nullable();
    $table->date('logged_at');
    $table->timestamps();
});
```

---

## 🔧 **Configuration Checklist**

- [x] **CORS** configured to allow `http://localhost:3000`
- [x] **Laravel Sanctum** installed and configured
- [x] **API routes** use `auth:sanctum` middleware
- [x] **Response format** is `{ "data": {...} }` for all endpoints
- [x] **Dates** returned as `YYYY-MM-DD` format
- [x] **Timestamps** returned as ISO 8601 format
- [x] **Validation** returns 422 errors with messages
- [x] **Not found** returns 404 errors
- [x] **Unauthorized** returns 401 errors

---

## 🚨 **Most Critical Endpoints**

These are **ABSOLUTELY REQUIRED** for the frontend to work:

1. ⭐ **`PUT /api/tasks/{id}/status`** - For drag-and-drop tasks
2. ⭐ **`GET /api/users`** - For task assignment dropdown
3. ⭐ **Milestones CRUD** - Entire new feature
4. ⭐ **Time Entries CRUD** - Entire new feature (timer feature)

---

## 📝 **Expected Response Format**

All responses should follow this format:

**Success (200/201):**
```json
{
  "data": {
    // Your resource data here
  }
}
```

**List (200):**
```json
{
  "data": [
    // Array of resources
  ]
}
```

**Error (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

**Error (401):**
```json
{
  "message": "Unauthenticated."
}
```

---

## 🧪 **How to Test**

### 1. Test Authentication
```bash
# Register
curl -X POST http://127.0.0.1:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@test.com","password":"password","password_confirmation":"password"}'

# Login
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"password"}'
```

### 2. Test Protected Endpoints
```bash
# Replace TOKEN with token from login response
curl -X GET http://127.0.0.1:8000/api/projects \
  -H "Authorization: Bearer TOKEN"
```

### 3. Test Task Status Update (Drag-Drop)
```bash
curl -X PUT http://127.0.0.1:8000/api/tasks/1/status \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"status":"in_progress"}'
```

### 4. Test Milestones
```bash
curl -X POST http://127.0.0.1:8000/api/milestones \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name":"Test Milestone","project_id":1,"due_date":"2025-12-31","status":"pending"}'
```

### 5. Test Time Entries
```bash
curl -X POST http://127.0.0.1:8000/api/time-entries \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"task_id":1,"hours":2.5,"logged_at":"2025-01-04"}'
```

---

## 📚 **Full Documentation**

See [BACKEND_REQUIREMENTS.md](BACKEND_REQUIREMENTS.md) for:
- Complete API endpoint specifications
- Request/response examples
- Database schemas
- Configuration details

---

## 🎯 **Summary**

**Minimum Required:**
1. Implement Milestones controller + routes + migration
2. Implement Time Entries controller + routes + migration
3. Add `PUT /api/tasks/{id}/status` endpoint
4. Add `GET /api/users` endpoint
5. Verify CORS allows localhost:3000
6. Test all endpoints return proper JSON format

**That's it!** Everything else should already exist from previous development phases.

---

## 📞 **Questions?**

If your backend engineer has questions, they can:
1. Check [BACKEND_REQUIREMENTS.md](BACKEND_REQUIREMENTS.md) for detailed specs
2. Look at the frontend code to see exactly what's being sent/expected
3. Test using the curl commands above
4. Check the Laravel logs for errors

**Estimated Time:** 2-4 hours for an experienced Laravel developer
