## ADDED Requirements

### Requirement: Properties list page

The system SHALL provide a page at `/properties` accessible to all authenticated users that lists all properties via `GET /properties`.

#### Scenario: User views properties list

- **WHEN** an authenticated user navigates to `/properties`
- **THEN** the system SHALL display all properties returned by `GET /properties`

### Requirement: Create a property

The system SHALL allow authenticated users to create a new property via `POST /properties`.

#### Scenario: User creates a property

- **WHEN** a user submits a valid create-property form
- **THEN** the system SHALL call `POST /properties` with the form data and add the new property to the list

### Requirement: Update a property

The system SHALL allow authenticated users to update an existing property via `PUT /properties/{id}`.

#### Scenario: User updates a property

- **WHEN** a user submits an edit-property form for an existing property
- **THEN** the system SHALL call `PUT /properties/{id}` with the updated data and reflect changes in the list

### Requirement: Delete a property

The system SHALL allow authenticated users to delete a property via `DELETE /properties/{id}`.

#### Scenario: User deletes a property

- **WHEN** a user confirms deletion of a property
- **THEN** the system SHALL call `DELETE /properties/{id}` and remove the property from the list

### Requirement: Add amenities to a property

The system SHALL allow authenticated users to add amenities to a property via `POST /properties/{propertyId}/amenities`.

#### Scenario: User adds an amenity

- **WHEN** a user submits the add-amenity form for a property
- **THEN** the system SHALL call `POST /properties/{propertyId}/amenities` and display the updated amenity list for that property

### Requirement: Update amenities of a property

The system SHALL allow authenticated users to update amenities on a property via `PUT /properties/{propertyId}/amenities`.

#### Scenario: User updates amenities

- **WHEN** a user submits updated amenity data for a property
- **THEN** the system SHALL call `PUT /properties/{propertyId}/amenities` and reflect the changes

### Requirement: Delete an amenity from a property

The system SHALL allow authenticated users to delete a specific amenity via `DELETE /properties/{propertyId}/amenities/{amenityId}`.

#### Scenario: User deletes an amenity

- **WHEN** a user confirms deletion of an amenity on a property
- **THEN** the system SHALL call `DELETE /properties/{propertyId}/amenities/{amenityId}` and remove the amenity from the display
