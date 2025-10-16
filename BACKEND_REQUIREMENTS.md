# Backend Requirements for Frontend PM Features

## 📋 Overview

The frontend now has complete CRUD functionality for all PM entities. Your Laravel backend needs to provide the following API endpoints to support these features.

---

## 🔐 **1. Authentication Endpoints**

### **POST /api/auth/register**
Create a new user account.

**Request:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "team_member"
    },
    "token": "1|laravel_sanctum_token_here"
  }
}
```

---

### **POST /api/auth/login**
Authenticate user and return token.

**Request:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "team_member"
    },
    "token": "1|laravel_sanctum_token_here"
  }
}
```

**Error (401):**
```json
{
  "message": "Invalid credentials"
}
```

---

### **POST /api/auth/logout** (Protected)
Invalidate current token.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Logged out successfully"
}
```

---

### **GET /api/auth/user** (Protected)
Get current authenticated user.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "team_member"
  }
}
```

---

## 📁 **2. Projects Endpoints**

### **GET /api/projects** (Protected)
List all projects.

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Website Redesign",
      "description": "Redesign company website",
      "status": "active",
      "owner_id": 1,
      "budget": 50000.00,
      "start_date": "2025-01-01",
      "end_date": "2025-06-30",
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    }
  ]
}
```

**Status options:** `planning`, `active`, `on_hold`, `completed`, `cancelled`

---

### **GET /api/projects/{id}** (Protected)
Get single project by ID.

**Response (200):**
```json
{
  "data": {
    "id": 1,
    "name": "Website Redesign",
    "description": "Redesign company website",
    "status": "active",
    "owner_id": 1,
    "budget": 50000.00,
    "start_date": "2025-01-01",
    "end_date": "2025-06-30",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

---

### **POST /api/projects** (Protected)
Create new project.

**Request:**
```json
{
  "name": "Website Redesign",
  "description": "Redesign company website",
  "status": "active",
  "budget": 50000.00,
  "start_date": "2025-01-01",
  "end_date": "2025-06-30"
}
```

**Response (201):**
```json
{
  "data": {
    "id": 1,
    "name": "Website Redesign",
    "description": "Redesign company website",
    "status": "active",
    "owner_id": 1,
    "budget": 50000.00,
    "start_date": "2025-01-01",
    "end_date": "2025-06-30",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

**Note:** `owner_id` should be auto-set to authenticated user's ID.

---

### **PUT /api/projects/{id}** (Protected)
Update existing project.

**Request:**
```json
{
  "name": "Website Redesign v2",
  "description": "Updated description",
  "status": "completed",
  "budget": 60000.00,
  "start_date": "2025-01-01",
  "end_date": "2025-12-31"
}
```

**Response (200):**
```json
{
  "data": {
    "id": 1,
    "name": "Website Redesign v2",
    "description": "Updated description",
    "status": "completed",
    "owner_id": 1,
    "budget": 60000.00,
    "start_date": "2025-01-01",
    "end_date": "2025-12-31",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-10-04T10:30:00.000000Z"
  }
}
```

---

### **DELETE /api/projects/{id}** (Protected)
Delete project.

**Response (204):**
```
(No content)
```

---

## 📋 **3. Tasks Endpoints**

### **GET /api/tasks** (Protected)
List all tasks (with optional project filter).

**Query Parameters (Optional):**
- `project_id` - Filter by project ID

**Example:** `GET /api/tasks?project_id=1`

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Design homepage",
      "description": "Create mockups for homepage",
      "status": "in_progress",
      "priority": "high",
      "project_id": 1,
      "assigned_to": 2,
      "due_date": "2025-02-01",
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    }
  ]
}
```

**Status options:** `todo`, `in_progress`, `in_review`, `completed`, `cancelled`
**Priority options:** `low`, `medium`, `high`, `urgent`

---

### **GET /api/tasks/{id}** (Protected)
Get single task by ID.

**Response (200):**
```json
{
  "data": {
    "id": 1,
    "title": "Design homepage",
    "description": "Create mockups for homepage",
    "status": "in_progress",
    "priority": "high",
    "project_id": 1,
    "assigned_to": 2,
    "due_date": "2025-02-01",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

---

### **POST /api/tasks** (Protected)
Create new task.

**Request:**
```json
{
  "title": "Design homepage",
  "description": "Create mockups for homepage",
  "status": "todo",
  "priority": "high",
  "project_id": 1,
  "assigned_to": 2,
  "due_date": "2025-02-01"
}
```

**Response (201):**
```json
{
  "data": {
    "id": 1,
    "title": "Design homepage",
    "description": "Create mockups for homepage",
    "status": "todo",
    "priority": "high",
    "project_id": 1,
    "assigned_to": 2,
    "due_date": "2025-02-01",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

---

### **PUT /api/tasks/{id}** (Protected)
Update existing task.

**Request:**
```json
{
  "title": "Design homepage v2",
  "description": "Updated description",
  "status": "completed",
  "priority": "medium",
  "project_id": 1,
  "assigned_to": 3,
  "due_date": "2025-03-01"
}
```

**Response (200):** Same as create response

---

### **PUT /api/tasks/{id}/status** (Protected) ⭐ IMPORTANT
Update ONLY task status (used by drag-and-drop).

**Request:**
```json
{
  "status": "in_progress"
}
```

**Response (200):**
```json
{
  "data": {
    "id": 1,
    "title": "Design homepage",
    "description": "Create mockups for homepage",
    "status": "in_progress",
    "priority": "high",
    "project_id": 1,
    "assigned_to": 2,
    "due_date": "2025-02-01",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-04T10:30:00.000000Z"
  }
}
```

---

### **PUT /api/tasks/{id}/assign** (Protected)
Assign task to user.

**Request:**
```json
{
  "user_id": 3
}
```

**Response (200):** Same format as above

---

### **DELETE /api/tasks/{id}** (Protected)
Delete task.

**Response (204):** No content

---

## 👥 **4. Teams Endpoints**

### **GET /api/teams** (Protected)
List all teams.

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Development Team",
      "description": "Main dev team",
      "member_ids": [1, 2, 3],
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    }
  ]
}
```

**Note:** `member_ids` is optional but helpful for member count display.

---

### **GET /api/teams/{id}** (Protected)
Get single team.

**Response (200):**
```json
{
  "data": {
    "id": 1,
    "name": "Development Team",
    "description": "Main dev team",
    "member_ids": [1, 2, 3],
    "members": [
      {"id": 1, "name": "John Doe"},
      {"id": 2, "name": "Jane Smith"},
      {"id": 3, "name": "Bob Johnson"}
    ],
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

---

### **POST /api/teams** (Protected)
Create new team.

**Request:**
```json
{
  "name": "Development Team",
  "description": "Main dev team"
}
```

**Response (201):** Same format as GET

---

### **PUT /api/teams/{id}** (Protected)
Update team.

**Request:**
```json
{
  "name": "Development Team Updated",
  "description": "Updated description"
}
```

**Response (200):** Same format as GET

---

### **DELETE /api/teams/{id}** (Protected)
Delete team.

**Response (204):** No content

---

### **GET /api/teams/{id}/members** (Protected)
Get team members.

**Response (200):**
```json
{
  "data": [
    {"id": 1, "name": "John Doe", "email": "john@example.com", "role": "team_lead"},
    {"id": 2, "name": "Jane Smith", "email": "jane@example.com", "role": "team_member"}
  ]
}
```

---

### **POST /api/teams/{id}/members** (Protected)
Add member to team.

**Request:**
```json
{
  "user_id": 4
}
```

**Response (200):**
```json
{
  "message": "Member added successfully",
  "data": {
    "id": 1,
    "name": "Development Team",
    "member_ids": [1, 2, 3, 4]
  }
}
```

---

### **DELETE /api/teams/{id}/members** (Protected)
Remove member from team.

**Request:**
```json
{
  "user_id": 4
}
```

**Response (200):**
```json
{
  "message": "Member removed successfully"
}
```

---

## 🎯 **5. Milestones Endpoints** ⭐ NEW

### **GET /api/milestones** (Protected)
List all milestones.

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Beta Release",
      "description": "Release beta version",
      "project_id": 1,
      "due_date": "2025-03-01",
      "status": "pending",
      "project": {
        "id": 1,
        "name": "Website Redesign"
      },
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    }
  ]
}
```

**Status options:** `pending`, `in_progress`, `completed`, `cancelled`

---

### **GET /api/milestones/{id}** (Protected)
Get single milestone.

**Response (200):** Same format as list

---

### **POST /api/milestones** (Protected)
Create milestone.

**Request:**
```json
{
  "name": "Beta Release",
  "description": "Release beta version",
  "project_id": 1,
  "due_date": "2025-03-01",
  "status": "pending"
}
```

**Response (201):** Same format as GET

---

### **PUT /api/milestones/{id}** (Protected)
Update milestone.

**Request:**
```json
{
  "name": "Beta Release v2",
  "description": "Updated description",
  "project_id": 1,
  "due_date": "2025-04-01",
  "status": "in_progress"
}
```

**Response (200):** Same format as GET

---

### **DELETE /api/milestones/{id}** (Protected)
Delete milestone.

**Response (204):** No content

---

## ⏱️ **6. Time Entries Endpoints** ⭐ NEW

### **GET /api/time-entries** (Protected)
List all time entries.

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "task_id": 1,
      "user_id": 1,
      "hours": 2.5,
      "description": "Worked on homepage design",
      "logged_at": "2025-01-04",
      "task": {
        "id": 1,
        "title": "Design homepage"
      },
      "created_at": "2025-01-04T00:00:00.000000Z",
      "updated_at": "2025-01-04T00:00:00.000000Z"
    }
  ]
}
```

