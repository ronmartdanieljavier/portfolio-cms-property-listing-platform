## Context

The frontend is a React + TypeScript app (Vite, React Router, Tailwind CSS). Authentication is already implemented — the token and user object (including `role`) are stored in `localStorage`. The current `Dashboard` is a standalone page with a logout button. There are no navigation menus, no role guards beyond token presence, and no feature pages yet. Backend REST endpoints for users (admin), properties, and amenities are all live and protected by Sanctum token auth.

## Goals / Non-Goals

**Goals:**

- Role-aware navigation rendered inside the authenticated layout
- Agent Users page restricted to admins with user management actions
- Properties page for all authenticated users with full property + amenity CRUD
- Reuse the existing `User` type (which already carries `role`) for the auth guard

**Non-Goals:**

- Pagination, search, or filtering (can be added later)
- Real-time updates / websockets
- Any backend changes

## Decisions

**1. Layout shell pattern over per-page navigation**

The `Dashboard` page becomes a persistent layout shell (`AuthLayout`) that renders the sidebar nav and an `<Outlet />`. All authenticated pages are nested routes under this layout. This avoids repeating the header/nav across pages and makes future additions trivial.

Alternatives: shared nav component imported per page — rejected because it duplicates nav state and complicates active-link tracking.

**2. Admin guard as a wrapper component, not middleware**

An `AdminRoute` wrapper reads `user.role` from `localStorage` and redirects to `/dashboard` if not admin. Keeps routing declarative in `App.tsx` without a separate auth context for this change.

Alternatives: React Context / Zustand for auth state — deferred; localStorage reads are sufficient for the current scope and avoid new dependencies.

**3. API service modules (plain async functions, no library)**

Three service files (`usersApi.ts`, `propertiesApi.ts`, `amenitiesApi.ts`) export typed async functions built on `fetch`. Token is read from `localStorage` in each call. No axios or react-query in this change.

Alternatives: axios — unnecessary wrapper for this scope. react-query — useful but out of scope; would be a separate refactor.

**4. Amenities managed inline within the Property detail view**

Rather than a separate `/amenities` route, amenities are managed in a modal or inline panel on the property row/detail. This avoids a deeply nested route structure.

## Risks / Trade-offs

- `localStorage` role check can be spoofed client-side → the backend enforces the `admin` middleware on protected endpoints, so security is not compromised; the frontend guard is UX-only.
- No loading/error state library means each page manages its own fetch state → acceptable for the current page count; add react-query when pages multiply.
- Reading `user` from `localStorage` on every render (no context) → negligible perf impact for 2-3 pages; revisit if the app grows.

## Migration Plan

1. Refactor `Dashboard.tsx` into `AuthLayout` shell with sidebar nav + `<Outlet />`
2. Add routes `/agent-users` (AdminRoute-wrapped) and `/properties` in `App.tsx`
3. Build `AgentUsers` page, `Properties` page, and amenity management UI
4. Wire API service calls
5. No backend migration needed; no breaking changes to existing login/register flow
