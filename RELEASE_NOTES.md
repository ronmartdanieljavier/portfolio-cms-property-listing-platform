# Release Notes

## [Unreleased] — feature/manage-users

### Added

#### Admin User Management (`/api/admin/users`)

- `GET /api/admin/users` — list all users; requires `admin` role
- `POST /api/admin/users` — register a new user with `name`, `email`, `password`, `password_confirmation`, and optional `role` (`agent` default); does not issue a session token to the created user; requires `admin` role
- `PATCH /api/admin/users/{user}` — update a non-admin user's `name`, `email`, `role`, and/or `password`; returns the updated user model; blocked for admin targets (403); requires `admin` role
- `DELETE /api/admin/users/{user}` — permanently delete a non-admin user and revoke all their tokens; blocked for admin targets (403); requires `admin` role
- `DELETE /api/admin/users/{user}/force-logout` — now explicitly blocked for admin targets (403); previously had no admin guard

#### Profile Endpoints (`/api/profile`)

- `GET /api/profile` — return the authenticated user's own profile; requires Sanctum bearer token
- `PATCH /api/profile` — update own `name`, `email`, and/or `password`; password change is optional (omit field to keep current); returns the updated user model; requires Sanctum bearer token

#### Agent Users Page (`frontend-react/src/pages/AgentUsers/AgentUsers.tsx`)

- **Register User** — "Register User" button opens a modal form (name, email, role, password, confirm password); new user is appended to the table on success; API validation errors shown inline per field
- **Edit User** — "Edit" button per non-admin row opens a pre-filled modal (name, email, role, optional password change); confirm password field appears only when a new password is typed; table row updates in place on save
- **Delete User** — "Delete" button per non-admin row with confirmation prompt; row is removed from the table on success
- All three action buttons (Edit, Activate/Deactivate, Force logout, Delete) are hidden for admin-role rows

#### Profile Page (`frontend-react/src/pages/Profile/Profile.tsx`)

- New page at `/profile` available to all authenticated users
- Pre-filled form with the current user's name and email (read from `localStorage`)
- Optional password change: confirm field appears only when a new password is typed
- Updates `localStorage` with the returned user on success so the sidebar name/role reflect changes immediately

#### Navigation (`frontend-react/src/layouts/AuthLayout.tsx`)

- **Profile** button added to the bottom sidebar section alongside Sign out; navigates to `/profile`
- **Properties page** — "Add property", Edit, Delete, and Amenities toggle buttons are hidden when the logged-in user has the `admin` role
- **AmenityManager** — added `readOnly` prop; when `true`, the amenity dropdown, Add button, Sync button, and `×` remove buttons are all hidden

#### Service Layer (`frontend-react/src/services/`)

- `usersApi.ts` — added `registerUser()` (`POST /admin/users`), `updateUser()` (`PATCH /admin/users/{id}`), `deleteUser()` (`DELETE /admin/users/{id}`)
- `profileApi.ts` — new file; `updateProfile()` (`PATCH /profile`)

#### Routing (`frontend-react/src/App.tsx`)

- `/profile` route added, available to all authenticated users inside `AuthLayout`

---

## [Unreleased] — frontend-react-manage-property

### Added

#### Role-Based Navigation Layout (`frontend-react/src/layouts/AuthLayout.tsx`)

- `AuthLayout` — persistent sidebar layout wrapping all authenticated routes; shows the signed-in user's name and role, a role-aware nav (Agent Users visible to admins only, Properties visible to all), and a Sign out button with loading state
- `AdminRoute` — route guard component that redirects non-admin users to `/dashboard`

#### Properties Management (`frontend-react/src/pages/Properties/`)

- `Properties` page — full CRUD UI for property listings; card grid with property details (type, status, price, address, city, state); inline create, edit, and delete actions with confirmation; error and loading states throughout
- `PropertyForm` — shared create/edit form covering all property fields: title, price, type, status, bedrooms, bathrooms, address, city, state (AU state dropdown), postcode, and description
- `AmenityManager` — inline amenity picker on each property card; loads all available amenities and syncs the selection to the property via the amenities sync endpoint

#### Agent Users Management (`frontend-react/src/pages/AgentUsers/AgentUsers.tsx`)

- `AgentUsers` page — admin-only table listing all agent accounts; supports toggle active/inactive status and force-logout (revokes all sessions) per user; toast notifications for action outcomes

#### Service Layer (`frontend-react/src/services/`)

