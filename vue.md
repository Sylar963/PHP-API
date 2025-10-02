# Vue.js 3.x SPA Setup Guide

## Project Management & Team Collaboration Application - Frontend

This guide provides comprehensive step-by-step instructions to build a Vue.js 3.x Single Page Application (SPA) that consumes the Laravel API backend, following **Domain-Driven Design (DDD)** and **Clean Architecture** principles.

**Repository Setup:** This is a **SEPARATE repository** from the Laravel backend. See `frontendABC.md` for architecture decisions.

---

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [DDD Principles for Frontend](#ddd-principles-for-frontend)
3. [Prerequisites](#prerequisites)
4. [Repository Setup](#repository-setup)
5. [Project Setup](#project-setup)
6. [Project Structure (DDD)](#project-structure-ddd)
7. [Environment Configuration](#environment-configuration)
8. [Domain Layer Implementation](#domain-layer-implementation)
9. [Infrastructure Layer (API)](#infrastructure-layer-api)
10. [Application Layer (State)](#application-layer-state)
11. [Presentation Layer (UI)](#presentation-layer-ui)
12. [Authentication Setup](#authentication-setup)
13. [Router Configuration](#router-configuration)
14. [Development Workflow](#development-workflow)
15. [Build & Deployment](#build-deployment)

---

## Architecture Overview

This Vue.js SPA follows the same architectural principles as the Laravel backend:

- **Domain-Driven Design (DDD)**: Business logic organized around domain concepts
- **Clean Architecture**: Dependency rule - inner layers don't depend on outer layers
- **Separation of Concerns**: Each layer has a specific responsibility
- **Independent Repository**: Separate Git repository from backend API

**Communication Flow:**
```
User → Presentation Layer (Vue Components)
     → Application Layer (Pinia Stores)
     → Infrastructure Layer (API Client)
     → Laravel Backend API
```

---

## DDD Principles for Frontend

### Why DDD in Frontend?

Traditional frontend structure mixes all concerns together. DDD provides:
- **Clear boundaries** between business logic and UI
- **Testable code** (domain logic independent of Vue)
- **Reusable logic** across different frameworks
- **Maintainable codebase** as application grows

### Four Layers Architecture

#### 1. Domain Layer (Core Business Logic)

**Purpose:** Pure business logic, independent of Vue.js or any framework

**Contains:**
- **Models**: Business entities (Project, Task, User, Team)
- **Value Objects**: Immutable values (ProjectStatus, TaskPriority, Email)
- **Domain Services**: Business rules and calculations
- **Interfaces**: Contracts that infrastructure must implement

**Rules:**
- ✅ NO Vue.js imports
- ✅ NO API calls
- ✅ NO framework dependencies
- ✅ Pure JavaScript/TypeScript only
- ✅ 100% testable with unit tests

**Example:**
```javascript
// domain/models/Project.js - Pure business entity
export class Project {
  constructor(id, name, description, status, ownerId) {
    this.id = id
    this.name = name
    this.description = description
    this.status = status
    this.ownerId = ownerId
  }

  isActive() {
    return this.status === 'active'
  }

  canBeCompletedBy(userId) {
    return this.ownerId === userId
  }
}
```

#### 2. Application Layer (Use Cases & State)

**Purpose:** Orchestrate domain logic, manage application state

**Contains:**
- **Pinia Stores**: Application state management
- **Use Cases/Services**: Application-specific workflows
- **DTOs**: Data Transfer Objects
- **Application Events**: Cross-feature communication

**Rules:**
- ✅ CAN use domain models
- ✅ CAN use infrastructure services
- ✅ Coordinates between layers
- ❌ NO UI components
- ❌ NO direct API calls (use infrastructure)

**Example:**
```javascript
// application/stores/projectStore.js
import { Project } from '@/domain/models/Project'
import { projectRepository } from '@/infrastructure/repositories/projectRepository'

export const useProjectStore = defineStore('projects', () => {
  const projects = ref([])

  async function loadProjects() {
    const data = await projectRepository.getAll()
    projects.value = data.map(dto =>
      new Project(dto.id, dto.name, dto.description, dto.status, dto.owner_id)
    )
  }

  return { projects, loadProjects }
})
```

#### 3. Infrastructure Layer (External Services)

**Purpose:** Communicate with external systems (API, localStorage, etc.)

**Contains:**
- **API Clients**: HTTP communication with backend
- **Repositories**: Implement domain repository interfaces
- **External Services**: Third-party integrations
- **Adapters**: Convert external data to domain models

**Rules:**
- ✅ Implements domain interfaces
- ✅ Handles HTTP, storage, external APIs
- ✅ Converts DTOs to domain models
- ❌ NO business logic
- ❌ NO UI components

**Example:**
```javascript
// infrastructure/repositories/projectRepository.js
import apiClient from '@/infrastructure/http/apiClient'
import { Project } from '@/domain/models/Project'

export const projectRepository = {
  async getAll() {
    const response = await apiClient.get('/projects')
    return response.data.data.map(dto =>
      new Project(dto.id, dto.name, dto.description, dto.status, dto.owner_id)
    )
  }
}
```

#### 4. Presentation Layer (UI Components)

**Purpose:** Display data and handle user interactions

**Contains:**
- **Vue Components**: UI elements
- **Views/Pages**: Route-level components
- **Composables**: Reusable Vue composition functions
- **UI Utilities**: Formatters, validators for display

**Rules:**
- ✅ Uses application stores
- ✅ Displays data from domain models
- ✅ Handles user input
- ❌ NO business logic
- ❌ NO direct API calls

**Example:**
```vue
<!-- presentation/views/projects/ProjectListView.vue -->
<script setup>
import { useProjectStore } from '@/application/stores/projectStore'

const projectStore = useProjectStore()
const activeProjects = computed(() =>
  projectStore.projects.filter(p => p.isActive())
)
</script>
```

### Dependency Flow

```
Presentation Layer (Vue Components)
        ↓ depends on
Application Layer (Pinia Stores, Use Cases)
        ↓ depends on
Domain Layer (Models, Business Logic)
        ↑ implemented by
Infrastructure Layer (API, Repositories)
```

**Key Rule:** Inner circles NEVER depend on outer circles

---

## Prerequisites

### Required Software
- **Node.js**: v18.x or v20.x (LTS recommended)
- **npm**: v9.x or higher (comes with Node.js)
- **Git**: For version control

### Verify Installation
```bash
node --version    # Should show v18.x or v20.x
npm --version     # Should show v9.x or higher
```

### Knowledge Requirements
- JavaScript ES6+
- Vue.js 3 Composition API basics
- Basic understanding of REST APIs
- HTML/CSS fundamentals
- Domain-Driven Design concepts (helpful but not required)

---

## Repository Setup

This frontend application is a **separate Git repository** from the Laravel backend.

### Step 1: Verify Backend Repository

```bash
# Check backend exists
ls /home/aladhimainwin/PHP/project-management-app

# Verify it's a git repository
cd /home/aladhimainwin/PHP/project-management-app
git status
```

### Step 2: Create Frontend Repository

The frontend will be created as a **sibling directory** to the backend:

```
/home/aladhimainwin/PHP/
├── project-management-app/          # Existing Laravel API (Repo #1)
└── project-management-frontend/     # New Vue.js SPA (Repo #2)
```

**Why Separate Repositories?**
- Independent deployment cycles
- Different technology stacks
- Team can work independently
- Frontend deployable to CDN (fast, cheap)
- Backend deployable to application servers

See `frontendABC.md` for detailed architecture comparison.

---

## Project Setup

### Step 1: Create Vue.js Project with Vite

Navigate to your main project directory (same level as the Laravel app):

```bash
cd /home/aladhimainwin/PHP
npm create vue@latest project-management-frontend
```

### Step 2: Configuration Prompts

When prompted, select the following options:

```
✔ Add TypeScript? › No
✔ Add JSX Support? › No
✔ Add Vue Router for Single Page Application development? › Yes
✔ Add Pinia for state management? › Yes
✔ Add Vitest for Unit testing? › Yes
✔ Add an End-to-End Testing Solution? › No
✔ Add ESLint for code quality? › Yes
✔ Add Prettier for code formatting? › Yes
```

### Step 3: Install Dependencies

```bash
cd project-management-frontend
npm install
```

### Step 4: Install Additional Dependencies

```bash
# HTTP client
npm install axios

# UI Framework (Choose one)
npm install element-plus  # OR npm install primevue primeicons

# Date handling
npm install dayjs

# Icons
npm install @heroicons/vue

# Form validation
npm install vee-validate yup

# Utilities
npm install lodash-es

# Chart library (for analytics/reports)
npm install chart.js vue-chartjs
```

### Step 5: Verify Installation

```bash
npm run dev
```

Visit `http://localhost:5173` - you should see the Vue.js welcome page.

### Step 6: Initialize Git Repository

```bash
cd /home/aladhimainwin/PHP/project-management-frontend

git init
git add .
git commit -m "feat: Initial Vue.js SPA setup with DDD architecture"

# When ready to push
# git remote add origin https://github.com/yourusername/project-management-frontend.git
# git branch -M main
# git push -u origin main
```

---

## Project Structure (DDD)

### Directory Structure Following DDD Principles

```bash
project-management-frontend/
├── .git/                                  # Separate Git repository
├── public/
│   └── favicon.ico
│
├── src/
│   │
│   ├── domain/                           # 🔴 DOMAIN LAYER (Core Business Logic)
│   │   ├── models/                       # Business entities
│   │   │   ├── Project.js
│   │   │   ├── Task.js
│   │   │   ├── User.js
│   │   │   ├── Team.js
│   │   │   ├── Milestone.js
│   │   │   └── TimeEntry.js
│   │   │
│   │   ├── value-objects/                # Immutable values
│   │   │   ├── ProjectStatus.js
│   │   │   ├── TaskPriority.js
│   │   │   ├── TaskStatus.js
│   │   │   ├── UserRole.js
│   │   │   └── Email.js
│   │   │
│   │   ├── services/                     # Domain business rules
│   │   │   ├── ProjectDomainService.js
│   │   │   ├── TaskDomainService.js
│   │   │   └── PermissionService.js
│   │   │
│   │   └── interfaces/                   # Contracts (repositories, services)
│   │       ├── IProjectRepository.js
│   │       ├── ITaskRepository.js
│   │       ├── ITeamRepository.js
│   │       └── IAuthService.js
│   │
│   ├── application/                      # 🟡 APPLICATION LAYER (Use Cases & State)
│   │   ├── stores/                       # Pinia stores (state management)
│   │   │   ├── authStore.js
│   │   │   ├── projectStore.js
│   │   │   ├── taskStore.js
│   │   │   ├── teamStore.js
│   │   │   ├── milestoneStore.js
│   │   │   ├── timeEntryStore.js
│   │   │   └── uiStore.js
│   │   │
│   │   ├── use-cases/                    # Application-specific workflows
│   │   │   ├── CreateProjectUseCase.js
│   │   │   ├── AssignTaskUseCase.js
│   │   │   └── CompleteProjectUseCase.js
│   │   │
│   │   └── dtos/                         # Data Transfer Objects
│   │       ├── CreateProjectDTO.js
│   │       ├── UpdateTaskDTO.js
│   │       └── LoginDTO.js
│   │
│   ├── infrastructure/                   # 🔵 INFRASTRUCTURE LAYER (External Services)
│   │   ├── http/                         # HTTP clients
│   │   │   ├── apiClient.js             # Axios instance & interceptors
│   │   │   └── endpoints.js             # API endpoint constants
│   │   │
│   │   ├── repositories/                 # Repository implementations
│   │   │   ├── HttpProjectRepository.js # Implements IProjectRepository
│   │   │   ├── HttpTaskRepository.js
│   │   │   ├── HttpTeamRepository.js
│   │   │   ├── HttpUserRepository.js
│   │   │   └── HttpAuthRepository.js
│   │   │
│   │   ├── services/                     # External service implementations
│   │   │   ├── LocalStorageService.js
│   │   │   ├── NotificationService.js
│   │   │   └── WebSocketService.js
│   │   │
│   │   └── adapters/                     # Data adapters/mappers
│   │       ├── ProjectAdapter.js         # API DTO → Domain Model
│   │       ├── TaskAdapter.js
│   │       └── UserAdapter.js
│   │
│   ├── presentation/                     # 🟢 PRESENTATION LAYER (UI)
│   │   ├── components/                   # Vue components
│   │   │   ├── common/                   # Shared UI components
│   │   │   │   ├── AppButton.vue
│   │   │   │   ├── AppInput.vue
│   │   │   │   ├── AppModal.vue
│   │   │   │   ├── AppTable.vue
│   │   │   │   └── LoadingSpinner.vue
│   │   │   │
│   │   │   ├── layout/                   # Layout components
│   │   │   │   ├── AppHeader.vue
│   │   │   │   ├── AppSidebar.vue
│   │   │   │   ├── AppFooter.vue
│   │   │   │   └── MainLayout.vue
│   │   │   │
│   │   │   └── features/                 # Feature-specific components
│   │   │       ├── projects/
│   │   │       │   ├── ProjectCard.vue
│   │   │       │   ├── ProjectForm.vue
│   │   │       │   └── ProjectStats.vue
│   │   │       ├── tasks/
│   │   │       │   ├── TaskCard.vue
│   │   │       │   ├── TaskForm.vue
│   │   │       │   └── TaskList.vue
│   │   │       └── teams/
│   │   │           ├── TeamCard.vue
│   │   │           ├── TeamMemberList.vue
│   │   │           └── AddMemberForm.vue
│   │   │
│   │   ├── views/                        # Page components (routes)
│   │   │   ├── auth/
│   │   │   │   ├── LoginView.vue
│   │   │   │   └── RegisterView.vue
│   │   │   ├── dashboard/
│   │   │   │   └── DashboardView.vue
│   │   │   ├── projects/
│   │   │   │   ├── ProjectListView.vue
│   │   │   │   ├── ProjectDetailView.vue
│   │   │   │   └── ProjectCreateView.vue
│   │   │   ├── tasks/
│   │   │   │   ├── TaskListView.vue
│   │   │   │   ├── TaskDetailView.vue
│   │   │   │   └── MyTasksView.vue
│   │   │   ├── teams/
│   │   │   │   ├── TeamListView.vue
│   │   │   │   ├── TeamDetailView.vue
│   │   │   │   └── TeamCreateView.vue
│   │   │   └── profile/
│   │   │       └── ProfileView.vue
│   │   │
│   │   ├── composables/                  # Vue composition functions
│   │   │   ├── useAuth.js
│   │   │   ├── usePermissions.js
│   │   │   ├── useNotification.js
│   │   │   └── useFormValidation.js
│   │   │
│   │   ├── router/                       # Vue Router
│   │   │   ├── index.js
│   │   │   └── guards.js
│   │   │
│   │   └── utils/                        # UI utilities
│   │       ├── formatters.js             # Date, currency formatting
│   │       ├── validators.js             # Form validation rules
│   │       └── constants.js              # UI constants
│   │
│   ├── shared/                           # Shared utilities (cross-layer)
│   │   ├── types/                        # Common types/interfaces
│   │   ├── constants/                    # Application constants
│   │   └── helpers/                      # Generic helper functions
│   │
│   ├── assets/                           # Static assets
│   │   ├── styles/
│   │   │   ├── main.css
│   │   │   ├── variables.css
│   │   │   └── tailwind.css
│   │   └── images/
│   │
│   ├── App.vue                           # Root component
│   └── main.js                           # Application entry point
│
├── tests/                                # Tests mirror src structure
│   ├── unit/
│   │   ├── domain/                       # Domain logic tests
│   │   ├── application/                  # Store tests
│   │   └── infrastructure/               # Repository tests
│   └── e2e/                             # End-to-end tests
│
├── .env                                  # Environment variables
├── .env.example                          # Environment template
├── .gitignore
├── index.html
├── package.json
├── vite.config.js
├── CLAUDE.md                             # DDD guidelines for AI
└── README.md                             # Project documentation
```

### Layer Responsibilities Summary

| Layer | Folder | Dependencies | Purpose |
|-------|--------|--------------|---------|
| **Domain** | `domain/` | None (pure JS) | Business logic, entities, rules |
| **Application** | `application/` | Domain | Orchestration, state management |
| **Infrastructure** | `infrastructure/` | Domain | External APIs, storage, services |
| **Presentation** | `presentation/` | Application, Domain | UI components, views |

---

## Environment Configuration

### Step 1: Create `.env` File

```bash
cd /home/aladhimainwin/PHP/project-management-frontend
cp .env.example .env  # If exists, or create manually
```

### Step 2: Add Environment Variables

Create/edit `.env`:

```env
# API Configuration
VITE_API_BASE_URL=http://127.0.0.1:8000/api
VITE_API_TIMEOUT=10000

# Application
VITE_APP_NAME=Project Management App
VITE_APP_VERSION=1.0.0

# Feature Flags (optional)
VITE_ENABLE_ANALYTICS=false
VITE_ENABLE_DEBUG=true
```

### Step 3: Update `vite.config.js`

```javascript
import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  server: {
    port: 5173,
    proxy: {
      '/api': {
        target: 'http://127.0.0.1:8000',
        changeOrigin: true,
        secure: false
      }
    }
  }
})
```

---

## Domain Layer Implementation

This is the **heart of your application** - pure business logic with NO framework dependencies.

### Step 1: Create Domain Models (`src/domain/models/`)

**Project Model** (`src/domain/models/Project.js`):

```javascript
// Pure JavaScript class - No Vue, No API calls
export class Project {
  constructor(id, name, description, status, ownerId, budget = 0, startDate = null, endDate = null) {
    this.id = id
    this.name = name
    this.description = description
    this.status = status
    this.ownerId = ownerId
    this.budget = budget
    this.startDate = startDate
    this.endDate = endDate
  }

  // Business logic methods
  isActive() {
    return this.status === 'active'
  }

  isCompleted() {
    return this.status === 'completed'
  }

  canBeEditedBy(userId) {
    return this.ownerId === userId
  }

  isOverBudget(spent) {
    return spent > this.budget
  }

  getDaysRemaining() {
    if (!this.endDate) return null
    const end = new Date(this.endDate)
    const now = new Date()
    const diff = end - now
    return Math.ceil(diff / (1000 * 60 * 60 * 24))
  }

  isOverdue() {
    const daysRemaining = this.getDaysRemaining()
    return daysRemaining !== null && daysRemaining < 0
  }
}
```

**Task Model** (`src/domain/models/Task.js`):

```javascript
export class Task {
  constructor(id, title, description, status, priority, projectId, assignedTo = null, dueDate = null) {
    this.id = id
    this.title = title
    this.description = description
    this.status = status
    this.priority = priority
    this.projectId = projectId
    this.assignedTo = assignedTo
    this.dueDate = dueDate
  }

  isCompleted() {
    return this.status === 'completed'
  }

  isPending() {
    return this.status === 'pending'
  }

  isInProgress() {
    return this.status === 'in_progress'
  }

  isAssigned() {
    return this.assignedTo !== null
  }

  isHighPriority() {
    return this.priority === 'high' || this.priority === 'urgent'
  }

  canBeCompletedBy(userId) {
    return this.assignedTo === userId
  }

  isOverdue() {
    if (!this.dueDate || this.isCompleted()) return false
    return new Date(this.dueDate) < new Date()
  }
}
```

### Step 2: Create Value Objects (`src/domain/value-objects/`)

**ProjectStatus** (`src/domain/value-objects/ProjectStatus.js`):

```javascript
export class ProjectStatus {
  static PLANNING = 'planning'
  static ACTIVE = 'active'
  static ON_HOLD = 'on_hold'
  static COMPLETED = 'completed'
  static CANCELLED = 'cancelled'

  static ALL = [
    this.PLANNING,
    this.ACTIVE,
    this.ON_HOLD,
    this.COMPLETED,
    this.CANCELLED
  ]

  static isValid(status) {
    return this.ALL.includes(status)
  }

  static getLabel(status) {
    const labels = {
      [this.PLANNING]: 'Planning',
      [this.ACTIVE]: 'Active',
      [this.ON_HOLD]: 'On Hold',
      [this.COMPLETED]: 'Completed',
      [this.CANCELLED]: 'Cancelled'
    }
    return labels[status] || 'Unknown'
  }

  static getColor(status) {
    const colors = {
      [this.PLANNING]: 'blue',
      [this.ACTIVE]: 'green',
      [this.ON_HOLD]: 'yellow',
      [this.COMPLETED]: 'gray',
      [this.CANCELLED]: 'red'
    }
    return colors[status] || 'gray'
  }
}
```

### Step 3: Create Domain Services (`src/domain/services/`)

**PermissionService** (`src/domain/services/PermissionService.js`):

```javascript
export class PermissionService {
  static canEditProject(project, user) {
    if (!user) return false
    if (user.role === 'super_admin') return true
    if (user.role === 'project_manager') return true
    return project.ownerId === user.id
  }

  static canDeleteProject(project, user) {
    if (!user) return false
    if (user.role === 'super_admin') return true
    return project.ownerId === user.id
  }

  static canAssignTask(task, user) {
    if (!user) return false
    if (user.role === 'super_admin') return true
    if (user.role === 'project_manager') return true
    if (user.role === 'team_lead') return true
    return false
  }

  static canCompleteTask(task, user) {
    if (!user) return false
    return task.assignedTo === user.id || user.role === 'super_admin'
  }
}
```

---

## Infrastructure Layer (API)

This layer handles ALL external communication - NO business logic here.

### Step 1: Create API Client (`src/infrastructure/http/apiClient.js`)

```javascript
import axios from 'axios'

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  timeout: parseInt(import.meta.env.VITE_API_TIMEOUT) || 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Request interceptor - Add auth token
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor - Handle errors
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export default apiClient
```

### Step 2: Create Repository Implementations (`src/infrastructure/repositories/`)

**HttpProjectRepository** (`src/infrastructure/repositories/HttpProjectRepository.js`):

```javascript
import apiClient from '../http/apiClient'
import { Project } from '@/domain/models/Project'

export class HttpProjectRepository {
  async getAll() {
    const response = await apiClient.get('/projects')
    return response.data.data.map(dto => this.toDomain(dto))
  }

  async getById(id) {
    const response = await apiClient.get(`/projects/${id}`)
    return this.toDomain(response.data.data)
  }

  async create(projectData) {
    const response = await apiClient.post('/projects', projectData)
    return this.toDomain(response.data.data)
  }

  async update(id, projectData) {
    const response = await apiClient.put(`/projects/${id}`, projectData)
    return this.toDomain(response.data.data)
  }

  async delete(id) {
    await apiClient.delete(`/projects/${id}`)
  }

  // Convert API DTO to Domain Model
  toDomain(dto) {
    return new Project(
      dto.id,
      dto.name,
      dto.description,
      dto.status,
      dto.owner_id,
      dto.budget || 0,
      dto.start_date,
      dto.end_date
    )
  }

  // Convert Domain Model to API DTO
  toDTO(project) {
    return {
      name: project.name,
      description: project.description,
      status: project.status,
      owner_id: project.ownerId,
      budget: project.budget,
      start_date: project.startDate,
      end_date: project.endDate
    }
  }
}

// Export singleton instance
export const projectRepository = new HttpProjectRepository()
```

**HttpTaskRepository** (`src/infrastructure/repositories/HttpTaskRepository.js`):

```javascript
import apiClient from '../http/apiClient'
import { Task } from '@/domain/models/Task'

export class HttpTaskRepository {
  async getAll() {
    const response = await apiClient.get('/tasks')
    return response.data.data.map(dto => this.toDomain(dto))
  }

  async getById(id) {
    const response = await apiClient.get(`/tasks/${id}`)
    return this.toDomain(response.data.data)
  }

  async create(taskData) {
    const response = await apiClient.post('/tasks', taskData)
    return this.toDomain(response.data.data)
  }

  async updateStatus(id, status) {
    const response = await apiClient.put(`/tasks/${id}/status`, { status })
    return this.toDomain(response.data.data)
  }

  async assign(id, userId) {
    const response = await apiClient.put(`/tasks/${id}/assign`, { user_id: userId })
    return this.toDomain(response.data.data)
  }

  async delete(id) {
    await apiClient.delete(`/tasks/${id}`)
  }

  toDomain(dto) {
    return new Task(
      dto.id,
      dto.title,
      dto.description,
      dto.status,
      dto.priority,
      dto.project_id,
      dto.assigned_to,
      dto.due_date
    )
  }
}

export const taskRepository = new HttpTaskRepository()
```

---

## Application Layer (State)

This layer orchestrates domain logic and manages application state using Pinia.

### Step 1: Create Pinia Stores (`src/application/stores/`)

**Project Store** (`src/application/stores/projectStore.js`):

```javascript
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { projectRepository } from '@/infrastructure/repositories/HttpProjectRepository'
import { ProjectStatus } from '@/domain/value-objects/ProjectStatus'

export const useProjectStore = defineStore('projects', () => {
  // State
  const projects = ref([])
  const currentProject = ref(null)
  const isLoading = ref(false)
  const error = ref(null)

  // Computed (getters)
  const activeProjects = computed(() =>
    projects.value.filter(p => p.isActive())
  )

  const completedProjects = computed(() =>
    projects.value.filter(p => p.isCompleted())
  )

  const overdueProjects = computed(() =>
    projects.value.filter(p => p.isOverdue())
  )

  // Actions
  async function fetchProjects() {
    isLoading.value = true
    error.value = null

    try {
      projects.value = await projectRepository.getAll()
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch projects'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function fetchProject(id) {
    isLoading.value = true
    error.value = null

    try {
      currentProject.value = await projectRepository.getById(id)
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch project'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function createProject(projectData) {
    isLoading.value = true
    error.value = null

    try {
      const newProject = await projectRepository.create(projectData)
      projects.value.push(newProject)
      return newProject
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create project'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function updateProject(id, projectData) {
    isLoading.value = true
    error.value = null

    try {
      const updatedProject = await projectRepository.update(id, projectData)
      const index = projects.value.findIndex(p => p.id === id)
      if (index !== -1) {
        projects.value[index] = updatedProject
      }
      currentProject.value = updatedProject
      return updatedProject
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update project'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function deleteProject(id) {
    isLoading.value = true
    error.value = null

    try {
      await projectRepository.delete(id)
      projects.value = projects.value.filter(p => p.id !== id)
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete project'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  return {
    // State
    projects,
    currentProject,
    isLoading,
    error,
    // Computed
    activeProjects,
    completedProjects,
    overdueProjects,
    // Actions
    fetchProjects,
    fetchProject,
    createProject,
    updateProject,
    deleteProject
  }
})
```

---

## Presentation Layer (UI)

This layer contains Vue components that use stores and display domain models.

### Example: Project List View (`src/presentation/views/projects/ProjectListView.vue`)

```vue
<template>
  <MainLayout>
    <div class="container mx-auto px-4 py-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Projects</h1>
        <router-link
          to="/projects/create"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          Create Project
        </router-link>
      </div>

      <!-- Loading State -->
      <div v-if="projectStore.isLoading" class="text-center py-8">
        <LoadingSpinner />
      </div>

      <!-- Error State -->
      <div v-else-if="projectStore.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        {{ projectStore.error }}
      </div>

      <!-- Empty State -->
      <div v-else-if="projectStore.projects.length === 0" class="text-center py-8">
        <p class="text-gray-600 mb-4">No projects found. Create your first project!</p>
      </div>

      <!-- Projects Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <ProjectCard
          v-for="project in projectStore.projects"
          :key="project.id"
          :project="project"
          :user="authStore.user"
          @edit="handleEdit"
          @delete="handleDelete"
        />
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useProjectStore } from '@/application/stores/projectStore'
import { useAuthStore } from '@/application/stores/authStore'
import MainLayout from '@/presentation/components/layout/MainLayout.vue'
import ProjectCard from '@/presentation/components/features/projects/ProjectCard.vue'
import LoadingSpinner from '@/presentation/components/common/LoadingSpinner.vue'

const projectStore = useProjectStore()
const authStore = useAuthStore()
const router = useRouter()

onMounted(async () => {
  await projectStore.fetchProjects()
})

const handleEdit = (project) => {
  router.push(`/projects/${project.id}/edit`)
}

const handleDelete = async (project) => {
  if (confirm(`Are you sure you want to delete "${project.name}"?`)) {
    try {
      await projectStore.deleteProject(project.id)
    } catch (error) {
      console.error('Failed to delete project:', error)
    }
  }
}
</script>
```

### Example: Project Card Component (`src/presentation/components/features/projects/ProjectCard.vue`)

```vue
<template>
  <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
    <div class="flex justify-between items-start mb-4">
      <h3 class="text-xl font-bold">{{ project.name }}</h3>
      <span :class="statusClass">
        {{ statusLabel }}
      </span>
    </div>

    <p class="text-gray-600 mb-4">{{ project.description }}</p>

    <div class="space-y-2 mb-4">
      <div v-if="project.budget" class="text-sm text-gray-500">
        Budget: ${{ formatCurrency(project.budget) }}
      </div>
      <div v-if="project.endDate" class="text-sm" :class="dueDateClass">
        {{ dueDateText }}
      </div>
    </div>

    <div class="flex gap-2">
      <router-link
        :to="`/projects/${project.id}`"
        class="flex-1 bg-blue-600 text-white text-center px-4 py-2 rounded hover:bg-blue-700"
      >
        View
      </router-link>

      <button
        v-if="canEdit"
        @click="$emit('edit', project)"
        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
      >
        Edit
      </button>

      <button
        v-if="canDelete"
        @click="$emit('delete', project)"
        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
      >
        Delete
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { ProjectStatus } from '@/domain/value-objects/ProjectStatus'
import { PermissionService } from '@/domain/services/PermissionService'

const props = defineProps({
  project: {
    type: Object,
    required: true
  },
  user: {
    type: Object,
    default: null
  }
})

defineEmits(['edit', 'delete'])

// Use domain logic for permissions
const canEdit = computed(() =>
  PermissionService.canEditProject(props.project, props.user)
)

const canDelete = computed(() =>
  PermissionService.canDeleteProject(props.project, props.user)
)

// Use value objects for status display
const statusLabel = computed(() =>
  ProjectStatus.getLabel(props.project.status)
)

const statusClass = computed(() => {
  const color = ProjectStatus.getColor(props.project.status)
  return `px-3 py-1 rounded-full text-sm bg-${color}-100 text-${color}-800`
})

// Use domain model methods
const dueDateText = computed(() => {
  const days = props.project.getDaysRemaining()
  if (days === null) return ''
  if (days < 0) return `Overdue by ${Math.abs(days)} days`
  if (days === 0) return 'Due today'
  return `${days} days remaining`
})

const dueDateClass = computed(() => {
  return props.project.isOverdue() ? 'text-red-600 font-bold' : 'text-gray-500'
})

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US').format(amount)
}
</script>
```

---

## API Integration Layer (Legacy Section - Use Infrastructure Layer Instead)

### Step 1: Create Axios Client (`src/api/client.js`)

```javascript
import axios from 'axios'

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  timeout: parseInt(import.meta.env.VITE_API_TIMEOUT) || 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Request interceptor - Add auth token
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor - Handle errors
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response) {
      // Handle 401 Unauthorized
      if (error.response.status === 401) {
        localStorage.removeItem('auth_token')
        localStorage.removeItem('user')
        window.location.href = '/login'
      }

      // Handle 403 Forbidden
      if (error.response.status === 403) {
        console.error('Access denied')
      }

      // Handle 500 Server Error
      if (error.response.status >= 500) {
        console.error('Server error occurred')
      }
    }

    return Promise.reject(error)
  }
)

export default apiClient
```

### Step 2: Authentication API (`src/api/auth.js`)

```javascript
import apiClient from './client'

export default {
  register(data) {
    return apiClient.post('/auth/register', data)
  },

  login(credentials) {
    return apiClient.post('/auth/login', credentials)
  },

  logout() {
    return apiClient.post('/auth/logout')
  },

  getCurrentUser() {
    return apiClient.get('/auth/user')
  }
}
```

### Step 3: Projects API (`src/api/projects.js`)

```javascript
import apiClient from './client'

export default {
  getAll() {
    return apiClient.get('/projects')
  },

  getById(id) {
    return apiClient.get(`/projects/${id}`)
  },

  create(data) {
    return apiClient.post('/projects', data)
  },

  update(id, data) {
    return apiClient.put(`/projects/${id}`, data)
  },

  delete(id) {
    return apiClient.delete(`/projects/${id}`)
  }
}
```

### Step 4: Tasks API (`src/api/tasks.js`)

```javascript
import apiClient from './client'

export default {
  getAll() {
    return apiClient.get('/tasks')
  },

  getById(id) {
    return apiClient.get(`/tasks/${id}`)
  },

  create(data) {
    return apiClient.post('/tasks', data)
  },

  updateStatus(id, status) {
    return apiClient.put(`/tasks/${id}/status`, { status })
  },

  assign(id, userId) {
    return apiClient.put(`/tasks/${id}/assign`, { user_id: userId })
  },

  delete(id) {
    return apiClient.delete(`/tasks/${id}`)
  }
}
```

### Step 5: Teams API (`src/api/teams.js`)

```javascript
import apiClient from './client'

export default {
  getAll() {
    return apiClient.get('/teams')
  },

  getById(id) {
    return apiClient.get(`/teams/${id}`)
  },

  create(data) {
    return apiClient.post('/teams', data)
  },

  delete(id) {
    return apiClient.delete(`/teams/${id}`)
  },

  getMembers(id) {
    return apiClient.get(`/teams/${id}/members`)
  },

  addMember(id, userId) {
    return apiClient.post(`/teams/${id}/members`, { user_id: userId })
  },

  removeMember(id, userId) {
    return apiClient.delete(`/teams/${id}/members`, {
      data: { user_id: userId }
    })
  }
}
```

---

## Authentication Setup

### Step 1: Create Auth Store (`src/stores/auth.js`)

```javascript
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import authAPI from '@/api/auth'
import router from '@/router'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('auth_token') || null)
  const isLoading = ref(false)
  const error = ref(null)

  const isAuthenticated = computed(() => !!token.value)
  const userRole = computed(() => user.value?.role || null)

  async function login(credentials) {
    isLoading.value = true
    error.value = null

    try {
      const response = await authAPI.login(credentials)
      const { token: authToken, user: userData } = response.data.data

      token.value = authToken
      user.value = userData

      localStorage.setItem('auth_token', authToken)
      localStorage.setItem('user', JSON.stringify(userData))

      router.push('/dashboard')
    } catch (err) {
      error.value = err.response?.data?.message || 'Login failed'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function register(data) {
    isLoading.value = true
    error.value = null

    try {
      await authAPI.register(data)
      // Auto-login after registration
      await login({
        email: data.email,
        password: data.password
      })
    } catch (err) {
      error.value = err.response?.data?.message || 'Registration failed'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function logout() {
    try {
      await authAPI.logout()
    } catch (err) {
      console.error('Logout error:', err)
    } finally {
      user.value = null
      token.value = null
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      router.push('/login')
    }
  }

  async function fetchCurrentUser() {
    if (!token.value) return

    try {
      const response = await authAPI.getCurrentUser()
      user.value = response.data.data
      localStorage.setItem('user', JSON.stringify(user.value))
    } catch (err) {
      console.error('Failed to fetch user:', err)
      logout()
    }
  }

  function initializeAuth() {
    const storedUser = localStorage.getItem('user')
    if (storedUser && token.value) {
      user.value = JSON.parse(storedUser)
    }
  }

  return {
    user,
    token,
    isLoading,
    error,
    isAuthenticated,
    userRole,
    login,
    register,
    logout,
    fetchCurrentUser,
    initializeAuth
  }
})
```

### Step 2: Create Login View (`src/views/auth/LoginView.vue`)

```vue
<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
      <h2 class="text-2xl font-bold text-center mb-6">Login</h2>

      <form @submit.prevent="handleLogin">
        <div class="mb-4">
          <label class="block text-gray-700 mb-2">Email</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2"
            placeholder="Enter your email"
          />
        </div>

        <div class="mb-6">
          <label class="block text-gray-700 mb-2">Password</label>
          <input
            v-model="form.password"
            type="password"
            required
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2"
            placeholder="Enter your password"
          />
        </div>

        <div v-if="authStore.error" class="mb-4 text-red-600 text-sm">
          {{ authStore.error }}
        </div>

        <button
          type="submit"
          :disabled="authStore.isLoading"
          class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50"
        >
          {{ authStore.isLoading ? 'Logging in...' : 'Login' }}
        </button>
      </form>

      <p class="mt-4 text-center text-gray-600">
        Don't have an account?
        <router-link to="/register" class="text-blue-600 hover:underline">
          Register
        </router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

const form = reactive({
  email: '',
  password: ''
})

const handleLogin = async () => {
  try {
    await authStore.login(form)
  } catch (err) {
    console.error('Login error:', err)
  }
}
</script>
```

### Step 3: Create Register View (`src/views/auth/RegisterView.vue`)

```vue
<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
      <h2 class="text-2xl font-bold text-center mb-6">Register</h2>

      <form @submit.prevent="handleRegister">
        <div class="mb-4">
          <label class="block text-gray-700 mb-2">Name</label>
          <input
            v-model="form.name"
            type="text"
            required
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2"
            placeholder="Enter your name"
          />
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 mb-2">Email</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2"
            placeholder="Enter your email"
          />
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 mb-2">Password</label>
          <input
            v-model="form.password"
            type="password"
            required
            minlength="8"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2"
            placeholder="Enter your password"
          />
        </div>

        <div class="mb-6">
          <label class="block text-gray-700 mb-2">Confirm Password</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            required
            minlength="8"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2"
            placeholder="Confirm your password"
          />
        </div>

        <div v-if="authStore.error" class="mb-4 text-red-600 text-sm">
          {{ authStore.error }}
        </div>

        <button
          type="submit"
          :disabled="authStore.isLoading"
          class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50"
        >
          {{ authStore.isLoading ? 'Registering...' : 'Register' }}
        </button>
      </form>

      <p class="mt-4 text-center text-gray-600">
        Already have an account?
        <router-link to="/login" class="text-blue-600 hover:underline">
          Login
        </router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const handleRegister = async () => {
  if (form.password !== form.password_confirmation) {
    authStore.error = 'Passwords do not match'
    return
  }

  try {
    await authStore.register(form)
  } catch (err) {
    console.error('Registration error:', err)
  }
}
</script>
```

---

## State Management (Pinia)

### Projects Store (`src/stores/projects.js`)

```javascript
import { defineStore } from 'pinia'
import { ref } from 'vue'
import projectsAPI from '@/api/projects'

export const useProjectsStore = defineStore('projects', () => {
  const projects = ref([])
  const currentProject = ref(null)
  const isLoading = ref(false)
  const error = ref(null)

  async function fetchProjects() {
    isLoading.value = true
    error.value = null

    try {
      const response = await projectsAPI.getAll()
      projects.value = response.data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch projects'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function fetchProject(id) {
    isLoading.value = true
    error.value = null

    try {
      const response = await projectsAPI.getById(id)
      currentProject.value = response.data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch project'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function createProject(data) {
    isLoading.value = true
    error.value = null

    try {
      const response = await projectsAPI.create(data)
      projects.value.push(response.data.data)
      return response.data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create project'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function updateProject(id, data) {
    isLoading.value = true
    error.value = null

    try {
      const response = await projectsAPI.update(id, data)
      const index = projects.value.findIndex(p => p.id === id)
      if (index !== -1) {
        projects.value[index] = response.data.data
      }
      currentProject.value = response.data.data
      return response.data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update project'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function deleteProject(id) {
    isLoading.value = true
    error.value = null

    try {
      await projectsAPI.delete(id)
      projects.value = projects.value.filter(p => p.id !== id)
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete project'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  return {
    projects,
    currentProject,
    isLoading,
    error,
    fetchProjects,
    fetchProject,
    createProject,
    updateProject,
    deleteProject
  }
})
```

### Tasks Store (`src/stores/tasks.js`)

```javascript
import { defineStore } from 'pinia'
import { ref } from 'vue'
import tasksAPI from '@/api/tasks'

export const useTasksStore = defineStore('tasks', () => {
  const tasks = ref([])
  const currentTask = ref(null)
  const isLoading = ref(false)
  const error = ref(null)

  async function fetchTasks() {
    isLoading.value = true
    error.value = null

    try {
      const response = await tasksAPI.getAll()
      tasks.value = response.data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch tasks'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function fetchTask(id) {
    isLoading.value = true
    error.value = null

    try {
      const response = await tasksAPI.getById(id)
      currentTask.value = response.data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch task'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function createTask(data) {
    isLoading.value = true
    error.value = null

    try {
      const response = await tasksAPI.create(data)
      tasks.value.push(response.data.data)
      return response.data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create task'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function updateTaskStatus(id, status) {
    isLoading.value = true
    error.value = null

    try {
      const response = await tasksAPI.updateStatus(id, status)
      const index = tasks.value.findIndex(t => t.id === id)
      if (index !== -1) {
        tasks.value[index].status = status
      }
      return response.data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update task status'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function assignTask(id, userId) {
    isLoading.value = true
    error.value = null

    try {
      const response = await tasksAPI.assign(id, userId)
      const index = tasks.value.findIndex(t => t.id === id)
      if (index !== -1) {
        tasks.value[index].assigned_to = userId
      }
      return response.data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to assign task'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  return {
    tasks,
    currentTask,
    isLoading,
    error,
    fetchTasks,
    fetchTask,
    createTask,
    updateTaskStatus,
    assignTask
  }
})
```

---

## Router Configuration

### Router Setup (`src/router/index.js`)

```javascript
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: '/dashboard'
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/auth/LoginView.vue'),
      meta: { guest: true }
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('@/views/auth/RegisterView.vue'),
      meta: { guest: true }
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('@/views/dashboard/DashboardView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/projects',
      name: 'projects',
      component: () => import('@/views/projects/ProjectListView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/projects/create',
      name: 'project-create',
      component: () => import('@/views/projects/ProjectCreateView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/projects/:id',
      name: 'project-detail',
      component: () => import('@/views/projects/ProjectDetailView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/tasks',
      name: 'tasks',
      component: () => import('@/views/tasks/TaskListView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/teams',
      name: 'teams',
      component: () => import('@/views/teams/TeamListView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/teams/:id',
      name: 'team-detail',
      component: () => import('@/views/teams/TeamDetailView.vue'),
      meta: { requiresAuth: true }
    }
  ]
})

// Navigation guard
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if (to.meta.guest && authStore.isAuthenticated) {
    next('/dashboard')
  } else {
    next()
  }
})

export default router
```

---

## Core Components

### Main Layout (`src/components/layout/MainLayout.vue`)

```vue
<template>
  <div class="min-h-screen bg-gray-100">
    <AppHeader />

    <div class="flex">
      <AppSidebar />

      <main class="flex-1 p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import AppHeader from './AppHeader.vue'
import AppSidebar from './AppSidebar.vue'
</script>
```

### Header Component (`src/components/layout/AppHeader.vue`)

```vue
<template>
  <header class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <h1 class="text-xl font-bold">Project Management</h1>

      <div class="flex items-center gap-4">
        <span class="text-gray-700">{{ authStore.user?.name }}</span>
        <button
          @click="handleLogout"
          class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
        >
          Logout
        </button>
      </div>
    </div>
  </header>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

const handleLogout = () => {
  authStore.logout()
}
</script>
```

### Sidebar Component (`src/components/layout/AppSidebar.vue`)

```vue
<template>
  <aside class="w-64 bg-white shadow-lg min-h-screen">
    <nav class="p-4">
      <ul class="space-y-2">
        <li>
          <router-link
            to="/dashboard"
            class="block px-4 py-2 rounded hover:bg-gray-100"
            active-class="bg-blue-100 text-blue-600"
          >
            Dashboard
          </router-link>
        </li>
        <li>
          <router-link
            to="/projects"
            class="block px-4 py-2 rounded hover:bg-gray-100"
            active-class="bg-blue-100 text-blue-600"
          >
            Projects
          </router-link>
        </li>
        <li>
          <router-link
            to="/tasks"
            class="block px-4 py-2 rounded hover:bg-gray-100"
            active-class="bg-blue-100 text-blue-600"
          >
            Tasks
          </router-link>
        </li>
        <li>
          <router-link
            to="/teams"
            class="block px-4 py-2 rounded hover:bg-gray-100"
            active-class="bg-blue-100 text-blue-600"
          >
            Teams
          </router-link>
        </li>
      </ul>
    </nav>
  </aside>
</template>
```

---

## Feature Modules

### Dashboard View (`src/views/dashboard/DashboardView.vue`)

```vue
<template>
  <MainLayout>
    <div>
      <h2 class="text-2xl font-bold mb-6">Dashboard</h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-gray-500 text-sm">Total Projects</h3>
          <p class="text-3xl font-bold">{{ projectsStore.projects.length }}</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-gray-500 text-sm">Total Tasks</h3>
          <p class="text-3xl font-bold">{{ tasksStore.tasks.length }}</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-gray-500 text-sm">My Tasks</h3>
          <p class="text-3xl font-bold">{{ myTasksCount }}</p>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-bold mb-4">Recent Projects</h3>
        <div v-if="projectsStore.isLoading">Loading...</div>
        <div v-else-if="projectsStore.projects.length === 0">
          No projects found
        </div>
        <ul v-else class="space-y-2">
          <li
            v-for="project in recentProjects"
            :key="project.id"
            class="border-b pb-2"
          >
            <router-link
              :to="`/projects/${project.id}`"
              class="text-blue-600 hover:underline"
            >
              {{ project.name }}
            </router-link>
          </li>
        </ul>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import MainLayout from '@/components/layout/MainLayout.vue'
import { useProjectsStore } from '@/stores/projects'
import { useTasksStore } from '@/stores/tasks'
import { useAuthStore } from '@/stores/auth'

const projectsStore = useProjectsStore()
const tasksStore = useTasksStore()
const authStore = useAuthStore()

const recentProjects = computed(() => {
  return projectsStore.projects.slice(0, 5)
})

const myTasksCount = computed(() => {
  return tasksStore.tasks.filter(
    task => task.assigned_to === authStore.user?.id
  ).length
})

onMounted(async () => {
  await Promise.all([
    projectsStore.fetchProjects(),
    tasksStore.fetchTasks()
  ])
})
</script>
```

### Project List View (`src/views/projects/ProjectListView.vue`)

```vue
<template>
  <MainLayout>
    <div>
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Projects</h2>
        <router-link
          to="/projects/create"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          Create Project
        </router-link>
      </div>

      <div v-if="projectsStore.isLoading" class="text-center py-8">
        Loading projects...
      </div>

      <div v-else-if="projectsStore.error" class="text-red-600">
        {{ projectsStore.error }}
      </div>

      <div v-else-if="projectsStore.projects.length === 0" class="text-center py-8">
        No projects found. Create your first project!
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="project in projectsStore.projects"
          :key="project.id"
          class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition"
        >
          <h3 class="text-lg font-bold mb-2">{{ project.name }}</h3>
          <p class="text-gray-600 mb-4">{{ project.description }}</p>

          <div class="flex justify-between items-center">
            <span class="text-sm text-gray-500">{{ project.status }}</span>
            <router-link
              :to="`/projects/${project.id}`"
              class="text-blue-600 hover:underline"
            >
              View Details
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { onMounted } from 'vue'
import MainLayout from '@/components/layout/MainLayout.vue'
import { useProjectsStore } from '@/stores/projects'

const projectsStore = useProjectsStore()

onMounted(async () => {
  await projectsStore.fetchProjects()
})
</script>
```

---

## Development Workflow

### Step 1: Start Development Servers

```bash
# Terminal 1: Laravel API
cd /home/aladhimainwin/PHP/project-management-app
php artisan serve

# Terminal 2: Vue.js Frontend
cd /home/aladhimainwin/PHP/project-management-frontend
npm run dev
```

### Step 2: Access the Application

- **Frontend**: http://localhost:5173
- **Backend API**: http://127.0.0.1:8000/api

### Step 3: Development Tasks

```bash
# Run linter
npm run lint

# Format code
npm run format

# Run tests
npm run test:unit

# Build for production
npm run build

# Preview production build
npm run preview
```

---

## Build & Deployment

### Step 1: Build for Production

```bash
cd /home/aladhimainwin/PHP/project-management-frontend
npm run build
```

This creates a `dist/` folder with optimized production files.

### Step 2: Environment Variables for Production

Update `.env.production`:

```env
VITE_API_BASE_URL=https://your-api-domain.com/api
VITE_API_TIMEOUT=10000
VITE_APP_NAME=Project Management App
```

### Step 3: Deploy Options

**Option A: Static Hosting (Netlify, Vercel)**
```bash
# Deploy to Netlify
netlify deploy --prod --dir=dist

# Deploy to Vercel
vercel --prod
```

**Option B: Serve with Laravel**
```bash
# Copy dist files to Laravel public folder
cp -r dist/* /path/to/laravel/public/
```

**Option C: Nginx**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/project-management-frontend/dist;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location /api {
        proxy_pass http://localhost:8000;
    }
}
```

---

## Main Entry Point Update

### Update `src/main.js`

```javascript
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import { useAuthStore } from './stores/auth'

import './assets/styles/main.css'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

// Initialize auth on app load
const authStore = useAuthStore()
authStore.initializeAuth()

app.mount('#app')
```

### Update `src/App.vue`

```vue
<template>
  <router-view />
</template>

<script setup>
// App root component
</script>

<style>
/* Global styles can be added here */
</style>
```

---

## Testing the Integration

### Step-by-Step Testing Flow

1. **Start both servers** (Laravel + Vue.js)
2. **Register a new user** at http://localhost:5173/register
3. **Login** with the registered credentials
4. **Navigate to Dashboard** - should show stats
5. **Create a project** via the UI
6. **Create tasks** for the project
7. **Create a team** and add members
8. **Verify all CRUD operations** work correctly

---

## Next Steps

1. **Add remaining features**:
   - Milestones management
   - Time entries tracking
   - File uploads
   - Real-time notifications (WebSockets)

2. **Improve UI/UX**:
   - Add loading skeletons
   - Implement toast notifications
   - Add form validation
   - Improve responsive design

3. **Testing**:
   - Write unit tests for stores
   - Write component tests
   - Add E2E tests with Cypress

4. **Performance**:
   - Implement lazy loading
   - Add caching strategies
   - Optimize bundle size

5. **Security**:
   - Implement CSRF protection
   - Add rate limiting
   - Sanitize user inputs

---

## Troubleshooting

### CORS Issues
If you encounter CORS errors, ensure Laravel has proper CORS configuration in `config/cors.php`.

### 401 Unauthorized
- Check that the token is being sent in requests
- Verify token hasn't expired
- Check Laravel Sanctum configuration

### Route Not Found
- Ensure Vue Router is in history mode
- Configure server to redirect all routes to `index.html`

### API Connection Failed
- Verify Laravel server is running
- Check `.env` file has correct API URL
- Test API endpoints with cURL or Postman first

---

## Resources

- **Vue.js 3 Docs**: https://vuejs.org/
- **Vite Docs**: https://vitejs.dev/
- **Pinia Docs**: https://pinia.vuejs.org/
- **Vue Router Docs**: https://router.vuejs.org/
- **Axios Docs**: https://axios-http.com/

---

**END OF GUIDE**

This comprehensive guide provides everything needed to build a production-ready Vue.js 3.x SPA that integrates with the Laravel API backend. Follow the steps in order for the best results.
