## ADDED Requirements

### Requirement: Authenticated layout with sidebar navigation

The system SHALL render a persistent sidebar navigation for all authenticated pages that displays role-appropriate menu items.

#### Scenario: Admin sees both menu items

- **WHEN** a user with role `admin` is authenticated and views any page under the authenticated layout
- **THEN** the sidebar SHALL display "Agent Users" and "Properties" navigation links

#### Scenario: Agent sees only Properties

- **WHEN** a user with role `agent` is authenticated and views any page under the authenticated layout
- **THEN** the sidebar SHALL display only the "Properties" navigation link and SHALL NOT display "Agent Users"

#### Scenario: Active link is highlighted

- **WHEN** the user is on the `/properties` route
- **THEN** the "Properties" nav link SHALL be visually highlighted as active

#### Scenario: User info displayed in navigation

- **WHEN** an authenticated user views the layout
- **THEN** the navigation SHALL display the user's name and role