- `propertiesApi.ts` — typed wrappers for `GET /api/properties`, `POST /api/properties`, `PUT /api/properties/{id}`, `DELETE /api/properties/{id}`
- `amenitiesApi.ts` — typed wrappers for `GET /api/amenities`, `POST /api/properties/{id}/amenities`, `PUT /api/properties/{id}/amenities`, `DELETE /api/properties/{id}/amenities/{amenityId}`
- `usersApi.ts` — typed wrappers for `GET /api/admin/users`, `PATCH /api/admin/users/{id}/toggle-status`, `DELETE /api/admin/users/{id}/force-logout`

#### Routing (`frontend-react/src/App.tsx`)

- Authenticated routes now render inside `AuthLayout` via a nested `<Route>` layout pattern
- `/dashboard` — welcome page (no sidebar header; moved to layout)
- `/agent-users` — admin-only; guarded by `AdminRoute`
- `/properties` — available to all authenticated users

#### TypeScript Types (`frontend-react/src/types/property.ts`)

- `Property` interface updated: `province` renamed to `state`; `zipCode` renamed to `postcode`
- `CreatePropertyForm` updated to match: `state` (required), `postcode` (optional)

### Changed

#### Dashboard (`frontend-react/src/pages/Dashboard.tsx`)

- Removed standalone header and sign-out button; those are now provided by `AuthLayout`

### Backend

#### Database Migration

- `2026_06_12_090417` — renames `province` → `state` and `zip_code` → `postcode` on the `properties` table

#### API Contract

- All property endpoints: request field `province` renamed to `state`; `zipCode` renamed to `postcode`
- `CreatePropertyRequest` and `UpdatePropertyRequest` updated to validate `state` and `postcode`
- `PropertyModel`, `PropertyRepositoryData`, `PropertyCoreData`, `PropertyModelFactory` updated to reflect the new column names

#### Postman

- Postman collection updated — `Create Property` request body updated: `province` → `state`, `zipCode` → `postcode`; validation error response example updated accordingly

---

## [Unreleased] — frontend-login

### Added

#### Authentication Pages (`frontend-react/src/pages/Auth/`)

- `Login` page — email and password form; displays field-level validation errors from the API and a general error banner for non-422 failures; shows loading state while the request is in flight
- `Register` page — name, email, password, and password confirmation form mirroring the backend `RegisterRequest` validation rules; same error display and loading behaviour as Login

#### Dashboard Page (`frontend-react/src/pages/Dashboard.tsx`)

- Protected route that requires a valid Sanctum token in `localStorage`
- Header bar shows the authenticated user's name and role
- **Sign out** button calls `DELETE /api/auth/logout`, clears `localStorage`, and redirects to `/login`; logout always succeeds locally even if the API call fails

#### Routing (`frontend-react/src/App.tsx`)

- `BrowserRouter` routes: `/login`, `/register`, `/dashboard`
- Root `/` redirects to `/login`
- `ProtectedRoute` wrapper redirects unauthenticated users to `/login`

#### Auth Hooks (`frontend-react/src/hooks/useAuth.ts`)

- `useLogin` — calls `POST /api/auth/login`, stores `token` and `user` in `localStorage`, navigates to `/dashboard`; surfaces 422 field errors and general API errors
- `useRegister` — calls `POST /api/auth/register` with the same error-handling pattern
- `useLogout` — calls `DELETE /api/auth/logout` with the Bearer token; clears `localStorage` and redirects to `/login` regardless of API outcome

#### Shared Component

- `InputField` — reusable Tailwind-styled input with accessible label, inline field error, disabled state, and error border styling

#### API Client (`frontend-react/src/lib/axios.ts`)

- Axios instance with `VITE_API_URL` base URL (defaults to `http://api.localhost/api`)
- Request interceptor automatically attaches the `Authorization: Bearer <token>` header when a token is present in `localStorage`

#### TypeScript Types (`frontend-react/src/types/auth.ts`)

- `User`, `AuthResponse`, `LoginForm`, `RegisterForm`, `ValidationErrors`, `ApiError` — typed to match backend `UserModel`, `UserLoginCoreData`, and `UserRegisteredCoreData`

#### Unit Tests

- Test runner: **Vitest** with **React Testing Library** and **@testing-library/jest-dom**
- 31 tests across 5 suites:
  - `InputField` — renders, error styles, disabled state, onChange forwarding
  - `useLogin` — success path, 422 field errors, non-422 general errors, processing state transitions
  - `useRegister` — success path, 422 field errors
  - `useLogout` — success path, API failure still clears session
  - `Login` page — renders, submit, error banner, field errors, loading state
  - `Register` page — renders all four fields, submit, error banner, field errors, loading state
  - `Dashboard` page — renders user info, sign out button, logout call, loading state
- New scripts: `npm test`, `npm run test:watch`, `npm run test:coverage`

#### CI

