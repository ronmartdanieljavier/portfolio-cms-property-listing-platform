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

## API Endpoints

### Auth

| Method   | Endpoint             | Description                             | Auth    |
| -------- | -------------------- | --------------------------------------- | ------- |
| `POST`   | `/api/auth/register` | Register a new agent account            | —       |
| `POST`   | `/api/auth/login`    | Authenticate and receive a bearer token | —       |
| `DELETE` | `/api/auth/logout`   | Revoke the current access token         | Sanctum |

### Admin

| Method   | Endpoint                                | Description                             | Auth  |
| -------- | --------------------------------------- | --------------------------------------- | ----- |
| `DELETE` | `/api/admin/users/{user}/force-logout`  | Revoke all tokens for a user            | Admin |
| `PATCH`  | `/api/admin/users/{user}/toggle-status` | Activate or deactivate an agent account | Admin |

### Properties

| Method          | Endpoint               | Description                         | Auth    |
| --------------- | ---------------------- | ----------------------------------- | ------- |
| `GET`           | `/api/properties`      | List all properties (paginated)     | Sanctum |
| `GET`           | `/api/properties/{id}` | Get a single property by ID         | Sanctum |
| `POST`          | `/api/properties`      | Create a new property listing       | Sanctum |
| `PUT` / `PATCH` | `/api/properties/{id}` | Update a property (owner only)      | Sanctum |
| `DELETE`        | `/api/properties/{id}` | Soft-delete a property (owner only) | Sanctum |

> Request bodies use camelCase keys: `propertyType`, `floorArea`, `lotArea`, `zipCode`. `propertyType` must be one of `house`, `apartment`, `condo`, `townhouse`, `land`, `commercial`. `status` must be one of `for_sale`, `for_rent`, `sold`, `rented`; defaults to `for_sale` on creation. List endpoint returns 15 results per page. List and detail responses include an `amenities` array.

### Property Amenities

| Method          | Endpoint                                     | Description                            | Auth    |
| --------------- | -------------------------------------------- | -------------------------------------- | ------- |
| `POST`          | `/api/properties/{id}/amenities`             | Attach amenities (keeps existing)      | Sanctum |
| `PUT` / `PATCH` | `/api/properties/{id}/amenities`             | Sync amenities (replaces all existing) | Sanctum |
| `DELETE`        | `/api/properties/{id}/amenities/{amenityId}` | Detach a single amenity                | Sanctum |

> All amenity endpoints require the authenticated agent to own the property. `amenityIds` must be an array of valid amenity IDs. `POST` returns `201` with the updated amenity list; `PUT`/`PATCH` returns `200`; `DELETE` returns `204 No Content`.

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
