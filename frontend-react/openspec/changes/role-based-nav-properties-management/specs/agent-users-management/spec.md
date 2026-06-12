## ADDED Requirements

### Requirement: Admin-only Agent Users page

The system SHALL provide a page at `/agent-users` that lists all agent users and is accessible only to users with role `admin`.

#### Scenario: Admin can access Agent Users page

- **WHEN** an admin user navigates to `/agent-users`
- **THEN** the system SHALL display a list of agent users with their name, email, role, and active status

#### Scenario: Non-admin is redirected away

- **WHEN** a user with role `agent` attempts to navigate to `/agent-users`
- **THEN** the system SHALL redirect the user to `/dashboard`

### Requirement: Toggle user active status

The system SHALL allow admins to toggle the active status of an agent user via `PATCH /admin/users/{user}/toggle-status`.

#### Scenario: Admin toggles user status

- **WHEN** an admin clicks the toggle-status action for a user
- **THEN** the system SHALL call `PATCH /admin/users/{user}/toggle-status` and update the displayed status to reflect the new state

### Requirement: Force logout a user

The system SHALL allow admins to force-logout an agent user via `DELETE /admin/users/{user}/force-logout`.

#### Scenario: Admin force-logs out a user

- **WHEN** an admin clicks the force-logout action for a user and confirms
- **THEN** the system SHALL call `DELETE /admin/users/{user}/force-logout` and show a success notification
