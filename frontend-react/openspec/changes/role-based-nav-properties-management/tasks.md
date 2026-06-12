## 1. Types & API Services

- [x] 1.1 Add `Property` and `Amenity` types to `src/types/property.ts`
- [x] 1.2 Create `src/services/propertiesApi.ts` with `getProperties`, `createProperty`, `updateProperty`, `deleteProperty`
- [x] 1.3 Create `src/services/amenitiesApi.ts` with `addAmenity`, `updateAmenities`, `deleteAmenity`
- [x] 1.4 Create `src/services/usersApi.ts` with `getUsers`, `toggleUserStatus`, `forceLogoutUser`

## 2. Layout & Navigation

- [x] 2.1 Refactor `src/pages/Dashboard.tsx` into `src/layouts/AuthLayout.tsx` with sidebar nav and `<Outlet />`
- [x] 2.2 Implement role-aware sidebar — show "Agent Users" only for `admin`, "Properties" for all
- [x] 2.3 Add active-link highlighting using `NavLink` from react-router-dom

## 3. Route Guards

- [x] 3.1 Create `AdminRoute` component in `src/components/AdminRoute.tsx` that redirects non-admins to `/dashboard`
- [x] 3.2 Update `src/App.tsx` — nest `/dashboard`, `/agent-users`, `/properties` as children of `AuthLayout`
- [x] 3.3 Wrap `/agent-users` with `AdminRoute`

## 4. Agent Users Page

- [x] 4.1 Create `src/pages/AgentUsers/AgentUsers.tsx` that fetches and lists users on mount
- [x] 4.2 Add toggle-status button per user row — calls `PATCH /admin/users/{user}/toggle-status` and updates UI
- [x] 4.3 Add force-logout button per user row with confirmation — calls `DELETE /admin/users/{user}/force-logout`

## 5. Properties Page

- [x] 5.1 Create `src/pages/Properties/Properties.tsx` that fetches and lists properties on mount
- [x] 5.2 Add "Create Property" form/modal — calls `POST /properties` and prepends result to list
- [x] 5.3 Add "Edit Property" inline form or modal — calls `PUT /properties/{id}` and updates list
- [x] 5.4 Add "Delete Property" button with confirmation — calls `DELETE /properties/{id}` and removes from list

## 6. Amenity Management

- [x] 6.1 Add amenity section to each property row/detail — displays current amenities
- [x] 6.2 Add "Add Amenity" form — calls `POST /properties/{propertyId}/amenities` and refreshes amenity list
- [x] 6.3 Add "Update Amenities" action — calls `PUT /properties/{propertyId}/amenities`
- [x] 6.4 Add "Delete Amenity" button per amenity with confirmation — calls `DELETE /properties/{propertyId}/amenities/{amenityId}`
