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
- [Running the Application](#running-the-application)
- [API Structure](#api-structure)
- [Authentication](#authentication)
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

The frontend (Vue.js) lives in the `/frontend` directory at the root of this monorepo and communicates with this API exclusively via HTTP.

---

## Tech Stack

| Layer           | Technology                    |
| --------------- | ----------------------------- |
| Framework       | Laravel 13                    |
| Language        | PHP 8.3+                      |
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

- PHP `>= 8.3`
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

The API will be available at: `http://localhost:8000/api/v1`

---

## API Structure

All API routes are versioned and prefixed with `/api/v1`.

| Method   | Endpoint                        | Description                     |
| -------- | ------------------------------- | ------------------------------- |
| `GET`    | `/api/v1/properties`            | List all properties (paginated) |
| `GET`    | `/api/v1/properties/{id}`       | Get a single property           |
| `POST`   | `/api/v1/properties`            | Create a new property           |
| `PUT`    | `/api/v1/properties/{id}`       | Update a property               |
| `DELETE` | `/api/v1/properties/{id}`       | Delete a property               |
| `POST`   | `/api/v1/properties/{id}/media` | Upload media for a property     |
| `GET`    | `/api/v1/auth/user`             | Get authenticated user          |
| `POST`   | `/api/v1/auth/login`            | Login                           |
| `POST`   | `/api/v1/auth/logout`           | Logout                          |

> Full API documentation is available via Postman collection or through `/api/documentation` when `APP_ENV=local`.

---

## Authentication

This API uses **Laravel Sanctum** for token-based authentication. To access protected endpoints:

1. Obtain a token via `POST /api/v1/auth/login`
2. Pass the token in the `Authorization` header:

```
Authorization: Bearer <your-token>
```

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

Tests are located in the `/tests` directory, organised into `Unit` and `Feature` suites.

---

## Project Structure

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/V1/   # Versioned API controllers
│   │   ├── Middleware/            # Auth, throttle, CORS
│   │   └── Requests/              # Form request validation
│   ├── Models/                    # Eloquent models
│   ├── Services/                  # Business logic layer
│   └── Repositories/             # Data access layer
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php                    # API route definitions
├── tests/
│   ├── Feature/
│   └── Unit/
└── .env.example
```

---

## Related

- **Frontend (Vue.js):** [`/frontend`](../frontend)
- **Root README:** [`/README.md`](../README.md)

---

## Author

**Ron Mart Daniel Javier**  
[GitHub](https://github.com/ronmartdanieljavier)
