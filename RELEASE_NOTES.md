# Release Notes

## [Unreleased] — backend-login

Authentication and user management foundation for the Backend API.

### Added

#### Authentication (`/api/auth`)

- `POST /api/auth/register` — register a new user with `name`, `email`, `password`, and `role` (`admin` | `agent`)
- `POST /api/auth/login` — authenticate and receive a Sanctum bearer token; blocked if account is inactive
- `DELETE /api/auth/logout` — revoke the current access token (requires bearer token)

#### Admin User Management (`/api/admin`)

- `DELETE /api/admin/users/{user}/force-logout` — revoke all active tokens for a user across all devices (admin only)
- `PATCH /api/admin/users/{user}/toggle-status` — activate or deactivate an agent account; admin accounts cannot be deactivated (admin only)

#### User Roles & Model

- `UserRoleEnum` — backed string enum with `Admin` and `Agent` cases
- `UserModel` — Eloquent model with `role` (cast to `UserRoleEnum`) and `is_active` columns
- Migration `2026_06_11_230940` — adds `role` (string, default `agent`) and `is_active` (boolean, default `true`) to the `users` table
- `AdminUserSeeder` — seeds an admin account from `ADMIN_EMAIL`, `ADMIN_PASSWORD`, and `ADMIN_NAME` env variables; safe to re-run (uses `updateOrCreate`)

#### Module Architecture

- Introduced a module-based structure under `app/Modules/` — each domain module self-contains its controllers, middleware, requests, models, repositories, services, routes, and tests
- `routes/api.php` auto-discovers and loads all `app/Modules/*/Routes/*.php` files — no manual route registration needed when adding new modules
- `EnsureUserIsAdmin` middleware — registered as the `admin` middleware alias; returns `403 Forbidden` for non-admin requests

#### Data Transfer Objects (Spatie Laravel Data)

- `UserCoreData` — core user payload (name, email, password, role, is_active)
- `UserRegisteredCoreData` — registration response (user + token)
- `UserLoginCoreData` — login response (user + token)
- `UserRepositoryData` — repository-layer DTO

#### Custom Casts (Spatie Laravel Data)

- `TrimmedStringCast` — trims whitespace from string and `BackedEnum` values on data object creation
- `ConvertIsoToDateFormatCast` — converts ISO 8601 / Carbon dates to `d/m/Y` display format

#### Testing

- Feature tests for all auth endpoints (`AuthApiTest`) — register, login, logout, inactive account blocking, duplicate email rejection
- Feature tests for all admin endpoints (`AdminUserApiTest`) — force logout, toggle status, role-based access enforcement
- Unit tests for both custom casts (`TrimmedStringCastTest`, `ConvertIsoToDateFormatCastTest`)

#### Postman

- Postman collection at `postman/PropertyListingPlatform.postman_collection.json` covering all auth and admin endpoints

#### Static Analysis

- Fixed `phpstan.neon` — added Pest PHPStan extension and excluded module `Tests/` directories from analysis (Pest `$this` binding is not statically inferrable)
- Fixed `UserModel` — `@property UserRoleEnum $role` PHPDoc placed before PHP attributes so PHPStan correctly resolves the cast type

---

## [0.4.0] — ci-initial-configuration

### Added

- GitHub Actions CI pipeline with jobs for PHPStan static analysis, Pest tests, and frontend lint/build
- Removed Greptile from CI configuration

---

## [0.3.0] — docker-initial-setup

### Added

- Docker Compose environment with services for the Laravel backend, React frontend, PostgreSQL, Redis, and Nginx
- `Dockerfile` for backend and frontend services
- Nginx configuration for local reverse proxy

---

## [0.2.0] — react-frontend-installation

### Added

- React (Vite + TypeScript) frontend scaffolded at `/frontend-react`

---

## [0.1.0] — backend-initial-installation

### Added

- Laravel 13 backend scaffolded at `/backend` with Sanctum, Pest, Larastan, and Pint
- Commit lint and Husky pre-commit hooks configured at the monorepo root
