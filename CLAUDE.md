# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Name**: Project Management & Team Collaboration Application
**Architecture**: Domain-Driven Design (DDD) + Clean Architecture
**Framework**: Laravel 11.x (planned, not yet installed)
**Status**: Planning/Pre-development Phase

This is a Laravel-based project management and team collaboration application following DDD and Clean Architecture principles. The codebase currently contains architectural planning documentation but no implementation.

## Current State

The repository contains planning documentation only:
- `Task.md`: Comprehensive development task tracker with all planned features
- `Authenticate.md`: Authentication strategy and architecture documentation
- `bug_registry/`: Bug tracking system (empty)

**No Laravel installation exists yet.** The first development step is installing Laravel 11.x.

## Architecture Principles

### Domain-Driven Design Layers

**Domain Layer** (Core business logic):
- Entities: Project, Task, User, Team, Milestone, TimeEntry
- Value Objects: Email, ProjectStatus, TaskPriority, Timestamp, Role
- Domain Services: ProjectCollaborationService, TimeTrackingService, PermissionService
- Domain Events: TaskCreated, ProjectUpdated, TaskAssigned, ProjectCompleted

**Application Layer** (Use cases):
- Commands & Command Handlers for CQRS pattern
- DTOs for data transfer between layers
- Application Services orchestrating domain operations

**Infrastructure Layer** (Technical implementation):
- Eloquent repositories implementing domain repository interfaces
- HTTP layer (controllers, requests, resources)
- Event listeners
- External service integrations

### Clean Architecture Rules
- Dependency rule: Inner circles (domain) don't depend on outer circles (infrastructure)
- Domain layer defines interfaces; infrastructure implements them
- Business logic isolated from framework details

## Planned Technology Stack

- **Backend**: Laravel 11.x
- **Database**: MySQL 8.x or PostgreSQL 15.x
- **Cache/Queue**: Redis + Laravel Horizon
- **Authentication**: Laravel Sanctum (API tokens)
- **Real-time**: Laravel Echo Server with Pusher/Ably
- **Frontend**: Vue.js 3.x or React 18.x (SPA)
- **Testing**: PHPUnit, Pest, Laravel Dusk
- **Deployment**: Docker

## User Roles & Permissions

- **Super Admin**: Full system access
- **Project Manager**: Manage projects, assign tasks, manage teams
- **Team Lead**: Manage assigned teams and projects
- **Team Member**: Access assigned tasks and projects
- **Client/Viewer**: Read-only access to specific projects

## Development Setup (When Ready)

Since Laravel is not yet installed, standard Laravel setup will apply:

```bash
# After Laravel installation
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

## Coding Standards

- PSR-12 coding standards
- Laravel best practices
- SOLID principles
- Repository pattern for data access (implementing domain interfaces)
- CQRS pattern for complex operations

## Bug Tracking

Use the bug registry system in `bug_registry/`:
- File naming: `BUG-YYYYMMDD-XXX.md`
- Follow template in `bug_registry/README.md`
- Priority levels: Critical, High, Medium, Low

## Key Features to Implement

Per `Task.md`, core features include:
- Project and task management with dependencies
- Team collaboration with real-time updates
- Time tracking functionality
- Role-based access control (RBAC)
- Real-time notifications and chat
- File attachments
- Gantt chart visualization
- Reporting and analytics
- Third-party integrations (Slack, GitHub, etc.)
