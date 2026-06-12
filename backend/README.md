# 🏠 Portfolio CMS — Property Listing Platform · Backend API

A RESTful API backend powering the Portfolio CMS Property Listing Platform, built with **Laravel 13** and deployed on **AWS**. This service handles property listings, media management, user authentication, and content management operations consumed by the frontend client(s).

---

## Table of Contents

- [Overview](#overview)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Getting Started](#getting-started)
- [Environment Configuration](#environment-configuration)
- [Database Setup](#database-setup)
    - [Seeding the Default Admin Account](#seeding-the-default-admin-account)
    - [Troubleshooting: Database Connection Error](#troubleshooting-database-connection-error)
- [Running the Application](#running-the-application)
- [API Structure](#api-structure)
- [Authentication](#authentication)
- [User Roles](#user-roles)
- [Testing](#testing)
- [AWS Deployment](#aws-deployment)
- [Project Structure](#project-structure)

---

## Overview

This is the **backend service** of the Portfolio CMS Property Listing Platform. It exposes a versioned RESTful API that supports:

- Property listing CRUD (create, read, update, delete)
- Media uploads and management (images, documents)
- Role-based access control (admin, agent, viewer)
- CMS content management for property details and metadata
- Search, filtering, and pagination of property listings

The frontend (React + Vite) lives in the `/frontend-react` directory at the root of this monorepo and communicates with this API exclusively via HTTP.

---

## Tech Stack

| Layer           | Technology                    |
| --------------- | ----------------------------- |
| Framework       | Laravel 13                    |
| Language        | PHP 8.5                       |
| Database        | MySQL 8 / PostgreSQL 16       |
| Cache & Queues  | Redis                         |
| Authentication  | Laravel Sanctum (token-based) |
| File Storage    | AWS S3                        |
| Hosting         | AWS EC2 / Elastic Beanstalk   |
| CI/CD           | GitHub Actions                |
| Package Manager | Composer                      |

---

## Prerequisites

Ensure you have the following installed locally:

- PHP `>= 8.5`
- Composer `>= 2.x`
- MySQL `>= 8.0` or PostgreSQL `>= 16`
- Redis `>= 7.x`
- Node.js `>= 20.x` _(for compiling assets if applicable)_
- AWS CLI _(for S3 or deployment tasks)_

---

## Getting Started

### 1. Clone the repository

```bash
git clone https://github.com/ronmartdanieljavier/portfolio-cms-property-listing-platform.git
cd portfolio-cms-property-listing-platform/backend
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Copy the environment file

```bash
cp .env.example .env
```

### 4. Generate the application key

```bash
php artisan key:generate
```

---

## Environment Configuration

Update your `.env` file with the appropriate values:

```env
APP_NAME="Portfolio CMS API"
APP_ENV=local
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portfolio_cms
DB_USERNAME=root
DB_PASSWORD=

# Cache & Queues
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# AWS S3 (Media Storage)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=ap-southeast-2
AWS_BUCKET=
AWS_URL=

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost:5173
```

---

## Database Setup

Run migrations and (optionally) seed the database with sample data:

```bash
# Run migrations
php artisan migrate

# Run with seeders
php artisan migrate --seed

# Fresh migration with seeders
php artisan migrate:fresh --seed
```

### Seeding the Default Admin Account

Add the following to your `.env` file before running the seeder:

```env
ADMIN_NAME="Your Name"
ADMIN_EMAIL=you@yourdomain.com
ADMIN_PASSWORD=a-strong-secret-password
```

Then run:

```bash
php artisan db:seed --class=AdminUserSeeder
```

The seeder uses `updateOrCreate`, so it is safe to re-run — it will update the existing admin rather than create a duplicate. If `ADMIN_EMAIL` or `ADMIN_PASSWORD` is not set, the seeder will skip with a warning.

### Troubleshooting: Database Connection Error

If you see `could not translate host name "postgres"` when running migrations or seeders, your `.env` `DB_HOST` may be set to `postgres` (a Docker service name) instead of a reachable host.

**Running locally (no Docker):** set `DB_HOST=127.0.0.1` in your `.env`.

**Running with Docker:** the `postgres` hostname is correct, but the container must be running first:

```bash
docker compose up -d
```

Then retry the command.

---

## Running the Application

```bash
# Start the development server
php artisan serve

# Start the queue worker (required for async jobs)
php artisan queue:work

# Start the scheduler (for cron-based tasks)
php artisan schedule:work
```

The API will be available at: `http://localhost:8000/api`

---

## API Structure

All API routes are prefixed with `/api` and defined per module under `app/Modules/*/Routes/`.

### Auth

| Method   | Endpoint             | Auth Required | Description                            |
| -------- | -------------------- | ------------- | -------------------------------------- |
| `POST`   | `/api/auth/register` | No            | Register a new admin or agent user     |
| `POST`   | `/api/auth/login`    | No            | Login (blocked if account is inactive) |
| `DELETE` | `/api/auth/logout`   | Bearer token  | Revoke the current access token        |

### Admin

| Method   | Endpoint                                | Auth Required | Description                     |
| -------- | --------------------------------------- | ------------- | ------------------------------- |
| `DELETE` | `/api/admin/users/{user}/force-logout`  | Admin only    | Revoke all tokens for a user    |
| `PATCH`  | `/api/admin/users/{user}/toggle-status` | Admin only    | Activate or deactivate an agent |

### Properties

| Method          | Endpoint               | Auth Required | Description                                                                        |
| --------------- | ---------------------- | ------------- | ---------------------------------------------------------------------------------- |
| `GET`           | `/api/properties`      | Bearer token  | List all properties (paginated, 15/page, newest first); includes `amenities` array |
| `GET`           | `/api/properties/{id}` | Bearer token  | Get a single property by ID; includes `amenities` array                            |
| `POST`          | `/api/properties`      | Bearer token  | Create a new property listing                                                      |
| `PUT` / `PATCH` | `/api/properties/{id}` | Bearer token  | Partially update a property (owner only)                                           |
| `DELETE`        | `/api/properties/{id}` | Bearer token  | Soft-delete a property (owner only)                                                |

### Property Amenities

| Method          | Endpoint                                     | Auth Required | Description                                                |
| --------------- | -------------------------------------------- | ------------- | ---------------------------------------------------------- |
| `POST`          | `/api/properties/{id}/amenities`             | Bearer token  | Attach amenities to a property (owner only; no duplicates) |
| `PUT` / `PATCH` | `/api/properties/{id}/amenities`             | Bearer token  | Sync/replace all amenities on a property (owner only)      |
| `DELETE`        | `/api/properties/{id}/amenities/{amenityId}` | Bearer token  | Detach a single amenity from a property (owner only)       |

> A Postman collection is available at `/postman/PropertyListingPlatform.postman_collection.json`.

---

## Authentication

This API uses **Laravel Sanctum** for token-based authentication. To access protected endpoints:

1. Obtain a token via `POST /api/v1/auth/login`
2. Pass the token in the `Authorization` header:

```
Authorization: Bearer <your-token>
```

---

## User Roles

There are two user roles: **admin** and **agent**. The `role` field is required on registration.

| Role    | Capabilities                                                                 |
| ------- | ---------------------------------------------------------------------------- |
| `admin` | Full access — can force-logout users and toggle agent account status         |
| `agent` | Standard access — can log in and out; account can be deactivated by an admin |

**Account status rules:**

- Users can only log in if their account `is_active` is `true`
- Only agent accounts can be deactivated/reactivated — admin accounts cannot have their status changed
- Force logout revokes all active tokens across all devices

---

## Testing

```bash
# Run the full test suite
php artisan test

# Run with coverage report
php artisan test --coverage

# Run a specific test file
php artisan test --filter=PropertyListingTest
```

Tests are located in the `/tests` directory and within each module under `app/Modules/*/Tests/`, organised into `Unit` and `Feature` suites.

---

## Project Structure

```
backend/
├── app/
│   ├── Casts/                     # Custom Spatie Laravel Data casts
│   ├── Http/
│   │   └── Controllers/           # Base controller
│   └── Modules/
│       └── Users/
│           ├── Enums/             # UserRoleEnum (admin, agent)
│           ├── Http/
│           │   ├── Controllers/   # Auth and Admin controllers
│           │   ├── Middleware/    # EnsureUserIsAdmin
│           │   └── Requests/      # Form request validation
│           ├── Models/            # UserModel (Eloquent)
│           ├── Repositories/      # UserRepository (data access)
│           ├── Routes/            # Module-scoped API routes
│           ├── Services/          # AuthService (business logic)
│           ├── Tests/             # Feature tests (Pest)
│           └── Transformations/   # Spatie Data objects (DTOs)
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/                   # AdminUserSeeder
├── routes/
│   └── api.php                    # Auto-loads module route files
├── tests/
│   ├── Feature/
│   └── Unit/                      # Cast unit tests
└── .env.example
```

---

## Related

- **Frontend (React):** [`/frontend-react`](../frontend-react)
- **Root README:** [`/README.md`](../README.md)
- **Postman Collection:** [`/postman`](../postman)

---

## Author

**Ron Mart Daniel Javier**  
[GitHub](https://github.com/ronmartdanieljavier)
