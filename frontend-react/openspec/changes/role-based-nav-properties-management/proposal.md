## Why

After login, the dashboard has no navigation or functional pages — users see only a welcome message. We need role-based navigation with an Agent Users page (admin-only) and a Properties page (all authenticated users) so the CMS is actually usable.

## What Changes

- Add a sidebar/top navigation to the dashboard layout with role-aware menu items
- Add an **Agent Users** page (admin-only) that lists users, with toggle-status and force-logout actions via existing backend endpoints
- Add a **Properties** page (admin + agent) that lists all properties with full CRUD — create, update, delete a property, and manage amenities per property (add, update, delete)
- Introduce role-based route guards so `/agent-users` redirects non-admins back to the dashboard
- Add API service modules for Users (admin) and Properties + Amenities

## Capabilities

### New Capabilities

- `navigation`: Role-aware sidebar/nav rendered inside the authenticated layout, showing "Agent Users" only to admins and "Properties" to all users
- `agent-users-management`: Admin-only page to list agent users and perform toggle-status / force-logout actions via `PATCH /admin/users/{user}/toggle-status` and `DELETE /admin/users/{user}/force-logout`
- `properties-management`: Page available to all authenticated users to list, create, update, and delete properties via `GET|POST|PUT|DELETE /properties`, and manage amenities per property via `POST|PUT|DELETE /properties/{id}/amenities`

### Modified Capabilities

- `user-auth`: ProtectedRoute must now also support an admin-only guard variant for the agent-users route

## Impact

- `src/App.tsx` — new routes `/agent-users` and `/properties`, updated `ProtectedRoute` with admin guard
- `src/pages/Dashboard.tsx` — refactored into a layout shell that renders navigation + child pages
- New pages: `src/pages/AgentUsers/`, `src/pages/Properties/`
- New API services: `src/services/usersApi.ts`, `src/services/propertiesApi.ts`, `src/services/amenitiesApi.ts`
- New types: `src/types/user.ts` (admin user list), `src/types/property.ts`, `src/types/amenity.ts`
- Backend endpoints consumed (no backend changes needed)