- Added `Test` step (`npm test`) to the existing `frontend` GitHub Actions job, running between lint and build

---

## [Unreleased] — backend-property-amenities

### Added

#### Amenity Endpoints

- `GET /api/amenities` — list all amenities ordered alphabetically; returns a flat array of `{ id, name, createdAt, updatedAt }`; requires Sanctum bearer token

#### Property Amenity Endpoints

- `POST /api/properties/{id}/amenities` — attach one or more amenities to a property without removing existing ones; accepts `amenityIds` array; returns `201` with the full updated amenity list; only the owning agent may call this
- `PUT /api/properties/{id}/amenities` / `PATCH /api/properties/{id}/amenities` — sync all amenities on a property, replacing existing ones; pass an empty array to clear all; returns `200` with the resulting amenity list
- `DELETE /api/properties/{id}/amenities/{amenityId}` — detach a single amenity from a property; returns `204 No Content`; returns `404` if the amenity is not attached

#### Property Listing Response Updated

- `GET /api/properties` and `GET /api/properties/{id}` — responses now include an `amenities` array (each item: `id`, `name`, `createdAt`, `updatedAt`); empty array when no amenities are attached
- All other property write endpoints (`POST`, `PUT`/`PATCH`) also return `amenities` in the response

#### Property Module (`app/Modules/Properties/`)

- `AmenityModel` — Eloquent model for the `amenities` table with `HasFactory`
- `AmenityModelFactory` — factory generating unique amenity names for tests
- `PropertyModel` — added `amenities()` `BelongsToMany` relationship via `amenity_property` pivot
- `AmenityController` — `index` action returning all amenities ordered alphabetically
- `PropertyAmenityRepository` — `attach`, `sync`, `detach` methods accept `PropertyModel` directly (no internal re-fetch)
- `PropertyAmenityService` — thin service layer delegating to `PropertyAmenityRepository`; methods accept `PropertyModel`
- `PropertyAmenityController` — `store`, `update`, `destroy` actions; uses `PropertyService::showModel` for a single property lookup that covers both existence check and ownership check
- `AddPropertyAmenitiesRequest` — validates `amenityIds` as a non-empty array of existing IDs
- `SyncPropertyAmenitiesRequest` — validates `amenityIds` as a present array (empty allowed) of existing IDs
- `AmenityRepositoryData` — Spatie Data object for amenity responses with `MapInputName` snake_case mapping and `#[DataCollectionOf]` attribute
- `PropertyRepositoryData` — added `amenities` field typed with `#[DataCollectionOf(AmenityRepositoryData::class)]`
- `PropertyRepository` — added `findModel` returning the raw `PropertyModel` without relations; `findAll`, `findById`, `create`, and `update` now eager-load `amenities`
- `PropertyService` — added `showModel` returning `?PropertyModel` for callers that need the Eloquent instance
- `PropertyModel` — added `amenities()` `BelongsToMany` relationship with correct `@return BelongsToMany<AmenityModel, $this, Pivot>` PHPDoc for PHPStan
- `api_amenity.php` — new route file registering `GET /api/amenities`
- `api_property.php` — registered three amenity management routes under `/{propertyId}/amenities`

#### Seeder

- `AmenitySeeder` — seeds 20 common amenities (Swimming Pool, Gym, Parking, Garden, Balcony, etc.) using `updateOrCreate` so it is safe to re-run; registered in `DatabaseSeeder` and runnable standalone via `php artisan db:seed --class=AmenitySeeder`

#### Tests

- `AmenityApiTest` — 3 feature tests covering: alphabetical ordering, empty list, and unauthenticated rejection
- `PropertyAmenityApiTest` — 17 feature tests covering: attach (including no-duplicate guard), sync (including empty-array clear), detach, ownership enforcement (403), 404 for non-existent property and un-attached amenity, authentication requirements, and validation errors

#### Postman

- Postman collection updated — added `Amenities` folder with `List Amenities` request; added `Property Amenities` folder with `Add Amenities to Property`, `Sync Amenities on Property`, and `Remove Amenity from Property` requests with example success and error responses
- `List Properties`, `Get Property`, `Create Property`, and `Update Property` example responses updated to include the `amenities` field

---

## [Unreleased] — backend-manage-property

### Added

#### Property Management Endpoints

- `GET /api/properties` — list all property listings paginated (15 per page, newest first); requires Sanctum bearer token
- `GET /api/properties/{id}` — retrieve a single property by ID; returns `404` if not found
- `PUT /api/properties/{id}` / `PATCH /api/properties/{id}` — partially update a property listing; only the owning agent may update; returns `403` for non-owners and `404` if not found
- `DELETE /api/properties/{id}` — soft-delete a property listing; only the owning agent may delete; returns `204 No Content` on success, `403` for non-owners, and `404` if not found

