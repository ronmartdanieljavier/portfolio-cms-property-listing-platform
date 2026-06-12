# Portfolio CMS — Property Listing Platform

Full-stack property listing and CMS platform built with Laravel, React, PostgreSQL, and Redis — featuring multi-role admin, RESTful API, property listings with images and amenities, enquiry management, and CMS-driven content pages.

## Tech Stack

| Layer         | Technology        |
| ------------- | ----------------- |
| Backend       | Laravel (PHP 8.5) |
| Frontend      | React 19 + Vite   |
| Database      | PostgreSQL 16     |
| Cache / Queue | Redis 7           |
| Web server    | Nginx             |

## Local Development

### Prerequisites

- [Docker](https://www.docker.com/products/docker-desktop) and Docker Compose

### Getting Started

1. Clone the repository:

   ```bash
   git clone <repo-url>
   cd portfolio-cms-property-listing-platform
   ```

2. Copy the backend environment file:

   ```bash
   cp backend/.env.example backend/.env
   ```

3. Start all services:

   ```bash
   docker compose up --build
   ```

   On first boot the backend container will automatically:
   - Install Composer dependencies
   - Generate an `APP_KEY` if one is not set
   - Run database migrations

### Services

| Service                    | URL                   |
| -------------------------- | --------------------- |
| API (Laravel via Nginx)    | http://api.localhost  |
| Frontend (Vite dev server) | http://localhost:5173 |
| PostgreSQL                 | `localhost:5432`      |
| Redis                      | `localhost:6379`      |

> `api.localhost` resolves to `127.0.0.1` automatically on macOS and most Linux distros — no `/etc/hosts` edit required.

### Useful Commands

```bash
# Run artisan commands
docker compose exec backend php artisan <command>

# Run migrations manually
docker compose exec backend php artisan migrate

# Open a shell in the backend container
docker compose exec backend sh

# Tail Laravel logs
docker compose exec backend php artisan pail

# Stop all containers
docker compose down

# Stop and remove volumes (wipes the database)
docker compose down -v
```