---

### **GET /api/time-entries/{id}** (Protected)
Get single time entry.

**Response (200):** Same format as list

---

### **POST /api/time-entries** (Protected)
Create time entry.

**Request:**
```json
{
  "task_id": 1,
  "hours": 2.5,
  "description": "Worked on homepage design",
  "logged_at": "2025-01-04"
}
```

**Response (201):** Same format as GET

**Note:** `user_id` should be auto-set to authenticated user's ID.

---

### **PUT /api/time-entries/{id}** (Protected)
Update time entry.

**Request:**
```json
{
  "task_id": 1,
  "hours": 3.0,
  "description": "Updated work description",
  "logged_at": "2025-01-04"
}
```

**Response (200):** Same format as GET

---

### **DELETE /api/time-entries/{id}** (Protected)
Delete time entry.

**Response (204):** No content

---

## 👤 **7. Users Endpoint** (For Task Assignment)

### **GET /api/users** (Protected)
List all users (for assignment dropdowns).

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "team_lead"
    },
    {
      "id": 2,
      "name": "Jane Smith",
      "email": "jane@example.com",
      "role": "team_member"
    }
  ]
}
```

---

## 🔧 **Required Laravel Setup**

### **1. CORS Configuration**
Update `config/cors.php`:

```php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:3000'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