#### Property Module Updates (`app/Modules/Properties/`)

- `UpdatePropertyRequest` — validates partial update payload; all fields are `sometimes` so the body can contain any subset of property fields
- `PropertyRepository` — added `findAll` (paginated), `findById`, `update`, and `delete` methods
- `PropertyService` — added `list`, `show`, `update`, and `delete` methods delegating to the repository
- `PropertyController` — added `index`, `show`, `update`, and `destroy` actions with ownership enforcement on write operations
- `api_property.php` — registered all five CRUD routes

#### Data Transfer Objects

- `PropertyCoreData` — all fields are now nullable to support partial update payloads alongside full create payloads
- `PropertyRepositoryData` — all fields are now nullable; added `id` field; added `toDBUpdate()` helper that returns only non-null fields for partial DB updates; `toDBCreate()` retains `for_sale` / `AU` defaults

#### Tests

- `PropertyApiTest` — added 14 new feature tests covering: paginated listing, single property retrieval, partial updates, ownership enforcement (403), and soft-delete with database assertion; total test count raised from 5 to 19

#### Postman

- Postman collection updated — added `List Properties`, `Get Property`, `Update Property`, and `Delete Property` requests to the Properties folder with example success and error responses

---

## [Unreleased] — backend-create-property

### Added

#### Property Listings (`POST /api/properties`)

- `POST /api/properties` — create a new property listing; requires a Sanctum bearer token; `agent_id` is resolved automatically from the authenticated user and is not accepted from the request body

#### Property Module Architecture (`app/Modules/Properties/`)

Follows the same module structure as `app/Modules/Users/`:

- `PropertyTypeEnum` — backed string enum: `House`, `Apartment`, `Condo`, `Townhouse`, `Land`, `Commercial`
- `PropertyStatusEnum` — backed string enum: `ForSale`, `ForRent`, `Sold`, `Rented`; defaults to `ForSale` on creation
- `PropertyModel` — Eloquent model with `SoftDeletes`; all listing columns are fillable via PHP attribute
- `PropertyRepository` — DB interaction layer; exposes a `create` method returning `PropertyRepositoryData`
- `PropertyService` — thin service layer delegating to `PropertyRepository`
- `CreatePropertyRequest` — validates the camelCase payload (`propertyType`, `floorArea`, `lotArea`, `zipCode`, etc.)
- `PropertyController` — single `store` action wired to the service
- `api_property.php` — module route file auto-loaded by `routes/api.php`; all property routes are grouped under `auth:sanctum`

#### Data Transfer Objects

- `PropertyCoreData` — controller-layer DTO; accepts camelCase keys from `$request->validated()` plus the resolved `agent_id`
- `PropertyRepositoryData` — repository-layer DTO with `MapInputName` attributes for snake_case DB column mapping and a `toDBCreate()` helper

#### Factory & Tests

- `PropertyModelFactory` — factory with realistic fake data for all property fields; uses `UserModel::factory()` for `agent_id`
- `PropertyApiTest` — 5 feature tests covering: successful creation, default `for_sale` status, unauthenticated rejection, missing required fields, and invalid `propertyType` enum value
- `phpunit.xml` — added `app/Modules/Properties/Tests/Feature` to the Feature test suite

#### Postman

- Postman collection updated — added a `Properties` folder with the `Create Property` request, example success/error responses, and inline field documentation

---

## [Unreleased] — backend-migratation-property

### Added

- Migration `2026_06_12_152545` — creates four tables for property data:
  - `properties` — core listing details: title, description, price, property type, status, physical specs (bedrooms, bathrooms, floor area, lot area, floors), full address with city, province, country (default `AU`), zip code, and GPS coordinates; includes `agent_id` FK to `users` with cascade delete and soft deletes
  - `property_images` — one-to-many images per property with `is_primary` flag and `sort_order`
  - `amenities` — lookup table for amenity names (e.g. pool, gym)
  - `amenity_property` — many-to-many pivot linking properties to amenities

---

## [Unreleased] — register-only-agent

### Changed

- `POST /api/auth/register` — removed `role` from the request payload; all new registrations are now assigned the `agent` role automatically
- `UserCoreData` and `UserRepositoryData` — `role` defaults to `'agent'`; no longer required to be supplied by callers of the register flow

---

## [Unreleased] — backend-login

Authentication and user management foundation for the Backend API.

### Added

#### Authentication (`/api/auth`)

- `POST /api/auth/register` — register a new user with `name`, `email`, and `password`; role is automatically set to `agent`
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
