# Frontend Repository Architecture Guide
## Repository Structure Best Practices for API + SPA Applications

This document explains the different approaches for organizing your Laravel API backend and Vue.js SPA frontend repositories, following industry best practices.

---

## Table of Contents

1. [Three Common Approaches](#three-common-approaches)
2. [Comparison Matrix](#comparison-matrix)
3. [Recommended Approach](#recommended-approach)
4. [Directory Structure Examples](#directory-structure-examples)
5. [Git Repository Setup](#git-repository-setup)
6. [Deployment Strategies](#deployment-strategies)
7. [Decision Tree](#decision-tree)

---

## Three Common Approaches

### **Approach 1: Separate Repositories (Recommended)**

Two independent Git repositories with completely separate codebases.

```
/home/aladhimainwin/PHP/
├── project-management-app/          # Repository #1 - Laravel API
│   ├── .git/
│   ├── app/
│   ├── routes/
│   ├── composer.json
│   └── README.md (Backend documentation)
│
└── project-management-frontend/     # Repository #2 - Vue.js SPA
    ├── .git/
    ├── src/
    ├── package.json
    └── README.md (Frontend documentation)
```

**Characteristics:**
- **Two Git repositories**
- **Separate deployment pipelines**
- **Independent versioning**
- **Different package managers** (Composer vs npm)
- **CORS required** (cross-origin requests)

**Industry Examples:**
- Facebook/Meta: Separate repos for React Native and backend APIs
- GitHub: Separate repos for github.com (frontend) and API
- Stripe: Dashboard (React) and API (separate repos)

---

### **Approach 2: Monorepo**

Single Git repository containing both frontend and backend as separate directories.

```
/home/aladhimainwin/PHP/project-management-monorepo/
├── .git/                           # Single Git repository
├── backend/                        # Laravel API
│   ├── app/
│   ├── routes/
│   └── composer.json
│
├── frontend/                       # Vue.js SPA
│   ├── src/
│   ├── package.json
│   └── vite.config.js
│
├── .gitignore                      # Shared
├── docker-compose.yml              # Optional: Both services
└── README.md                       # Project overview
```

**Characteristics:**
- **Single Git repository**
- **Shared version control**
- **Atomic commits** across full stack
- **Unified CI/CD** pipeline
- **Requires monorepo tools** (optional: Nx, Turborepo, Lerna)

**Industry Examples:**
- Google (internal): Most projects in single repo
- Uber: Many microservices in monorepo
- Airbnb: Frontend and backend together

---

### **Approach 3: Laravel-Integrated Frontend**

Vue.js source code lives inside Laravel's `resources/` directory and Laravel serves the compiled SPA.

```
/home/aladhimainwin/PHP/project-management-app/
├── .git/
├── app/                            # Laravel backend
├── resources/
│   ├── js/                         # Vue.js source code
│   │   ├── components/
│   │   ├── views/
│   │   ├── router/
│   │   ├── stores/
│   │   └── app.js
│   └── views/
│       └── app.blade.php           # Single blade file
│
├── public/
│   ├── build/                      # Compiled Vue assets (Vite)
│   └── index.php
│
├── routes/
│   ├── api.php                     # API routes
│   └── web.php                     # Fallback route for SPA
│
├── package.json                    # npm dependencies
├── vite.config.js                  # Vite configuration
└── composer.json                   # PHP dependencies
```

**Characteristics:**
- **Single Git repository**
- **Single deployment**
- **No CORS issues** (same origin)
- **Laravel Mix/Vite** handles building
- **Tightly coupled**

**Industry Examples:**
- Small agencies: Admin panels with Vue/React
- Internal tools: Company dashboards
- MVPs: Quick prototypes

---

## Comparison Matrix

| Feature | Separate Repos | Monorepo | Laravel-Integrated |
|---------|---------------|----------|-------------------|
| **Deployment Flexibility** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐ |
| **Scaling** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐ |
| **Team Independence** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐ |
| **Setup Complexity** | ⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **CORS Handling** | Required | Required | Not needed |
| **CI/CD Complexity** | Medium | High | Low |
| **Version Control** | Independent | Unified | Single |
| **CDN Deployment** | Easy | Easy | Manual |
| **Learning Curve** | Low | Medium | Low |
| **Maintenance** | ⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |

---

## Recommended Approach

### **For Your Project: Separate Repositories (Approach 1)**

**Why This is the Best Choice:**

1. **True SPA Architecture**
   - Frontend is completely independent
   - Can be deployed to CDN (Netlify, Vercel, CloudFlare)
   - Static file serving is fast and cheap

2. **API-First Design**
   - Your Laravel backend is already designed as pure API
   - No blade templates or server-side rendering
   - Clean separation of concerns

3. **Scalability**
   - Frontend: Deploy to global CDN (unlimited scale)
   - Backend: Scale API servers independently
   - Different scaling strategies for each

4. **Modern DevOps**
   - Separate CI/CD pipelines
   - Frontend: Deploy on every merge to main (minutes)
   - Backend: Deploy with proper testing (controlled)

5. **Team Flexibility**
   - Frontend developers work independently
   - Backend developers work independently
   - Different release schedules

6. **Technology Freedom**
   - Frontend can switch frameworks (Vue → React → Svelte)
   - Backend remains stable
   - No tight coupling

7. **Industry Standard (2024)**
   - This is how modern SaaS companies structure projects
   - Microservices-friendly
   - Cloud-native architecture

---

## Directory Structure Examples

### Separate Repositories Structure

```bash
# Your file system
/home/aladhimainwin/PHP/
│
├── project-management-app/              # Git Repository #1
│   ├── .git/
│   ├── .env
│   ├── .gitignore
│   ├── app/
│   │   ├── Application/
│   │   ├── Domain/
│   │   └── Infrastructure/
│   ├── routes/
│   │   └── api.php
│   ├── database/
│   ├── tests/
│   ├── composer.json
│   ├── artisan
│   ├── CLAUDE.md
│   ├── README.md                        # Backend documentation
│   └── .env.example
│
└── project-management-frontend/         # Git Repository #2
    ├── .git/
    ├── .env
    ├── .gitignore
    ├── src/
    │   ├── domain/                      # DDD: Domain layer
    │   ├── application/                 # DDD: Application layer
    │   ├── infrastructure/              # DDD: Infrastructure layer
    │   ├── presentation/                # DDD: Presentation layer
    │   ├── App.vue
    │   └── main.js
    ├── public/
    ├── tests/
    ├── package.json
    ├── vite.config.js
    ├── index.html
    ├── CLAUDE.md                        # Frontend DDD guidelines
    └── README.md                        # Frontend documentation
```

### Git Remote URLs (Example)

```bash
# Backend repository
https://github.com/yourusername/project-management-api.git

# Frontend repository
https://github.com/yourusername/project-management-frontend.git

# Or using organization
https://github.com/your-company/pm-api.git
https://github.com/your-company/pm-web.git
```

---

## Git Repository Setup

### Step 1: Backend Repository (Already Exists)

```bash
cd /home/aladhimainwin/PHP/project-management-app

# Check current status
git status
git remote -v

# If not initialized
git init
git add .
git commit -m "feat: Laravel API with DDD architecture"

# Add remote (when ready)
git remote add origin https://github.com/yourusername/project-management-api.git
git branch -M main
git push -u origin main
```

### Step 2: Frontend Repository (New)

```bash
cd /home/aladhimainwin/PHP

# Create Vue.js project
npm create vue@latest project-management-frontend

# Navigate to project
cd project-management-frontend

# Initialize Git
git init
git add .
git commit -m "feat: Initial Vue.js SPA setup with DDD architecture"

# Add remote (when ready)
git remote add origin https://github.com/yourusername/project-management-frontend.git
git branch -M main
git push -u origin main
```

### Step 3: Create .gitignore for Each

**Backend (.gitignore):**
```gitignore
/vendor
/node_modules
.env
.env.backup
.phpunit.result.cache
storage/*.key
.DS_Store
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log
```

**Frontend (.gitignore):**
```gitignore
# Dependencies
node_modules
.pnp
.pnp.js

# Testing
coverage

# Production
dist
dist-ssr
*.local

# Editor
.vscode/*
!.vscode/extensions.json
.idea
*.swp
*.swo
*~

# OS
.DS_Store
Thumbs.db

# Env
.env
.env.local
.env.*.local

# Logs
logs
*.log
npm-debug.log*
yarn-debug.log*
yarn-error.log*
pnpm-debug.log*
lerna-debug.log*
```

---

## Deployment Strategies

### Strategy 1: Separate Hosting (Recommended)

```bash
# Backend (API)
Platform: AWS, DigitalOcean, Railway, Render
URL: https://api.yourapp.com
Cost: $5-20/month (starts small)

# Frontend (SPA)
Platform: Vercel, Netlify, CloudFlare Pages
URL: https://yourapp.com or https://app.yourapp.com
Cost: FREE (for most projects)
```

**Configuration:**

**Frontend .env:**
```env
VITE_API_BASE_URL=https://api.yourapp.com/api
```

**Backend .env:**
```env
FRONTEND_URL=https://yourapp.com
SANCTUM_STATEFUL_DOMAINS=yourapp.com
SESSION_DOMAIN=.yourapp.com
```

### Strategy 2: Single Server (Development/Small Projects)

```bash
# Single server running both
Server: DigitalOcean Droplet, AWS EC2
URL Backend: https://yourapp.com/api
URL Frontend: https://yourapp.com

# Nginx configuration serves both
```

**Nginx Config:**
```nginx
server {
    listen 80;
    server_name yourapp.com;

    # Frontend (Vue.js SPA)
    location / {
        root /var/www/frontend/dist;
        try_files $uri $uri/ /index.html;
    }

    # Backend (Laravel API)
    location /api {
        proxy_pass http://localhost:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

### Strategy 3: Docker Compose (Local Development)

```yaml
# docker-compose.yml (in parent directory)
version: '3.8'

services:
  backend:
    build: ./project-management-app
    ports:
      - "8000:8000"
    volumes:
      - ./project-management-app:/var/www/html
    environment:
      - APP_ENV=local

  frontend:
    build: ./project-management-frontend
    ports:
      - "5173:5173"
    volumes:
      - ./project-management-frontend:/app
    environment:
      - VITE_API_BASE_URL=http://localhost:8000/api

  database:
    image: mysql:8.0
    ports:
      - "3306:3306"
    environment:
      MYSQL_DATABASE: project_management
      MYSQL_ROOT_PASSWORD: secret
```

---

## Decision Tree

Use this to decide which approach fits your needs:

```
START: Do you need to scale frontend and backend independently?
├─ YES → Are you building a commercial SaaS product?
│  ├─ YES → Use Separate Repositories (Approach 1) ✅
│  └─ NO → Do you have a dedicated frontend team?
│     ├─ YES → Use Separate Repositories (Approach 1) ✅
│     └─ NO → Use Monorepo (Approach 2)
│
└─ NO → Is this an internal tool or MVP?
   ├─ YES → Will you need mobile apps in the future?
   │  ├─ YES → Use Separate Repositories (Approach 1) ✅
   │  └─ NO → Use Laravel-Integrated (Approach 3)
   │
   └─ NO → Is frontend secondary to backend?
      ├─ YES → Use Laravel-Integrated (Approach 3)
      └─ NO → Use Separate Repositories (Approach 1) ✅
```

**For your project:** You answered YES to "commercial SaaS" → **Separate Repositories** ✅

---

## Migration Paths

### If You Start with Approach 3 → Want to Move to Approach 1

```bash
# 1. Create new Vue.js project
npm create vue@latest project-management-frontend

# 2. Copy source files
cp -r project-management-app/resources/js/* project-management-frontend/src/

# 3. Update imports and paths
# 4. Configure API client with base URL
# 5. Test thoroughly
# 6. Deploy frontend to CDN
# 7. Remove resources/js from Laravel (keep API only)
```

### If You Start with Approach 1 → Want to Move to Approach 2

```bash
# 1. Create monorepo directory
mkdir project-management-monorepo
cd project-management-monorepo

# 2. Move repositories
git clone <backend-repo> backend
git clone <frontend-repo> frontend

# 3. Initialize new repo
git init
git add .
git commit -m "feat: Merge into monorepo"

# 4. Optional: Add workspace tools (Nx, Turborepo)
```

---

## Best Practices Summary

### ✅ DO (For Separate Repositories)

1. **Use consistent naming:**
   - `project-name-api` or `project-name-backend`
   - `project-name-web` or `project-name-frontend`

2. **Document API contract:**
   - OpenAPI/Swagger documentation
   - Shared in both repositories (link or file)

3. **Version API endpoints:**
   - `/api/v1/projects`
   - Allows frontend to migrate gradually

4. **Environment variables:**
   - Backend: `FRONTEND_URL`
   - Frontend: `VITE_API_BASE_URL`

5. **CORS configuration:**
   - Whitelist only your frontend domain
   - Use Laravel Sanctum for SPA authentication

6. **Independent CI/CD:**
   - Frontend: Auto-deploy on merge
   - Backend: Deploy with testing gates

### ❌ DON'T

1. **Don't hardcode URLs:**
   - Use environment variables everywhere

2. **Don't skip CORS setup:**
   - Will break in production

3. **Don't mix concerns:**
   - Keep API purely API (no blade views)
   - Keep SPA purely SPA (no inline PHP)

4. **Don't ignore versioning:**
   - Tag releases: `v1.0.0`, `v1.1.0`
   - Keep changelog

---

## Recommended Setup Commands

```bash
# 1. Ensure backend is ready
cd /home/aladhimainwin/PHP/project-management-app
php artisan serve  # Test API

# 2. Create frontend repository
cd /home/aladhimainwin/PHP
npm create vue@latest project-management-frontend

# 3. Follow prompts (see vue.md for details)

# 4. Initialize Git for frontend
cd project-management-frontend
git init
git add .
git commit -m "feat: Initial commit - Vue.js SPA with DDD"

# 5. Test integration
npm run dev  # Frontend at localhost:5173
# Backend at localhost:8000

# 6. Test API connection
curl http://localhost:5173
curl http://localhost:8000/api/health
```

---

## Conclusion

**For your Project Management & Team Collaboration Application:**

✅ **Use Approach 1: Separate Repositories**

**Reasons:**
1. You have a robust DDD-architected Laravel API
2. You want modern, scalable SPA architecture
3. You'll deploy frontend to CDN (fast, global)
4. You want team flexibility
5. Industry best practice for 2024

**Next Steps:**
1. Keep your Laravel API repository as-is
2. Create new Vue.js repository (follow vue.md)
3. Set up CORS in Laravel
4. Configure API client in Vue.js
5. Deploy frontend to Vercel/Netlify
6. Deploy backend to your preferred host

---

**END OF DOCUMENT**

Follow the updated `vue.md` for detailed DDD architecture implementation in your Vue.js SPA frontend.