---

### **2. Laravel Sanctum**
Make sure Sanctum is configured for API token authentication:

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

In `app/Http/Kernel.php`:
```php
'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

---

### **3. API Routes Structure**

Your `routes/api.php` should look like:

```php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\UserController;

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::put('/tasks/{task}/status', [TaskController::class, 'updateStatus']);
    Route::put('/tasks/{task}/assign', [TaskController::class, 'assign']);

    Route::apiResource('teams', TeamController::class);
    Route::get('/teams/{team}/members', [TeamController::class, 'members']);
    Route::post('/teams/{team}/members', [TeamController::class, 'addMember']);
    Route::delete('/teams/{team}/members', [TeamController::class, 'removeMember']);

    Route::apiResource('milestones', MilestoneController::class);
    Route::apiResource('time-entries', TimeEntryController::class);

    Route::get('/users', [UserController::class, 'index']);
});
```

---

## 📊 **Database Schema Updates Needed**

### **Milestones Table** (if not exists)
```sql
CREATE TABLE milestones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    project_id BIGINT UNSIGNED NOT NULL,
    due_date DATE NOT NULL,
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);
```

### **Time Entries Table** (if not exists)
```sql
CREATE TABLE time_entries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    hours DECIMAL(8, 2) NOT NULL,
    description TEXT NULL,
    logged_at DATE NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

---

## ✅ **Testing Checklist for Backend Engineer**

- [ ] Authentication endpoints return user + token
- [ ] All endpoints require `Authorization: Bearer {token}` header
- [ ] Projects CRUD works
- [ ] Tasks CRUD works
- [ ] **Tasks status update endpoint** (`PUT /tasks/{id}/status`) works
- [ ] Milestones CRUD works
- [ ] Time entries CRUD works
- [ ] Teams CRUD works
- [ ] Users list endpoint works
- [ ] CORS allows `http://localhost:3000`
- [ ] All responses follow `{ "data": {...} }` format
- [ ] Dates are in `YYYY-MM-DD` format
- [ ] Timestamps are ISO 8601 format
- [ ] 401 errors for unauthenticated requests
- [ ] 404 errors for not found resources
- [ ] 422 errors for validation failures

---

## 🐛 **Common Issues & Fixes**

### **Issue: CORS errors**
**Fix:** Update `config/cors.php` to allow `http://localhost:3000`

### **Issue: 401 Unauthorized**
**Fix:** Ensure Sanctum middleware is on API routes, check token is being sent

### **Issue: Drag-drop not updating status**
**Fix:** Implement `PUT /api/tasks/{id}/status` endpoint

### **Issue: Task assignment dropdown empty**
**Fix:** Implement `GET /api/users` endpoint

### **Issue: Milestones not loading**
**Fix:** Create milestones table and controller/routes

### **Issue: Timer not saving**
**Fix:** Create time_entries table and implement POST endpoint

---

## 📞 **Need Help?**

If you need clarification on any endpoint:
1. Check the frontend code in `src/components/forms/` and `src/components/pm/`
2. See exactly what data is being sent/expected
3. All API calls use `apiFetch()` from `src/lib/api.ts`

---

**Summary:** Implement all endpoints marked with ⭐ (Milestones, Time Entries) and ensure the drag-drop status update endpoint works. Everything else should already exist in your Laravel backend from previous phases.
