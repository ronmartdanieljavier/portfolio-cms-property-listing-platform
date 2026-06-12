## MODIFIED Requirements

### Requirement: Protected route enforces authentication and role

The system SHALL support two guard variants: a general authenticated guard (token present) and an admin-only guard (token present AND `user.role === "admin"`).

#### Scenario: Unauthenticated user is redirected to login

- **WHEN** an unauthenticated user navigates to any protected route
- **THEN** the system SHALL redirect to `/login`

#### Scenario: Authenticated non-admin is redirected from admin-only routes

- **WHEN** a user with role `agent` navigates to a route wrapped by the admin guard
- **THEN** the system SHALL redirect the user to `/dashboard`

#### Scenario: Authenticated admin can access admin-only routes

- **WHEN** a user with role `admin` navigates to a route wrapped by the admin guard
- **THEN** the system SHALL render the requested page
