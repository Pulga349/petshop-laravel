# Category Polymorphic Specification

## Purpose

Replace string-based category references with a dedicated categories table and polymorphic relations, enabling unified category management across products and suppliers.

## Requirements

### Requirement: Categories Table

The system MUST have a `categories` table with `name` and `description` fields. Products and suppliers MUST reference categories via a polymorphic relationship rather than string identifiers.

#### Scenario: Product references category

- GIVEN a category exists in the categories table
- WHEN a product is created with a category_id
- THEN the product MUST reference the category via a morphTo relationship
- AND the category MUST be retrievable through the product's category() relation

#### Scenario: Supplier references category

- GIVEN a category exists in the categories table
- WHEN a supplier is created with a category_id
- THEN the supplier MUST reference the category via a morphTo relationship
- AND the category MUST be retrievable through the supplier's category() relation

### Requirement: Category Migration

A migration MUST create the categories table and a morphable reference column on products and suppliers, replacing existing string-based category_id fields.

#### Scenario: Schema migration

- GIVEN the current string-based category_id fields on products and suppliers
- WHEN the migration runs
- THEN the string fields MUST be replaced with polymorphic category references
- AND existing data MUST be preserved or transformed without loss

### Requirement: Category Model Relations

The Category model MUST define hasMany relationships for products and suppliers using the polymorphic pattern.

#### Scenario: Category listing related items

- GIVEN a category with multiple products and suppliers
- WHEN accessing the category's products() or suppliers() relations
- THEN all related products and suppliers MUST be returned
